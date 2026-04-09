<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileStatusHistory;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class QCController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the QC Dashboard with summary stats.
     */
    public function index()
    {
        // Stats for the QC dashboard
        $stats = [
            'pending' => File::where('current_status', config('constants.file_statuses.QC'))->count(),
            'passed_today' => FileStatusHistory::where('to_status', config('constants.file_statuses.ACCOUNTING'))
                ->whereDate('created_at', now()->toDateString())
                ->count(),
            'failed_today' => FileStatusHistory::where('to_status', config('constants.file_statuses.CHECK_IN'))
                ->where('from_status', config('constants.file_statuses.QC'))
                ->whereDate('created_at', now()->toDateString())
                ->count(),
        ];

        // Recent QC Activity
        $recentActivity = FileStatusHistory::with(['file', 'changedBy'])
            ->whereIn('to_status', [config('constants.file_statuses.ACCOUNTING'), config('constants.file_statuses.CHECK_IN')])
            ->where('from_status', config('constants.file_statuses.QC'))
            ->latest()
            ->take(5)
            ->get();

        return view('qc.index', compact('stats', 'recentActivity'));
    }

    /**
     * Display a list of files pending QC review.
     */
    public function pending(Request $request)
    {
        $query = File::with(['client', 'docType', 'state', 'county'])
            ->where('current_status', config('constants.file_statuses.QC'));

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('file_no', 'like', '%' . $request->search . '%')
                    ->orWhere('partner_ref_no', 'like', '%' . $request->search . '%');
            });
        }

        $files = $query->latest()->paginate(15)->withQueryString();

        return view('qc.pending', compact('files'));
    }

    /**
     * Show the review page for a specific file.
     */
    public function show(File $file)
    {
        // Only allow review if the file is in QC status
        if ($file->current_status !== config('constants.file_statuses.QC')) {
            return redirect()->route('qc.pending')->with('error', 'File is not in QC status.');
        }

        $file->load([
            'client',
            'docType',
            'recordingPurpose',
            'state',
            'county',
            'statusHistory.changedBy',
            'attachments.uploader'
        ]);

        return view('qc.show', compact('file'));
    }

    /**
     * Pass a file through QC to Accounting.
     */
    public function pass(Request $request, File $file)
    {
        if ($file->current_status !== config('constants.file_statuses.QC')) {
            return back()->with('error', 'Invalid file status.');
        }

        return DB::transaction(function () use ($file, $request) {
            $oldStatus = $file->current_status;
            $newStatus = config('constants.file_statuses.ACCOUNTING');

            $file->update(['current_status' => $newStatus]);

            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => $request->notes ?? 'QC Passed',
            ]);

            AuditService::log('QC_PERFORMED', $file, [
                'status' => $oldStatus,
                'result' => 'PASS',
            ], [
                'status' => $newStatus,
                'notes' => $request->notes ?? 'QC Passed',
            ]);

            return redirect()->route('qc.pending')
                ->with('success', "File {$file->file_no} passed to Accounting.");
        });
    }

    /**
     * Fail a file and return to Check-in.
     */
    public function fail(Request $request, File $file)
    {
        $request->validate([
            'notes' => 'required|string|min:5|max:1000',
        ]);

        if ($file->current_status !== config('constants.file_statuses.QC')) {
            return back()->with('error', 'Invalid file status.');
        }

        return DB::transaction(function () use ($file, $request) {
            $oldStatus = $file->current_status;
            $newStatus = config('constants.file_statuses.CHECK_IN');

            $file->update(['current_status' => $newStatus]);

            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => $request->notes,
            ]);

            AuditService::log('QC_PERFORMED', $file, [
                'status' => $oldStatus,
                'result' => 'FAIL',
            ], [
                'status' => $newStatus,
                'notes' => $request->notes,
            ]);

            return redirect()->route('qc.pending')
                ->with('warning', "File {$file->file_no} returned to Check-in with notes.");
        });
    }
}
