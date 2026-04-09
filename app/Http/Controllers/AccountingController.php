<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FeeLine;
use App\Models\FeeRule;
use App\Models\FileStatusHistory;
use App\Services\AuditService;
use App\Services\FeeCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Controller for the Accounting module.
 * Handles fee review, approval, manual adjustments, and recalculation
 * for files in the ACCOUNTING stage.
 */
class AccountingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the Accounting Dashboard with summary statistics.
     */
    public function index()
    {
        $accountingStatus = config('constants.file_statuses.ACCOUNTING');
        $accountingApprovedStatus = config('constants.file_statuses.ACCOUNTING_APPROVED');
        $shippingStatus = config('constants.file_statuses.SHIPPING');

        // Pending files with their fee totals
        $pendingFiles = File::with(['client', 'docType', 'state', 'county'])
            ->withSum('feeLines', 'total_amount')
            ->where('current_status', $accountingStatus)
            ->latest()
            ->get();

        // Stats for the accounting dashboard
        $stats = [
            'pending' => $pendingFiles->count(),
            'pending_fees_total' => $pendingFiles->sum('fee_lines_sum_total_amount') ?? 0,
            'approved_today' => FileStatusHistory::where('to_status', $accountingApprovedStatus)
                ->whereDate('created_at', now()->toDateString())
                ->count(),
            'billed_today' => FileStatusHistory::where('to_status', $shippingStatus)
                ->where('from_status', $accountingApprovedStatus)
                ->whereDate('created_at', now()->toDateString())
                ->count(),
            'revenue_today' => File::whereHas('statusHistory', function($q) use ($shippingStatus) {
                $q->where('to_status', $shippingStatus)
                  ->whereDate('created_at', now()->toDateString());
            })
            ->withSum('feeLines', 'total_amount')
            ->get()
            ->sum('fee_lines_sum_total_amount'),
        ];

        // Recent Accounting Activity - Fetching 15 so we can show 'View More' if it exceeds 5
        $recentActivity = FileStatusHistory::with(['file', 'changedBy'])
            ->whereIn('to_status', [$accountingStatus, $accountingApprovedStatus, $shippingStatus])
            ->latest()
            ->take(15)
            ->get();

        return view('accounting.index', compact('stats', 'recentActivity', 'pendingFiles'));
    }

    /**
     * Display a list of files pending accounting approval.
     */
    public function pending(Request $request)
    {
        $query = File::with(['client', 'docType', 'state', 'county'])
            ->withSum('feeLines', 'total_amount')
            ->where('current_status', config('constants.file_statuses.ACCOUNTING'));

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('file_no', 'like', '%' . $request->search . '%')
                    ->orWhere('partner_ref_no', 'like', '%' . $request->search . '%');
            });
        }

        $files = $query->latest()->paginate(15)->withQueryString();

        return view('accounting.pending', compact('files'));
    }

    /**
     * Show the detailed accounting audit page for a file.
     * If the file has no fee lines, auto-triggers fee calculation to ensure
     * the breakdown is always populated for review.
     */
    public function show(File $file)
    {
        if ($file->current_status !== config('constants.file_statuses.ACCOUNTING')) {
            return redirect()->route('accounting.pending')->with('error', 'File is not in Accounting status.');
        }

        $file->load([
            'client',
            'docType',
            'recordingPurpose',
            'state',
            'county',
            'feeLines.feeRule',
            'statusHistory.changedBy'
        ]);

        // Auto-calculate fees if the file has zero fee lines
        // This catches edge cases where a file reached Accounting without fees
        $calculationSummary = null;
        if ($file->feeLines->count() === 0) {
            $calculationSummary = FeeCalculationService::calculate($file);
            $file->load('feeLines.feeRule'); // Reload after calculation
        }

        // Fetch matched rules for the context panel
        $matchedRules = FeeRule::active()
            ->effective()
            ->matching(
                $file->client_id,
                $file->doc_type_id,
                $file->state_id,
                $file->county_id
            )
            ->orderByPriority()
            ->get();

        return view('accounting.show', compact('file', 'matchedRules', 'calculationSummary'));
    }

    /**
     * Approve fees and move file to Accounting Approved status.
     */
    public function approve(Request $request, File $file)
    {
        if ($file->current_status !== config('constants.file_statuses.ACCOUNTING')) {
            return back()->with('error', 'Invalid file status.');
        }

        return DB::transaction(function () use ($file, $request) {
            $oldStatus = $file->current_status;
            $newStatus = config('constants.file_statuses.ACCOUNTING_APPROVED');

            // Update file status
            $file->update(['current_status' => $newStatus]);

            // Add history
            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => $request->notes ?? 'Accounting Approved',
            ]);

            // Log audit
            AuditService::log('STATUS_CHANGED', $file, [
                'status' => $oldStatus,
            ], [
                'status' => $newStatus,
                'notes' => $request->notes ?? 'Accounting Approved',
            ]);

            return redirect()->route('accounting.pending')
                ->with('success', "File {$file->file_no} approved and sent to Shipping.");
        });
    }

    /**
     * Return file to QC for correction.
     */
    public function returnToQC(Request $request, File $file)
    {
        $request->validate([
            'notes' => 'required|string|min:5|max:1000',
        ]);

        if ($file->current_status !== config('constants.file_statuses.ACCOUNTING')) {
            return back()->with('error', 'Invalid file status.');
        }

        return DB::transaction(function () use ($file, $request) {
            $oldStatus = $file->current_status;
            $newStatus = config('constants.file_statuses.QC');

            $file->update(['current_status' => $newStatus]);

            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => $request->notes,
            ]);

            AuditService::log('STATUS_CHANGED', $file, [
                'status' => $oldStatus,
            ], [
                'status' => $newStatus,
                'notes' => $request->notes,
            ]);

            return redirect()->route('accounting.pending')
                ->with('warning', "File {$file->file_no} returned to QC for correction.");
        });
    }

    /**
     * Override a specific fee line with a manual amount.
     */
    public function overrideFee(Request $request, ?FeeLine $feeLine = null)
    {
        $request->validate([
            'fee_line_id' => 'required_without:feeLine|exists:fee_lines,id',
            'new_total' => 'required|numeric|min:0',
            'reason' => 'required|string|min:5|max:500',
        ]);

        // Support both route model binding and form-submitted ID
        if (!$feeLine) {
            $feeLine = FeeLine::findOrFail($request->fee_line_id);
        }

        return DB::transaction(function () use ($feeLine, $request) {
            $oldAmount = $feeLine->total_amount;

            $feeLine->update([
                'total_amount' => $request->new_total,
                'is_override' => true,
                'override_reason' => $request->reason,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            AuditService::log('FEE_APPROVED', $feeLine->file, [
                'fee_line' => $feeLine->description,
                'old_amount' => $oldAmount,
            ], [
                'new_amount' => $request->new_total,
                'reason' => $request->reason,
            ]);

            return back()->with('success', "Fee '{$feeLine->description}' overridden successfully.");
        });
    }

    /**
     * Update the file's page count and re-calculate fees.
     */
    public function updatePageCount(Request $request, File $file)
    {
        $request->validate([
            'page_count' => 'required|integer|min:1|max:5000',
        ]);

        // RBAC & Workflow Check: Only Accounting/Admin can edit, and only in ACCOUNTING stage
        $isAccounting = Auth::user()->hasRole('Accounting');
        $isAdmin = Auth::user()->hasRole('Admin');
        $isPendingAccounting = ($file->current_status === config('constants.file_statuses.ACCOUNTING'));

        if (!$isAdmin && (!$isAccounting || !$isPendingAccounting)) {
            return back()->with('error', 'You do not have permission to edit page count for this file in its current state.');
        }

        DB::transaction(function () use ($file, $request) {
            $oldPageCount = $file->page_count;
            $file->update(['page_count' => $request->page_count]);

            // TRIGGER RE-CALCULATION
            FeeCalculationService::calculate($file);

            AuditService::log('FILE_UPDATED', $file, [
                'page_count' => $oldPageCount,
            ], [
                'page_count' => $request->page_count,
                'action' => 'FEE_RECALCULATION',
            ]);
        });

        return back()->with('success', "Page count updated to {$request->page_count}. Fees have been re-calculated.");
    }

    /**
     * Manually recalculate fees for a file.
     * This clears all auto-generated fee lines and re-runs the calculation engine.
     * Manual overrides (is_override = true) are preserved.
     */
    public function recalculateFees(File $file)
    {
        // RBAC: Only Accounting/Admin can recalculate
        $isAccounting = Auth::user()->hasRole('Accounting');
        $isAdmin = Auth::user()->hasRole('Admin');

        if (!$isAdmin && !$isAccounting) {
            return back()->with('error', 'You do not have permission to recalculate fees.');
        }

        if ($file->current_status !== config('constants.file_statuses.ACCOUNTING')) {
            return back()->with('error', 'Fees can only be recalculated for files in Accounting status.');
        }

        $summary = FeeCalculationService::calculate($file);

        AuditService::log('FEE_CALCULATED', $file, [], [
            'rules_matched' => $summary['rules_matched'],
            'lines_created' => $summary['lines_created'],
            'total_amount' => $summary['total_amount'],
            'triggered_by' => 'manual_recalculate',
        ]);

        return back()->with('success', "Fees recalculated: {$summary['rules_matched']} rules matched, {$summary['lines_created']} fee lines created. Total: \${$summary['total_amount']}");
    }
}
