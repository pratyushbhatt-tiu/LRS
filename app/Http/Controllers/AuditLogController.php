<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuditLogController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('subject')) {
            $query->where(function ($q) use ($request) {
                $q->where('auditable_type', 'like', '%' . $request->subject . '%')
                    ->orWhere('auditable_id', $request->subject);
            });
        }

        $logs = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = array_keys(config('constants.audit_events', []));

        return view('audit-logs.index', compact('logs', 'users', 'actions'));
    }
}
