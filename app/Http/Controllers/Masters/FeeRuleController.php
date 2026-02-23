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

class FeeRuleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', FeeRule::class);
        $feeRules = FeeRule::query()
            ->with(['client', 'docType', 'state', 'county'])
            ->when($request->search, fn($q) => $q->where('rule_name', 'like', "%{$request->search}%"))
            ->when($request->status === 'active', fn($q) => $q->where('active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('active', false))
            ->when($request->with_trashed, fn($q) => $q->withTrashed())
            ->orderBy('priority')
            ->latest()
            ->paginate(15)->withQueryString();
        return view('masters.fee-rules.index', compact('feeRules'));
    }

    public function create()
    {
        $this->authorize('create', FeeRule::class);
        $clients = Client::where('active', true)->orderBy('name')->get();
        $docTypes = DocType::where('active', true)->orderBy('name')->get();
        $states = State::where('active', true)->orderBy('name')->get();
        $counties = County::where('active', true)->orderBy('name')->get();
        return view('masters.fee-rules.create', compact('clients', 'docTypes', 'states', 'counties'));
    }

    public function store(StoreFeeRuleRequest $request)
    {
        $this->authorize('create', FeeRule::class);
        FeeRule::create($request->validated());
        return redirect()->route('masters.fee-rules.index')->with('success', 'Fee Rule created successfully.');
    }

    public function edit(FeeRule $feeRule)
    {
        $this->authorize('update', $feeRule);
        if ($feeRule->trashed()) {
            $feeRule = FeeRule::withTrashed()->findOrFail($feeRule->id);
        }
        $clients = Client::where('active', true)->orderBy('name')->get();
        $docTypes = DocType::where('active', true)->orderBy('name')->get();
        $states = State::where('active', true)->orderBy('name')->get();
        $counties = County::where('active', true)->orderBy('name')->get();
        return view('masters.fee-rules.edit', compact('feeRule', 'clients', 'docTypes', 'states', 'counties'));
    }

    public function update(UpdateFeeRuleRequest $request, FeeRule $feeRule)
    {
        $this->authorize('update', $feeRule);
        $feeRule->update($request->validated());
        return redirect()->route('masters.fee-rules.index')->with('success', 'Fee Rule updated successfully.');
    }

    public function destroy(FeeRule $feeRule)
    {
        $this->authorize('delete', $feeRule);
        $feeRule->delete();
        return redirect()->route('masters.fee-rules.index')->with('success', 'Fee Rule deleted successfully.');
    }

    public function restore($id)
    {
        $feeRule = FeeRule::withTrashed()->findOrFail($id);
        $this->authorize('restore', $feeRule);
        $feeRule->restore();
        return redirect()->route('masters.fee-rules.index')->with('success', 'Fee Rule restored successfully.');
    }

    public function toggleActive(FeeRule $feeRule)
    {
        $this->authorize('update', $feeRule);
        $feeRule->update(['active' => !$feeRule->active]);
        $status = $feeRule->fresh()->active ? 'activated' : 'deactivated';
        return back()->with('success', "Fee Rule {$status} successfully.");
    }
}
