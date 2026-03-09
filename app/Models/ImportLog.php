<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Eloquent Model for tracking bulk CSV import jobs.
 * Records the outcome (success/failure counts, error details) of each import.
 */
class ImportLog extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'file_path',
        'status',
        'total_rows',
        'success_rows',
        'failed_rows',
        'errors',
    ];

    protected function casts(): array
    {
        return [
            'total_rows' => 'integer',
            'success_rows' => 'integer',
            'failed_rows' => 'integer',
        ];
    }

    // --- Relationships ---

    /**
     * The user who triggered this import.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- Helpers ---

    /**
     * Whether the import job is still running.
     */
    public function isProcessing(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Whether the import has finished (success or failure).
     */
    public function isComplete(): bool
    {
        return in_array($this->status, ['done', 'failed']);
    }

    /**
     * Decode the errors JSON column, returning an array.
     */
    public function getErrorsArray(): array
    {
        return json_decode($this->errors ?? '[]', true);
    }

    /**
     * A human-readable label for the import status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Queued',
            'processing' => 'Processing',
            'done' => 'Completed',
            'failed' => 'Failed',
            default => ucfirst($this->status),
        };
    }

    /**
     * Tailwind CSS badge classes for the status.
     */
    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-gray-100 text-gray-700',
            'processing' => 'bg-blue-100 text-blue-700',
            'done' => 'bg-green-100 text-green-700',
            'failed' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
