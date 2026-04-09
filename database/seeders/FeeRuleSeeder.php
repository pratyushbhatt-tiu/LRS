<?php

namespace Database\Seeders;

use App\Models\FeeRule;
use App\Models\Client;
use App\Models\DocType;
use App\Models\State;
use App\Models\County;
;
use Illuminate\Database\Seeder;

class FeeRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Standard Rule for California (Realistic: $25 Base + $3/Page + $5 Tech Surcharge)
        $ca = State::where('code', 'CA')->first();
        if ($ca) {
            FeeRule::create([
                'rule_name' => 'CA Standard Deed Processing',
                'state_id' => $ca->id,
                'doc_type_id' => DocType::where('code', 'DEED')->first()?->id,
                'base_fee' => 25.00,
                'per_page_fee' => 3.00,
                'surcharge' => 5.00,
                'priority' => 10,
                'active' => true,
            ]);
        }

        // 2. Premium Rule for New York Mortgage (Realistic: $45 Base + $5/Page + $15 Jurisdiction Surcharge)
        $ny = State::where('code', 'NY')->first();
        if ($ny) {
            FeeRule::create([
                'rule_name' => 'NY Mortgage High-Value Audit',
                'state_id' => $ny->id,
                'doc_type_id' => DocType::where('code', 'MORTGAGE')->first()?->id,
                'base_fee' => 45.00,
                'per_page_fee' => 5.00,
                'surcharge' => 15.00,
                'priority' => 20,
                'active' => true,
            ]);
        }

        // 3. Fallback Universal Rule (Realistic: $15 Base + $2/Page)
        FeeRule::create([
            'rule_name' => 'Universal Minimum Billing',
            'base_fee' => 15.00,
            'per_page_fee' => 2.00,
            'surcharge' => 0.00,
            'priority' => 1, // Lowest priority, matches everything if others fail
            'active' => true,
        ]);
    }
}
