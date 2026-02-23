<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Http\Requests\Masters\StoreStateRequest;
use App\Http\Requests\Masters\UpdateStateRequest;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', State::class);
        $states = State::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")->orWhere('code', 'like', "%{$request->search}%"))
            ->when($request->status === 'active', fn($q) => $q->where('active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('active', false))
            ->when($request->with_trashed, fn($q) => $q->withTrashed())
            ->withCount(['counties', 'files'])
            ->latest()->paginate(15)->withQueryString();
        return view('masters.states.index', compact('states'));
    }

    public function create()
    {
        $this->authorize('create', State::class);
        return view('masters.states.create');
    }

    public function store(StoreStateRequest $request)
    {
        $this->authorize('create', State::class);
        State::create($request->validated());
        return redirect()->route('masters.states.index')->with('success', 'State created successfully.');
    }

    public function edit(State $state)
    {
        $this->authorize('update', $state);
        if ($state->trashed()) {
            $state = State::withTrashed()->findOrFail($state->id);
        }
        return view('masters.states.edit', compact('state'));
    }

    public function update(UpdateStateRequest $request, State $state)
    {
        $this->authorize('update', $state);
        $state->update($request->validated());
        return redirect()->route('masters.states.index')->with('success', 'State updated successfully.');
    }

    public function destroy(State $state)
    {
        $this->authorize('delete', $state);
        $state->delete();
        return redirect()->route('masters.states.index')->with('success', 'State deleted successfully.');
    }

    public function restore($id)
    {
        $state = State::withTrashed()->findOrFail($id);
        $this->authorize('restore', $state);
        $state->restore();
        return redirect()->route('masters.states.index')->with('success', 'State restored successfully.');
    }

    public function toggleActive(State $state)
    {
        $this->authorize('update', $state);
        $state->update(['active' => !$state->active]);
        $status = $state->fresh()->active ? 'activated' : 'deactivated';
        return back()->with('success', "State {$status} successfully.");
    }
}
