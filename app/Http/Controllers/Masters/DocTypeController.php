<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\DocType;
use App\Http\Requests\Masters\StoreDocTypeRequest;
use App\Http\Requests\Masters\UpdateDocTypeRequest;
use Illuminate\Http\Request;

class DocTypeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', DocType::class);

        $docTypes = DocType::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            })
            ->when($request->status === 'active', fn($q) => $q->where('active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('active', false))
            ->when($request->with_trashed, fn($q) => $q->withTrashed())
            ->withCount('files')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('masters.doc-types.index', compact('docTypes'));
    }

    public function create()
    {
        $this->authorize('create', DocType::class);
        return view('masters.doc-types.create');
    }

    public function store(StoreDocTypeRequest $request)
    {
        $this->authorize('create', DocType::class);
        DocType::create($request->validated());
        return redirect()->route('masters.doc-types.index')->with('success', 'Document Type created successfully.');
    }

    public function edit(DocType $docType)
    {
        $this->authorize('update', $docType);
        if ($docType->trashed()) {
            $docType = DocType::withTrashed()->findOrFail($docType->id);
        }
        return view('masters.doc-types.edit', compact('docType'));
    }

    public function update(UpdateDocTypeRequest $request, DocType $docType)
    {
        $this->authorize('update', $docType);
        $docType->update($request->validated());
        return redirect()->route('masters.doc-types.index')->with('success', 'Document Type updated successfully.');
    }

    public function destroy(DocType $docType)
    {
        $this->authorize('delete', $docType);
        $docType->delete();
        return redirect()->route('masters.doc-types.index')->with('success', 'Document Type deleted successfully.');
    }

    public function restore($id)
    {
        $docType = DocType::withTrashed()->findOrFail($id);
        $this->authorize('restore', $docType);
        $docType->restore();
        return redirect()->route('masters.doc-types.index')->with('success', 'Document Type restored successfully.');
    }

    public function toggleActive(DocType $docType)
    {
        $this->authorize('update', $docType);
        $docType->update(['active' => !$docType->active]);
        $status = $docType->fresh()->active ? 'activated' : 'deactivated';
        return back()->with('success', "Document Type {$status} successfully.");
    }
}
