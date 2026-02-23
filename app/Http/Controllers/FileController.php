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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FileController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // Global middleware is handled in web.php
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', File::class);
        $query = File::with(['client', 'docType', 'state', 'county']);

        if ($request->filled('search')) {
            $query->where('file_no', 'like', '%' . $request->search . '%')
                ->orWhere('partner_ref_no', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $files = $query->latest()->paginate(15)->withQueryString();
        $clients = Client::where('active', true)->orderBy('name')->get();

        return view('files.index', compact('files', 'clients'));
    }

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

    public function store(StoreFileRequest $request)
    {
        $this->authorize('create', File::class);
        return DB::transaction(function () use ($request) {
            $file = File::create(array_merge($request->validated(), [
                'current_status' => config('constants.file_statuses.CHECK_IN'),
            ]));

            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => null,
                'to_status' => $file->current_status,
                'changed_by' => Auth::id(),
                'notes' => 'File created',
            ]);

            return redirect()->route('files.show', $file)
                ->with('success', 'File created successfully.');
        });
    }

    public function show(File $file)
    {
        $this->authorize('view', $file);
        $file->load(['client', 'docType', 'recordingPurpose', 'state', 'county', 'statusHistory.changedBy', 'feeLines.feeRule', 'attachments.uploader']);
        return view('files.show', compact('file'));
    }

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

    public function update(StoreFileRequest $request, File $file)
    {
        $this->authorize('update', $file);
        $file->update($request->validated());

        return redirect()->route('files.show', $file)
            ->with('success', 'File updated successfully.');
    }

    public function destroy(File $file)
    {
        $this->authorize('delete', $file);
        $file->delete();

        return redirect()->route('files.index')
            ->with('success', 'File deleted successfully.');
    }

    public function transition(Request $request, File $file)
    {
        $this->authorize('update', $file);

        $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = $request->status;

        if (!$file->canTransitionTo($newStatus)) {
            return back()->with('error', 'Invalid status transition.');
        }

        return DB::transaction(function () use ($file, $newStatus, $request) {
            $oldStatus = $file->current_status;
            $file->update(['current_status' => $newStatus]);

            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => $request->notes,
            ]);

            return redirect()->route('files.show', $file)
                ->with('success', "Status updated to {$newStatus}.");
        });
    }
}
