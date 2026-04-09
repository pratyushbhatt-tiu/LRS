<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileStatusHistory;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecordingController extends Controller
{
    /**
     * Display a listing of files awaiting recording details.
     */
    public function index()
    {
        $recordingStatus = config('constants.file_statuses.RECORDING');

        $pendingFiles = File::with(['client', 'state', 'county', 'docType'])
            ->where('current_status', $recordingStatus)
            ->orderBy('updated_at', 'desc')
            ->get();

        $stats = [
            'pending' => $pendingFiles->count(),
            'recorded_today' => File::where('current_status', config('constants.file_statuses.RETURN'))
                ->whereDate('recorded_at', today())
                ->count(),
        ];

        return view('recording.index', compact('pendingFiles', 'stats'));
    }

    /**
     * Show the form for entering recording data for a specific file.
     */
    public function show(File $file)
    {
        if ($file->current_status !== config('constants.file_statuses.RECORDING')) {
            return redirect()->route('recording.index')->with('error', 'File is not in Recording status.');
        }

        return view('recording.show', compact('file'));
    }

    /**
     * Finalize recording data and transition to Return stage.
     */
    public function record(Request $request, File $file)
    {
        $request->validate([
            'instrument_no' => 'required_without:book|nullable|string|max:100',
            'book' => 'required_without:instrument_no|nullable|string|max:50',
            'page' => 'required_with:book|nullable|string|max:50',
            'recorded_at' => 'required|date|before_or_equal:today',
            'recording_fee' => 'nullable|numeric|min:0',
        ], [
            'instrument_no.required_without' => 'Either Instrument Number or Book/Page is required.',
            'book.required_without' => 'Either Book/Page or Instrument Number is required.',
        ]);

        return DB::transaction(function () use ($file, $request) {
            $oldStatus = $file->current_status;
            $newStatus = config('constants.file_statuses.RETURN');

            $file->update([
                'current_status' => $newStatus,
                'instrument_no' => $request->instrument_no,
                'book' => $request->book,
                'page' => $request->page,
                'recorded_at' => $request->recorded_at,
                'recording_fee' => $request->recording_fee,
            ]);

            // Create status history
            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => "Recording Confirmed. Inst#: {$request->instrument_no} | Book: {$request->book} | Page: {$request->page}"
            ]);

            // Audit Log
            AuditService::log('STATUS_CHANGED', $file, [
                'status' => $oldStatus,
            ], [
                'status' => $newStatus,
                'instrument_no' => $request->instrument_no,
                'recorded_at' => $request->recorded_at,
            ]);

            return redirect()->route('recording.index')
                ->with('success', "Legal recording details saved for File #{$file->file_no}. File moved to Return stage.");
        });
    }
}
