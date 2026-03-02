<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Write an immutable audit entry.
     *
     * @param  string       $action   One of the keys from config('constants.audit_events')
     * @param  Model|null   $subject  The model being acted on (polymorphic)
     * @param  array        $old      Snapshot of attributes BEFORE the change
     * @param  array        $new      Snapshot of attributes AFTER the change
     */
    public static function log(
        string $action,
        ?Model $subject = null,
        array $old = [],
        array $new = []
    ): void {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $subject ? get_class($subject) : null,
            'auditable_id' => $subject?->getKey(),
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
        ]);
    }
}
