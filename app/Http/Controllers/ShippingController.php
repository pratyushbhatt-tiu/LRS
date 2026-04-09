<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileStatusHistory;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShippingController extends Controller
{
    /**
     * Display a listing of files awaiting shipping.
     */
    public function index()
    {
        $shippingStatus = config('constants.file_statuses.SHIPPING');
        $readyStatus = config('constants.file_statuses.ACCOUNTING_APPROVED');

        $pendingFiles = File::with(['client', 'state', 'county', 'docType'])
            ->whereIn('current_status', [$shippingStatus, $readyStatus])
            ->orderBy('updated_at', 'desc')
            ->get();

        $stats = [
            'ready' => File::where('current_status', $readyStatus)->count(),
            'in_shipping' => File::where('current_status', $shippingStatus)->count(),
            'shipped_today' => File::where('current_status', config('constants.file_statuses.RECORDING'))
                ->whereDate('shipped_at', today())
                ->count(),
        ];

        return view('shipping.index', compact('pendingFiles', 'stats'));
    }

    /**
     * Show the shipping form for a specific file.
     */
    public function show(File $file)
    {
        $allowedStatuses = [
            config('constants.file_statuses.ACCOUNTING_APPROVED'),
            config('constants.file_statuses.SHIPPING')
        ];

        if (!in_array($file->current_status, $allowedStatuses)) {
            return redirect()->route('shipping.index')->with('error', 'File is not in a shippable status.');
        }

        // Auto-generate a default tracking number if not already present
        // Format: TRK-[FileNo]-[Rand]
        if (!$file->tracking_number) {
            $prefix = str_replace(['LRS-', '-'], '', $file->file_no);
            $file->tracking_number = 'TRK-' . $prefix . '-' . strtoupper(bin2hex(random_bytes(2)));
        }

        return view('shipping.show', compact('file'));
    }

    /**
     * Finalize shipment and transition to Recording.
     */
    public function ship(Request $request, File $file)
    {
        $request->validate([
            'courier' => 'required|string|max:100',
            'tracking_number' => 'nullable|string|max:100|required_unless:courier,Hand Delivered',
            'shipped_at' => 'required|date|before_or_equal:today',
            'shipping_notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($file, $request) {
            $oldStatus = $file->current_status;
            // The destination status according to constants is RECORDING
            $newStatus = config('constants.file_statuses.RECORDING');

            $file->update([
                'current_status' => $newStatus,
                'courier' => $request->courier,
                'tracking_number' => $request->tracking_number,
                'shipped_at' => $request->shipped_at,
                'shipping_notes' => $request->shipping_notes,
            ]);

            // Create record of the status change and shipment details
            FileStatusHistory::create([
                'file_id' => $file->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => "Shipment Finalized via {$request->courier}. " . ($request->tracking_number ? "Tracking: {$request->tracking_number}" : "Hand Delivered/No Tracking.")
            ]);

            // Specialized audit log for status changes
            AuditService::log('STATUS_CHANGED', $file, [
                'status' => $oldStatus,
            ], [
                'status' => $newStatus,
                'courier' => $request->courier,
                'tracking_number' => $request->tracking_number,
                'shipped_at' => $request->shipped_at,
            ]);

            return redirect()->route('shipping.index')
                ->with('success', "File #{$file->file_no} has been shipped successfully.");
        });
    }
}
