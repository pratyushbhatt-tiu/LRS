<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileStatusHistory;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    /**
     * Display a listing of files awaiting final return to partner.
     */
    public function index()
    {
        $returnStatus = config('constants.file_statuses.RETURN');

        $pendingFiles = File::with(['client', 'state', 'county', 'docType'])
            ->where('current_status', $returnStatus)
            ->orderBy('updated_at', 'desc')
            ->get();

        $stats = [
            'pending' => $pendingFiles->count(),
            'closed_today' => File::where('current_status', config('constants.file_statuses.CLOSED'))
                ->whereDate('returned_at', today())
                ->count(),
        ];

        return view('returns.index', compact('pendingFiles', 'stats'));
    }

    /**
     * Show the form for entering return tracking for a specific file.
     */
    public function show(File $file)
    {
        if ($file->current_status !== config('constants.file_statuses.RETURN')) {
            return redirect()->route('returns.index')->with('error', 'File is not in Return status.');
        }

        return view('returns.show', compact('file'));
    }

    /**
     * Finalize return dispatch and close the file.
     */
    public function return(Request $request, File $file)
    {
        $request->validate([
            'return_courier' => 'required|string|max:100',
            'return_tracking_no' => 'required_unless:return_courier,Hand Delivered|nullable|string|max:100',
            'returned_at' => 'required|date|before_or_equal:today',
            'return_notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($file, $request) {
            $oldStatus = $file->current_status;
            $newStatus = config('constants.file_statuses.CLOSED');

            $file->update([
                'current_status' => $newStatus,
                'return_courier' => $request->return_courier,
                'return_tracking_no' => $request->return_tracking_no,
                'returned_at' => $request->returned_at,
                'return_notes' => $request->return_notes,
            ]);

            // Create status history
            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => "File Returned to Partner via {$request->return_courier}. Tracking: {$request->return_tracking_no}. File Closed."
            ]);

            // Audit Log
            AuditService::log('STATUS_CHANGED', $file, [
                'status' => $oldStatus,
            ], [
                'status' => $newStatus,
                'return_courier' => $request->return_courier,
                'returned_at' => $request->returned_at,
            ]);

            return redirect()->route('returns.index')
                ->with('success', "File #{$file->file_no} has been returned and closed successfully.");
        });
    }
}
