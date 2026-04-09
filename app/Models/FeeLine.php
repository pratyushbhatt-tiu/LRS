<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property float|null $total_amount
 */
class FeeLine extends Model
{
    protected $fillable = [
        'file_id',
        'fee_rule_id',
        'description',
        'quantity',
        'unit_price',
        'total_amount',
        'is_override',
        'override_reason',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'is_override' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function feeRule(): BelongsTo
    {
        return $this->belongsTo(FeeRule::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors
    protected function formattedTotalAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => '$' . number_format((float) $this->total_amount, 2)
        );
    }

    protected function formattedUnitPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => '$' . number_format((float) $this->unit_price, 2)
        );
    }

    // Helper methods
    public function isApproved(): bool
    {
        return !is_null($this->approved_at);
    }

    public function requiresApproval(): bool
    {
        return $this->is_override && !$this->isApproved();
    }

    // Auto-calculate total amount before saving, unless it's a manual override
    protected static function booted(): void
    {
        static::saving(function (FeeLine $feeLine) {
            if (!$feeLine->is_override) {
                $feeLine->total_amount = (float) ((float) $feeLine->quantity * (float) $feeLine->unit_price);
            }
        });
    }
}
