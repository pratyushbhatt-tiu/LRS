<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\County;
use App\Models\State;
use App\Http\Requests\Masters\StoreCountyRequest;
use App\Http\Requests\Masters\UpdateCountyRequest;
use Illuminate\Http\Request;

class CountyController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', County::class);
        $counties = County::query()
            ->with('state')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")->orWhere('code', 'like', "%{$request->search}%"))
            ->when($request->status === 'active', fn($q) => $q->where('active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('active', false))
            ->when($request->with_trashed, fn($q) => $q->withTrashed())
            ->withCount(['cities', 'files'])
            ->latest()->paginate(15)->withQueryString();
        return view('masters.counties.index', compact('counties'));
    }

    public function create()
    {
        $this->authorize('create', County::class);
        $states = State::where('active', true)->orderBy('name')->get();
        return view('masters.counties.create', compact('states'));
    }

    public function store(StoreCountyRequest $request)
    {
        $this->authorize('create', County::class);
        County::create($request->validated());
        return redirect()->route('masters.counties.index')->with('success', 'County created successfully.');
    }

    public function edit(County $county)
    {
        $this->authorize('update', $county);
        if ($county->trashed()) {
            $county = County::withTrashed()->findOrFail($county->id);
        }
        $states = State::where('active', true)->orderBy('name')->get();
        return view('masters.counties.edit', compact('county', 'states'));
    }

    public function update(UpdateCountyRequest $request, County $county)
    {
        $this->authorize('update', $county);
        $county->update($request->validated());
        return redirect()->route('masters.counties.index')->with('success', 'County updated successfully.');
    }

    public function destroy(County $county)
    {
        $this->authorize('delete', $county);
        $county->delete();
        return redirect()->route('masters.counties.index')->with('success', 'County deleted successfully.');
    }

    public function restore($id)
    {
        $county = County::withTrashed()->findOrFail($id);
        $this->authorize('restore', $county);
        $county->restore();
        return redirect()->route('masters.counties.index')->with('success', 'County restored successfully.');
    }

    public function toggleActive(County $county)
    {
        $this->authorize('update', $county);
        $county->update(['active' => !$county->active]);
        $status = $county->fresh()->active ? 'activated' : 'deactivated';
        return back()->with('success', "County {$status} successfully.");
    }
}
