<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Client;
use App\Models\DocType;
use App\Models\RecordingPurpose;
use App\Models\State;
use App\Models\County;
use App\Models\FileStatusHistory;
use App\Http\Requests\StoreFileRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Controller class for managing Recording Files.
 * This is the central hub for creating, tracking, and transitioning files
 * throughout the recording lifecycle (Check-in, QC, Accounting, etc.).
 */
class FileController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // Controller-specific middleware configuration
    }

    /**
     * Display a paginated listing of files with advanced filtering.
     * Allows searching by file numbers and filtering by status, client, and document type.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', File::class);

        // Eager load relationships to prevent N+1 query issues
        $query = File::with(['client', 'docType', 'state', 'county']);

        // Handle string-based search for file or reference numbers
        if ($request->filled('search')) {
            $query->where('file_no', 'like', '%' . $request->search . '%')
                ->orWhere('partner_ref_no', 'like', '%' . $request->search . '%');
        }

        // Apply attribute filters if provided in the request
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('doc_type_id')) {
            $query->where('doc_type_id', $request->doc_type_id);
        }

        $files = $query->latest()->paginate(15)->withQueryString();

        // Master data for filter dropdowns
        $clients = Client::where('active', true)->orderBy('name')->get();
        $docTypes = DocType::where('active', true)->orderBy('name')->get();

        return view('files.index', compact('files', 'clients', 'docTypes'));
    }

    /**
     * Show the form for creating a new file.
     */
    public function create()
    {
        $this->authorize('create', File::class);
        $clients = Client::where('active', true)->orderBy('name')->get();
        $docTypes = DocType::where('active', true)->orderBy('name')->get();
        $purposes = RecordingPurpose::where('active', true)->orderBy('name')->get();
        $states = State::where('active', true)->orderBy('name')->get();
        $counties = County::where('active', true)->orderBy('name')->get();

        return view('files.create', compact('clients', 'docTypes', 'purposes', 'states', 'counties'));
    }

    /**
     * Store a newly created file.
     * Uses a database transaction to ensure atomic generation of unique LRS and Reference numbers.
     */
    public function store(StoreFileRequest $request)
    {
        $this->authorize('create', File::class);

        return DB::transaction(function () use ($request) {
            $year = now()->year;
            $lrsPrefix = "LRS-{$year}-";
            $refPrefix = "REF-{$year}-";

            // Generate next LRS Sequence Number with pessimistic locking
            $lastFile = File::where('file_no', 'like', $lrsPrefix . '%')
                ->orderBy('file_no', 'desc')
                ->lockForUpdate()
                ->first();

            $newSequence = $lastFile
                ? str_pad((int) substr($lastFile->file_no, strlen($lrsPrefix)) + 1, 6, '0', STR_PAD_LEFT)
                : '000001';
            $fileNo = $lrsPrefix . $newSequence;

            // Generate next Partner Reference Sequence Number
            $lastRef = File::where('partner_ref_no', 'like', $refPrefix . '%')
                ->orderBy('partner_ref_no', 'desc')
                ->lockForUpdate()
                ->first();

            $newRefSequence = $lastRef
                ? str_pad((int) substr($lastRef->partner_ref_no, strlen($refPrefix)) + 1, 6, '0', STR_PAD_LEFT)
                : '000001';
            $partnerRefNo = $refPrefix . $newRefSequence;

            // Create the main file record
            $file = File::create(array_merge($request->validated(), [
                'file_no' => $fileNo,
                'partner_ref_no' => $partnerRefNo,
                'current_status' => config('constants.file_statuses.CHECK_IN'),
            ]));

            // Initial status history entry
            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => null,
                'to_status' => $file->current_status,
                'changed_by' => Auth::id(),
                'notes' => 'File checked in with system generated numbers',
            ]);

            // Log the creation event for auditing
            AuditService::log('FILE_CREATED', $file, [], $file->toArray());

            return redirect()->route('files.show', $file)
                ->with('success', "File {$fileNo} created with Reference {$partnerRefNo}.");
        });
    }

    /**
     * Display the specified file details.
     */
    public function show(File $file)
    {
        $this->authorize('view', $file);

        // Load all necessary relationships for a comprehensive view
        $file->load([
            'client',
            'docType',
            'recordingPurpose',
            'state',
            'county',
            'statusHistory.changedBy',
            'feeLines.feeRule',
            'attachments.uploader'
        ]);

        return view('files.show', compact('file'));
    }

    /**
     * Show the form for editing file details.
     */
    public function edit(File $file)
    {
        $this->authorize('update', $file);
        $clients = Client::where('active', true)->orderBy('name')->get();
        $docTypes = DocType::where('active', true)->orderBy('name')->get();
        $purposes = RecordingPurpose::where('active', true)->orderBy('name')->get();
        $states = State::where('active', true)->orderBy('name')->get();
        $counties = County::where('active', true)->orderBy('name')->get();

        return view('files.edit', compact('file', 'clients', 'docTypes', 'purposes', 'states', 'counties'));
    }

    /**
     * Update the file data.
     */
    public function update(StoreFileRequest $request, File $file)
    {
        $this->authorize('update', $file);

        $oldValues = $file->toArray(); // Snapshot before update
        $file->update($request->validated());

        // Audit the modification
        AuditService::log('FILE_UPDATED', $file, $oldValues, $file->fresh()->toArray());

        return redirect()->route('files.show', $file)
            ->with('success', 'File updated successfully.');
    }

    /**
     * Delete a file (Soft Delete).
     */
    public function destroy(File $file)
    {
        $this->authorize('delete', $file);

        $oldValues = $file->toArray();
        $file->delete();

        AuditService::log('FILE_DELETED', $file, $oldValues, []);

        return redirect()->route('files.index')
            ->with('success', 'File deleted successfully.');
    }

    /**
     * Handle workflow status transitions for a file.
     * Validates if the transition is allowed and records history.
     */
    public function transition(Request $request, File $file)
    {
        $this->authorize('update', $file);

        $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = $request->status;

        // Verify transition validity via model logic
        if (!$file->canTransitionTo($newStatus)) {
            return back()->with('error', 'Invalid status transition.');
        }

        return DB::transaction(function () use ($file, $newStatus, $request) {
            $oldStatus = $file->current_status;

            // Move file to the new status
            $file->update(['current_status' => $newStatus]);

            // Create record of the move
            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => $request->notes,
            ]);

            // Specialized audit log for status changes
            AuditService::log('STATUS_CHANGED', $file, [
                'status' => $oldStatus,
            ], [
                'status' => $newStatus,
                'notes' => $request->notes,
            ]);

            return redirect()->route('files.show', $file)
                ->with('success', "Status updated to {$newStatus}.");
        });
    }
}
