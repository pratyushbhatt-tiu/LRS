<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class FeeRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'doc_type_id',
        'state_id',
        'county_id',
        'rule_name',
        'base_fee',
        'per_page_fee',
        'minimum_fee',
        'maximum_fee',
        'priority',
        'active',
        'effective_from',
        'effective_to',
    ];

    protected function casts(): array
    {
        return [
            'base_fee' => 'decimal:2',
            'per_page_fee' => 'decimal:2',
            'minimum_fee' => 'decimal:2',
            'maximum_fee' => 'decimal:2',
            'priority' => 'integer',
            'active' => 'boolean',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function docType(): BelongsTo
    {
        return $this->belongsTo(DocType::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function feeLines(): HasMany
    {
        return $this->hasMany(FeeLine::class);
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('active', true);
    }

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

    public function scopeOrderByPriority(Builder $query): void
    {
        $query->orderBy('priority', 'desc');
    }

    // Helper method for matching rules
    public function scopeMatching(Builder $query, $clientId, $docTypeId, $stateId, $countyId): void
    {
        $query->where(function ($q) use ($clientId, $docTypeId, $stateId, $countyId) {
            $q->where(function ($subQ) use ($clientId) {
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
