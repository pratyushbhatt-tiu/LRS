<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Client;
use App\Models\FeeLine;
use App\Models\FileStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ReportsController extends Controller
{
    /**
     * Display the Reports Dashboard with Enhanced Analytics.
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

        // 4. Daily Operational Metrics (Today)
        $dailyStats = [
            'received' => File::whereDate('received_date', today())->count(),
            'shipped'  => File::whereDate('shipped_at', today())->count(),
            'recorded' => File::whereDate('recorded_at', today())->count(),
            'returned' => File::whereDate('returned_at', today())->count(),
        ];

        // 5. Aging Analysis (Stale Files > 48 Hours)
        // We look at updated_at as a proxy for the last status change
        $staleFiles = File::with(['client', 'docType'])
            ->whereNotIn('current_status', ['CLOSED'])
            ->where('updated_at', '<', now()->subHours(48))
            ->orderBy('updated_at', 'asc')
            ->take(5)
            ->get();

        // 6. Global Activity (Recent Updates)
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

        return view('reports.index', compact('statusCounts', 'stats', 'clientData', 'recentFiles', 'availableMonths', 'dailyStats', 'staleFiles'));
    }

    /**
     * Export the filtered file list into a CSV.
     */
    public function export(Request $request)
    {
        $selectedMonth = $request->get('month');
        $query = File::with(['client', 'docType', 'state', 'county', 'feeLines']);

        if ($selectedMonth) {
            $query->where(DB::raw('DATE_FORMAT(received_date, "%Y-%m")'), $selectedMonth);
        }

        $files = $query->get();
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=lrs_report_" . ($selectedMonth ?: 'all') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['File #', 'Client', 'Doc Type', 'State/County', 'Received Date', 'Status', 'Recording Fee', 'Total Service Fee'];

        $callback = function() use($files, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            /** @var File $row */
            foreach ($files as $row) {
                fputcsv($file, [
                    $row->file_no,
                    $row->client->name,
                    $row->docType->name,
                    "{$row->state->name} / {$row->county->name}",
                    $row->received_date->format('d-m-Y'),
                    $row->current_status,
                    number_format($row->recording_fee, 2),
                    number_format($row->feeLines->sum('total_amount'), 2),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
