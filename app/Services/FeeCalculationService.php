<?php

namespace App\Services;

use App\Models\File;
use App\Models\FeeLine;
use App\Models\FeeRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service to handle dynamic fee calculations for files based on configured rules.
 * This ensures financial data is generated with proper business logic including
 * page-based pricing, surcharges, and rule-specific caps.
 *
 * Fee calculation is triggered:
 *  - On file creation (manual or bulk import)
 *  - Via manual "Recalculate" action on the Accounting page
 *  - When page count is updated
 */
class FeeCalculationService
{
    /**
     * Calculate and sync fee lines for a specific file.
     * Clears existing auto-generated fee lines and re-creates them
     * from the current set of active, matching fee rules.
     * Manual override lines (is_override = true) are always preserved.
     *
     * @param  File  $file
     * @return array  Summary of what was calculated: ['rules_matched', 'lines_created', 'total_amount']
     */
    public static function calculate(File $file): array
    {
        $summary = [
            'rules_matched' => 0,
            'lines_created' => 0,
            'total_amount'  => 0.00,
        ];

        DB::transaction(function () use ($file, &$summary) {
            // 1. Find all active and matching rules for this file
            $rules = FeeRule::active()
                ->effective()
                ->matching(
                    $file->client_id,
                    $file->doc_type_id,
                    $file->state_id,
                    $file->county_id
                )
                ->orderByPriority()
                ->get();

            $summary['rules_matched'] = $rules->count();

            // 2. Clear existing non-override (auto-generated) fee lines
            //    Manual overrides are always preserved
            $file->feeLines()->where('is_override', false)->delete();

            // 3. Process each matching rule and create fee lines
            foreach ($rules as $index => $rule) {
                $isPrimary = ($index === 0);
                $pageCount = $file->page_count ?? 1;
                $ruleTotalAmount = 0.00;

                // --- Case A: Base Fee Line ---
                if ($rule->base_fee > 0) {
                    $description = $isPrimary
                        ? ($rule->rule_name ?: "Base Processing Fee")
                        : ($rule->rule_name ?: "Additional Rule: Base Fee");

                    $lineTotal = (float) $rule->base_fee;

                    $file->feeLines()->create([
                        'fee_rule_id'  => $rule->id,
                        'description'  => $description,
                        'quantity'     => 1.00,
                        'unit_price'   => $rule->base_fee,
                        'total_amount' => $lineTotal,
                        'is_override'  => false,
                    ]);

                    $ruleTotalAmount += $lineTotal;
                    $summary['lines_created']++;
                }

                // --- Case B: Per-Page Fee Line ---
                if ($rule->per_page_fee > 0) {
                    $description = "Recording Fee ({$pageCount} Pages)";
                    $lineTotal = (float) ($pageCount * $rule->per_page_fee);

                    $file->feeLines()->create([
                        'fee_rule_id'  => $rule->id,
                        'description'  => $description,
                        'quantity'     => (float) $pageCount,
                        'unit_price'   => $rule->per_page_fee,
                        'total_amount' => $lineTotal,
                        'is_override'  => false,
                    ]);

                    $ruleTotalAmount += $lineTotal;
                    $summary['lines_created']++;
                }

                // --- Case C: Surcharge Line ---
                if ($rule->surcharge > 0) {
                    $description = "Jurisdiction Surcharge";
                    $lineTotal = (float) $rule->surcharge;

                    $file->feeLines()->create([
                        'fee_rule_id'  => $rule->id,
                        'description'  => $description,
                        'quantity'     => 1.00,
                        'unit_price'   => $rule->surcharge,
                        'total_amount' => $lineTotal,
                        'is_override'  => false,
                    ]);

                    $ruleTotalAmount += $lineTotal;
                    $summary['lines_created']++;
                }

                // --- Apply minimum/maximum fee caps at the rule level ---
                // If total for this rule falls below minimum or above maximum,
                // adjust the last created line to compensate
                if ($rule->minimum_fee && $ruleTotalAmount < (float) $rule->minimum_fee) {
                    $adjustment = (float) $rule->minimum_fee - $ruleTotalAmount;
                    $file->feeLines()->create([
                        'fee_rule_id'  => $rule->id,
                        'description'  => 'Minimum Fee Adjustment',
                        'quantity'     => 1.00,
                        'unit_price'   => $adjustment,
                        'total_amount' => $adjustment,
                        'is_override'  => false,
                    ]);
                    $ruleTotalAmount = (float) $rule->minimum_fee;
                    $summary['lines_created']++;
                }

                if ($rule->maximum_fee && $ruleTotalAmount > (float) $rule->maximum_fee) {
                    $discount = $ruleTotalAmount - (float) $rule->maximum_fee;
                    $file->feeLines()->create([
                        'fee_rule_id'  => $rule->id,
                        'description'  => 'Maximum Fee Cap Discount',
                        'quantity'     => 1.00,
                        'unit_price'   => -$discount,
                        'total_amount' => -$discount,
                        'is_override'  => false,
                    ]);
                    $ruleTotalAmount = (float) $rule->maximum_fee;
                    $summary['lines_created']++;
                }

                $summary['total_amount'] += $ruleTotalAmount;
            }

            // Include any existing manual override totals
            $overrideTotal = $file->feeLines()->where('is_override', true)->sum('total_amount');
            $summary['total_amount'] += (float) $overrideTotal;
        });

        // Log the calculation for debugging/audit purposes
        Log::info("Fee calculation completed for File #{$file->file_no}", $summary);

        return $summary;
    }
}
