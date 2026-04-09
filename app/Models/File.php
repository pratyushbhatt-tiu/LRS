<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class File extends Model
{
    protected $fillable = [
        'file_no',
        'client_id',
        'doc_type_id',
        'recording_purpose_id',
        'state_id',
        'county_id',
        'partner_ref_no',
        'received_date',
        'page_count',
        'current_status',
        'courier',
        'tracking_number',
        'shipped_at',
        'shipping_notes',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'shipped_at' => 'date',
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

    public function recordingPurpose(): BelongsTo
    {
        return $this->belongsTo(RecordingPurpose::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(FileStatusHistory::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function feeLines(): HasMany
    {
        return $this->hasMany(FeeLine::class);
    }

    // Scopes
    public function scopeWithStatus(Builder $query, string $status): void
    {
        $query->where('current_status', $status);
    }

    public function scopeReceivedBetween(Builder $query, $startDate, $endDate): void
    {
        $query->whereBetween('received_date', [$startDate, $endDate]);
    }

    // Accessors
    protected function totalFees(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->feeLines()->sum('total_amount')
        );
    }

    // Helper methods
    public function canTransitionTo(string $newStatus): bool
    {
        $allowedTransitions = config('constants.status_transitions');
        return in_array($newStatus, $allowedTransitions[$this->current_status] ?? []);
    }

    public function getStatusConfig(): array
    {
        return config("constants.status_config.{$this->current_status}", []);
    }
}
