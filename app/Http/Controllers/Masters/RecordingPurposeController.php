<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\RecordingPurpose;
use App\Http\Requests\Masters\StoreRecordingPurposeRequest;
use App\Http\Requests\Masters\UpdateRecordingPurposeRequest;
use Illuminate\Http\Request;

class RecordingPurposeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', RecordingPurpose::class);
        $recordingPurposes = RecordingPurpose::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")->orWhere('code', 'like', "%{$request->search}%"))
            ->when($request->status === 'active', fn($q) => $q->where('active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('active', false))
            ->when($request->with_trashed, fn($q) => $q->withTrashed())
            ->withCount('files')
            ->latest()->paginate(15)->withQueryString();
        return view('masters.recording-purposes.index', compact('recordingPurposes'));
    }

    public function create()
    {
        $this->authorize('create', RecordingPurpose::class);
        return view('masters.recording-purposes.create');
    }

    public function store(StoreRecordingPurposeRequest $request)
    {
        $this->authorize('create', RecordingPurpose::class);
        RecordingPurpose::create($request->validated());
        return redirect()->route('masters.recording-purposes.index')->with('success', 'Recording Purpose created successfully.');
    }

    public function edit(RecordingPurpose $recordingPurpose)
    {
        $this->authorize('update', $recordingPurpose);
        if ($recordingPurpose->trashed()) {
            $recordingPurpose = RecordingPurpose::withTrashed()->findOrFail($recordingPurpose->id);
        }
        return view('masters.recording-purposes.edit', compact('recordingPurpose'));
    }

    public function update(UpdateRecordingPurposeRequest $request, RecordingPurpose $recordingPurpose)
    {
        $this->authorize('update', $recordingPurpose);
        $recordingPurpose->update($request->validated());
        return redirect()->route('masters.recording-purposes.index')->with('success', 'Recording Purpose updated successfully.');
    }

    public function destroy(RecordingPurpose $recordingPurpose)
    {
        $this->authorize('delete', $recordingPurpose);
        $recordingPurpose->delete();
        return redirect()->route('masters.recording-purposes.index')->with('success', 'Recording Purpose deleted successfully.');
    }

    public function restore($id)
    {
        $recordingPurpose = RecordingPurpose::withTrashed()->findOrFail($id);
        $this->authorize('restore', $recordingPurpose);
        $recordingPurpose->restore();
        return redirect()->route('masters.recording-purposes.index')->with('success', 'Recording Purpose restored successfully.');
    }

    public function toggleActive(RecordingPurpose $recordingPurpose)
    {
        $this->authorize('update', $recordingPurpose);
        $recordingPurpose->update(['active' => !$recordingPurpose->active]);
        $status = $recordingPurpose->fresh()->active ? 'activated' : 'deactivated';
        return back()->with('success', "Recording Purpose {$status} successfully.");
    }
}
