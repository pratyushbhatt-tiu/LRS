<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Service class for handling application audit logs.
 * Provides a standardized way to record user actions and data changes.
 */
class AuditService
{
    /**
     * Write an immutable audit entry to the database.
     * Captures the current user, action performed, and optional before/after snapshots.
     *
     * @param  string       $action   Identifier for the event being logged (e.g., 'FILE_CREATED')
     * @param  Model|null   $subject  The Eloquent model being audited (uses polymorphism)
     * @param  array        $old      Data snapshot BEFORE the operation
     * @param  array        $new      Data snapshot AFTER the operation
     */
    public static function log(
        string $action,
        ?Model $subject = null,
        array $old = [],
        array $new = []
    ): void {
        // Create an entry in the audit_logs table
        AuditLog::create([
            'user_id' => Auth::id(), // ID of the currently authenticated user
            'action' => $action,
            'auditable_type' => $subject ? get_class($subject) : null, // Class name of the subject
            'auditable_id' => $subject?->getKey(), // Primary key of the subject
            'old_values' => $old ?: null, // Store as JSON null if empty
            'new_values' => $new ?: null, // Store as JSON null if empty
        ]);
    }
}
