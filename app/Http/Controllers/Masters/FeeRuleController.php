<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\FeeRule;
use App\Models\Client;
use App\Models\DocType;
use App\Models\State;
use App\Models\County;
use App\Http\Requests\Masters\StoreFeeRuleRequest;
use App\Http\Requests\Masters\UpdateFeeRuleRequest;
use Illuminate\Http\Request;

/**
 * Controller class for managing Fee Rules.
 * Handles the logic for defining complex fee structures based on
 * dynamic criteria like client, document type, and geography.
 */
class FeeRuleController extends Controller
{
    /**
     * Display a listing of the available fee rules.
     * Supports filtering by name (search), status (active/inactive),
     * and viewing trashed rules for restoration.
     */
    public function index(Request $request)
    {
        // Enforce authorization for viewing rules
        $this->authorize('viewAny', FeeRule::class);

        // Build the query with essential relationships
        $feeRules = FeeRule::query()
            ->with(['client', 'docType', 'state', 'county'])
            ->when($request->search, fn($q) => $q->where('rule_name', 'like', "%{$request->search}%")) // Name filter
            ->when($request->status === 'active', fn($q) => $q->where('active', true)) // Active filter
            ->when($request->status === 'inactive', fn($q) => $q->where('active', false)) // Inactive filter
            ->when($request->with_trashed, fn($q) => $q->withTrashed()) // Include soft-deleted rules
            ->orderBy('priority') // Rules with lower priority values are evaluated first
            ->latest()
            ->paginate(15)->withQueryString();

        return view('masters.fee-rules.index', compact('feeRules'));
    }

    /**
     * Show the form for creating a new fee rule.
     * Pre-loads necessary master data for selection.
     */
    public function create()
    {
        $this->authorize('create', FeeRule::class);

        // Fetch master entities to populate dropdown selections
        $clients = Client::where('active', true)->orderBy('name')->get();
        $docTypes = DocType::where('active', true)->orderBy('name')->get();
        $states = State::where('active', true)->orderBy('name')->get();
        $counties = County::where('active', true)->orderBy('name')->get();

        return view('masters.fee-rules.create', compact('clients', 'docTypes', 'states', 'counties'));
    }

    /**
     * Store a newly created fee rule in storage.
     * Validation is handled by the StoreFeeRuleRequest.
     */
    public function store(StoreFeeRuleRequest $request)
    {
        $this->authorize('create', FeeRule::class);
        FeeRule::create($request->validated()); // Persistence logic
        return redirect()->route('masters.fee-rules.index')->with('success', 'Fee Rule created successfully.');
    }

    /**
     * Show the form for editing an existing fee rule.
     * Handles both active and soft-deleted rules.
     */
    public function edit(FeeRule $feeRule)
    {
        $this->authorize('update', $feeRule);

        // If the rule is trashed, ensure we retrieve it correctly from storage
        if ($feeRule->trashed()) {
            $feeRule = FeeRule::withTrashed()->findOrFail($feeRule->id);
        }

        $clients = Client::where('active', true)->orderBy('name')->get();
        $docTypes = DocType::where('active', true)->orderBy('name')->get();
        $states = State::where('active', true)->orderBy('name')->get();
        $counties = County::where('active', true)->orderBy('name')->get();

        return view('masters.fee-rules.edit', compact('feeRule', 'clients', 'docTypes', 'states', 'counties'));
    }

    /**
     * Update the specified fee rule in storage.
     * Validation is performed by the UpdateFeeRuleRequest.
     */
    public function update(UpdateFeeRuleRequest $request, FeeRule $feeRule)
    {
        $this->authorize('update', $feeRule);
        $feeRule->update($request->validated()); // Apply changes
        return redirect()->route('masters.fee-rules.index')->with('success', 'Fee Rule updated successfully.');
    }

    /**
     * Remove the specified fee rule from storage (Soft Delete).
     */
    public function destroy(FeeRule $feeRule)
    {
        $this->authorize('delete', $feeRule);
        $feeRule->delete(); // Soft-delete operation managed by model trait
        return redirect()->route('masters.fee-rules.index')->with('success', 'Fee Rule deleted successfully.');
    }

    /**
     * Restore a soft-deleted fee rule to the system.
     */
    public function restore($id)
    {
        $feeRule = FeeRule::withTrashed()->findOrFail($id);
        $this->authorize('restore', $feeRule);
        $feeRule->restore(); // Re-activate the record in the database
        return redirect()->route('masters.fee-rules.index')->with('success', 'Fee Rule restored successfully.');
    }

    /**
     * Quick action to toggle the 'active' status of a fee rule.
     */
    public function toggleActive(FeeRule $feeRule)
    {
        $this->authorize('update', $feeRule);
        $feeRule->update(['active' => !$feeRule->active]); // Switch boolean state
        $status = $feeRule->fresh()->active ? 'activated' : 'deactivated';
        return back()->with('success', "Fee Rule {$status} successfully.");
    }
}
