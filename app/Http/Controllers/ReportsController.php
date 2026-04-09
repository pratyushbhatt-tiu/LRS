<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Client;
use App\Models\FeeLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display the Reports Dashboard with Month-Wise filtering.
     */
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month'); // Expects 'YYYY-MM' or null
        
        // 0. Get available months for filtering
        $availableMonths = File::select(DB::raw('DATE_FORMAT(received_date, "%Y-%m") as month'))
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        // Initializing Query Builders
        $statusQuery = File::query();
        $financialFileQuery = File::query();
        $feeLineQuery = FeeLine::query();
        $clientQuery = Client::withCount(['files' => function($q) use ($selectedMonth) {
            if ($selectedMonth) {
                $q->where(DB::raw('DATE_FORMAT(received_date, "%Y-%m")'), $selectedMonth);
            }
        }]);

        // Apply Month Filters if selected
        if ($selectedMonth) {
            $statusQuery->where(DB::raw('DATE_FORMAT(received_date, "%Y-%m")'), $selectedMonth);
            $financialFileQuery->where(DB::raw('DATE_FORMAT(received_date, "%Y-%m")'), $selectedMonth);
            $feeLineQuery->whereHas('file', function($q) use ($selectedMonth) {
                $q->where(DB::raw('DATE_FORMAT(received_date, "%Y-%m")'), $selectedMonth);
            });
        }

        // 1. Status Breakdown
        $statusCounts = $statusQuery->select('current_status', DB::raw('count(*) as count'))
            ->groupBy('current_status')
            ->get()
            ->pluck('count', 'current_status')
            ->toArray();

        // 2. Financial Aggregates
        $totalServiceFees = $feeLineQuery->sum('total_amount');
        $totalRecordingFees = $financialFileQuery->sum('recording_fee');

        // 3. Client Distribution
        $clientData = $clientQuery->orderBy('files_count', 'desc')
            ->take(25)
            ->get();

        // 4. Monthly Intake Trend (Always show last 6 months regardless of filter)
        $monthlyIntake = File::select(
            DB::raw('DATE_FORMAT(received_date, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->take(6)
        ->get();

        // 5. Global Activity (Recent Updates)
        $recentFiles = File::with(['client', 'docType'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'total_files' => $statusQuery->count(),
            'open_files' => (clone $statusQuery)->whereNotIn('current_status', ['CLOSED'])->count(),
            'closed_this_period' => (clone $statusQuery)->where('current_status', 'CLOSED')->count(),
            'total_service_fees' => $totalServiceFees,
            'total_recording_fees' => $totalRecordingFees,
            'selected_label' => $selectedMonth ? Carbon::parse($selectedMonth . '-01')->format('F Y') : 'Lifetime (All Time)',
            'current_month' => $selectedMonth
        ];

        return view('reports.index', compact('statusCounts', 'stats', 'clientData', 'monthlyIntake', 'recentFiles', 'availableMonths'));
    }
}
