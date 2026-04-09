<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Eloquent Model for Fee Rules.
 * Defines how fees are calculated for files based on specific matching criteria.
 */
class FeeRule extends Model
{
    use SoftDeletes; // Enables soft deletion to preserve history

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'client_id',
        'doc_type_id',
        'state_id',
        'county_id',
        'rule_name',
        'base_fee',
        'per_page_fee',
        'surcharge',
        'minimum_fee',
        'maximum_fee',
        'priority',
        'active',
        'effective_from',
        'effective_to',
    ];

    /**
     * Get the attributes that should be cast to specialized types.
     */
    protected function casts(): array
    {
        return [
            'base_fee' => 'decimal:2',
            'per_page_fee' => 'decimal:2',
            'surcharge' => 'decimal:2',
            'minimum_fee' => 'decimal:2',
            'maximum_fee' => 'decimal:2',
            'priority' => 'integer',
            'active' => 'boolean',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    // --- Relationships ---

    /**
     * Get the client this rule applies to. If null, applies to all clients.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the document type this rule applies to.
     */
    public function docType(): BelongsTo
    {
        return $this->belongsTo(DocType::class);
    }

    /**
     * Get the state this rule applies to.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the county this rule applies to.
     */
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    /**
     * Get the individual fee line items associated with this rule.
     */
    public function feeLines(): HasMany
    {
        return $this->hasMany(FeeLine::class);
    }

    // --- Scopes ---

    /**
     * Scope a query to only include active rules.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', true);
    }

    /**
     * Scope a query to include rules effective for a specific date.
     * Checks if the date falls within the effective_from and effective_to range.
     */
    public function scopeEffective(Builder $query, $date = null): void
    {
        $date = $date ?? now();
        $query->where(function ($q) use ($date) {
            $q->whereNull('effective_from')
                ->orWhere('effective_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('effective_to')
                ->orWhere('effective_to', '>=', $date);
        });
    }

    /**
     * Sort rules by priority. Lower numerical values usually have higher precedence.
     */
    public function scopeOrderByPriority(Builder $query): void
    {
        $query->orderBy('priority', 'desc');
    }

    /**
     * Complex scope for matching rules against specific file properties.
     * Handles 'fallback' logic where a null value in a rule column matches ANY input.
     */
    public function scopeMatching(Builder $query, $clientId, $docTypeId, $stateId, $countyId): void
    {
        $query->where(function ($q) use ($clientId, $docTypeId, $stateId, $countyId) {
            $q->where(function ($subQ) use ($clientId) {
                // Match either the specific client or a universal rule (null client)
                $subQ->whereNull('client_id')->orWhere('client_id', $clientId);
            })->where(function ($subQ) use ($docTypeId) {
                $subQ->whereNull('doc_type_id')->orWhere('doc_type_id', $docTypeId);
            })->where(function ($subQ) use ($stateId) {
                $subQ->whereNull('state_id')->orWhere('state_id', $stateId);
            })->where(function ($subQ) use ($countyId) {
                $subQ->whereNull('county_id')->orWhere('county_id', $countyId);
            });
        });
    }
}
