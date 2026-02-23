<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Http\Requests\Masters\StoreClientRequest;
use App\Http\Requests\Masters\UpdateClientRequest;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::query()
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

        return view('masters.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        $this->authorize('create', Client::class);
        return view('masters.clients.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $this->authorize('create', Client::class);

        Client::create($request->validated());

        return redirect()
            ->route('masters.clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        // Load with trashed to allow editing soft-deleted records
        if ($client->trashed()) {
            $client = Client::withTrashed()->findOrFail($client->id);
        }

        return view('masters.clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return redirect()
            ->route('masters.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified client from storage (soft delete).
     */
    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return redirect()
            ->route('masters.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Restore a soft-deleted client.
     */
    public function restore($id)
    {
        $client = Client::withTrashed()->findOrFail($id);
        $this->authorize('restore', $client);

        $client->restore();

        return redirect()
            ->route('masters.clients.index')
            ->with('success', 'Client restored successfully.');
    }

    /**
     * Toggle the active status of the specified client.
     */
    public function toggleActive(Client $client)
    {
        $this->authorize('update', $client);
        $client->update(['active' => !$client->active]);
        $status = $client->fresh()->active ? 'activated' : 'deactivated';
        return back()->with('success', "Client {$status} successfully.");
    }
}
