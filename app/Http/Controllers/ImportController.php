<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImportRequest;
use App\Jobs\ImportFilesJob;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Handles the CSV bulk import workflow for files.
 * Supports: template download, CSV upload, import log viewing, and error CSV download.
 */
class ImportController extends Controller
{
    /**
     * Show the bulk import landing page.
     */
    public function index()
    {
        // Fetch recent import logs for the current user
        $recentImports = ImportLog::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        return view('files.import', compact('recentImports'));
    }

    /**
     * Download a blank CSV template for bulk import.
     * The template contains headers that match the expected import columns.
     */
    public function downloadTemplate()
    {
        $headers = [
            'client_code',
            'received_date',   // Format: YYYY-MM-DD
            'doc_type_code',
            'recording_purpose_code',
            'state_code',
            'county_name',
        ];

        // Build CSV content: header row + one example row
        $csvContent = implode(',', $headers) . "\n";
        $csvContent .= 'CLIENT001,2026-03-09,DEED,STANDARD,CA,Los Angeles' . "\n";

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="lrs_import_template.csv"',
        ]);
    }

    /**
     * Handle CSV file upload: validate the file, persist it to disk,
     * create an ImportLog record, and dispatch the background import job.
     */
    public function upload(StoreImportRequest $request)
    {
        // Store the uploaded file in a private disk under imports/
        $path = $request->file('csv_file')->store('imports', 'local');

        // Create an import log record to track the job's progress
        $importLog = ImportLog::create([
            'user_id' => Auth::id(),
            'filename' => $request->file('csv_file')->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending',
            'total_rows' => 0,
            'success_rows' => 0,
            'failed_rows' => 0,
            'errors' => null,
        ]);

        // Dispatch the import job to run in the background
        ImportFilesJob::dispatch($importLog->id, Auth::id());

        return redirect()
            ->route('files.import.show', $importLog)
            ->with('success', 'Your CSV has been uploaded and is being processed. Refresh to see progress.');
    }

    /**
     * Show the result/summary of a specific import job.
     */
    public function show(ImportLog $importLog)
    {
        // Only allow the uploader or Admin to view the log
        if ($importLog->user_id !== Auth::id() && !Auth::user()->hasRole('Admin')) {
            abort(403);
        }

        return view('files.import-show', compact('importLog'));
    }

    /**
     * Download the error CSV for a completed import that had failed rows.
     * The error file contains all failed rows with an appended 'error' column.
     */
    public function downloadErrors(ImportLog $importLog)
    {
        if ($importLog->user_id !== Auth::id() && !Auth::user()->hasRole('Admin')) {
            abort(403);
        }

        if (empty($importLog->errors)) {
            return back()->with('error', 'No errors to download for this import.');
        }

        // Build error CSV from the stored errors JSON
        $errors = json_decode($importLog->errors, true);
        $csvContent = "row,client_code,received_date,doc_type_code,recording_purpose_code,state_code,county_name,error\n";

        foreach ($errors as $error) {
            $row = array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $error);
            $csvContent .= implode(',', $row) . "\n";
        }

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="import_errors_' . $importLog->id . '.csv"',
        ]);
    }
}
