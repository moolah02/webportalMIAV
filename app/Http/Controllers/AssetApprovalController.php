<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AssetRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetApprovalController extends Controller
{
    public function __construct()
    {
        // Uncomment this line if you want to add authorization middleware
        // $this->middleware(['auth', 'can:manage_assets']);
    }

    /**
     * Dashboard for approvals
     */
    public function index(Request $request)
    {
        $query = AssetRequest::with(['employee.department', 'employee.role', 'items.asset']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->latest()->paginate(10);

        // Statistics for dashboard
        $stats = [
            'pending_requests' => AssetRequest::where('status', 'pending')->count(),
            'pending_high_priority' => AssetRequest::where('status', 'pending')
                ->whereIn('priority', ['high', 'urgent'])->count(),
            'total_pending_value' => AssetRequest::where('status', 'pending')
                ->sum('total_estimated_cost'),
            'requests_this_month' => AssetRequest::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        return view('asset-approvals.index', compact('requests', 'stats'));
    }

    /**
     * View specific request for approval
     */
    public function show($id)
    {
        $assetRequest = AssetRequest::with([
            'items.asset', 
            'employee.department', 
            'employee.role', 
            'approver', 
            'fulfiller'
        ])->findOrFail($id);

        return view('asset-approvals.show', compact('assetRequest'));
    }

    /**
     * Approve request
     */
    public function approve($id, Request $request)
    {
        $assetRequest = AssetRequest::findOrFail($id);
        
        // Check if request can be approved
        if ($assetRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request cannot be approved.');
        }

        try {
            DB::beginTransaction();

            Log::info('Starting approval process for request: ' . $assetRequest->id);

            // Check if this is a detailed approval (from show page) or quick approval (from index page)
            if ($request->has('item_approvals')) {
                // Detailed approval from show page
                $request->validate([
                    'approval_notes' => 'nullable|string|max:1000',
                    'item_approvals' => 'required|array',
                    'item_approvals.*.quantity_approved' => 'required|integer|min:0',
                ]);

                $totalApprovedValue = 0;
                $hasApprovedItems = false;
                
                foreach ($request->item_approvals as $itemId => $approval) {
                    $item = AssetRequestItem::find($itemId);
                    if ($item && $item->asset_request_id == $assetRequest->id) {
                        $quantityApproved = (int) $approval['quantity_approved'];
                        
                        // Determine item status
                        $itemStatus = 'rejected';
                        if ($quantityApproved > 0) {
                            $hasApprovedItems = true;
                            $itemStatus = ($quantityApproved >= $item->quantity_requested) ? 'approved' : 'partially_approved';
                        }
                        
                        $item->update([
                            'quantity_approved' => $quantityApproved,
                            'item_status' => $itemStatus
                        ]);

                        $totalApprovedValue += $quantityApproved * $item->unit_price_at_request;
                        
                        Log::info("Updated item {$itemId}: approved={$quantityApproved}, status={$itemStatus}");
                    }
                }

                // Update main request
                $assetRequest->update([
                    'status' => $hasApprovedItems ? 'approved' : 'rejected',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'approval_notes' => $request->approval_notes,
                    'total_estimated_cost' => $totalApprovedValue,
                ]);

                $message = $hasApprovedItems ? 'Request approved successfully!' : 'Request rejected (no items approved).';

            } else {
                // Quick approval from index page - approve all items with requested quantities
                $request->validate([
                    'approval_notes' => 'nullable|string|max:1000',
                ]);

                $totalApprovedValue = 0;
                
                foreach ($assetRequest->items as $item) {
                    // Use available stock or requested quantity, whichever is smaller
                    $quantityToApprove = min($item->quantity_requested, $item->asset->stock_quantity ?? $item->quantity_requested);
                    
                    $item->update([
                        'quantity_approved' => $quantityToApprove,
                        'item_status' => 'approved'
                    ]);

                    $totalApprovedValue += $quantityToApprove * $item->unit_price_at_request;
                    
                    Log::info("Quick approved item {$item->id}: approved={$quantityToApprove}, status=approved");
                }

                // Update main request
                $assetRequest->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'approval_notes' => $request->approval_notes,
                    'total_estimated_cost' => $totalApprovedValue,
                ]);

                $message = 'Request approved successfully!';
            }

            Log::info('Updated main request: ' . $assetRequest->id . ' to status: ' . $assetRequest->status);

            DB::commit();

            return redirect()->route('asset-approvals.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to approve request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }

    /**
     * Reject request
     */
    public function reject($id, Request $request)
    {
        $assetRequest = AssetRequest::findOrFail($id);
        
        if ($assetRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request cannot be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Update main request
            $assetRequest->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Update all items to rejected
            $assetRequest->items()->update([
                'item_status' => 'rejected',
                'quantity_approved' => 0
            ]);

            DB::commit();

            return redirect()->route('asset-approvals.index')
                ->with('success', 'Request rejected successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to reject request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject request: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions (approve/reject multiple requests)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'request_ids' => 'required|array|min:1',
            'request_ids.*' => 'exists:asset_requests,id',
            'bulk_notes' => 'nullable|string|max:1000',
        ]);

        $requests = AssetRequest::whereIn('id', $request->request_ids)
            ->where('status', 'pending')
            ->get();

        if ($requests->isEmpty()) {
            return redirect()->back()->with('error', 'No valid requests selected.');
        }

        try {
            DB::beginTransaction();

            $successCount = 0;

            foreach ($requests as $assetRequest) {
                if ($request->action === 'approve') {
                    // Auto-approve all items with requested quantities
                    $totalApprovedValue = 0;
                    
                    foreach ($assetRequest->items as $item) {
                        $quantityToApprove = min($item->quantity_requested, $item->asset->stock_quantity ?? $item->quantity_requested);
                        
                        $item->update([
                            'quantity_approved' => $quantityToApprove,
                            'item_status' => 'approved'
                        ]);

                        $totalApprovedValue += $quantityToApprove * $item->unit_price_at_request;
                    }

                    $assetRequest->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'approval_notes' => $request->bulk_notes,
                        'total_estimated_cost' => $totalApprovedValue,
                    ]);

                } else {
                    // Reject
                    $assetRequest->update([
                        'status' => 'rejected',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'rejection_reason' => $request->bulk_notes ?: 'Bulk rejection',
                    ]);

                    $assetRequest->items()->update([
                        'item_status' => 'rejected',
                        'quantity_approved' => 0
                    ]);
                }

                $successCount++;
                Log::info("Bulk {$request->action} processed for request: {$assetRequest->id}");
            }

            DB::commit();

            $actionText = $request->action === 'approve' ? 'approved' : 'rejected';
            return redirect()->back()->with('success', "Successfully {$actionText} {$successCount} request(s).");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to process bulk action: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process bulk action: ' . $e->getMessage());
        }
    }

    /**
     * Get approval statistics for dashboard widgets
     */
    public function getStats()
    {
        return response()->json([
            'pending_requests' => AssetRequest::where('status', 'pending')->count(),
            'pending_high_priority' => AssetRequest::where('status', 'pending')
                ->whereIn('priority', ['high', 'urgent'])->count(),
            'total_pending_value' => AssetRequest::where('status', 'pending')
                ->sum('total_estimated_cost'),
            'requests_this_month' => AssetRequest::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'approval_rate' => $this->calculateApprovalRate(),
            'average_processing_time' => $this->calculateAverageProcessingTime(),
        ]);
    }

    /**
     * Calculate approval rate percentage
     */
    private function calculateApprovalRate()
    {
        $totalProcessed = AssetRequest::whereIn('status', ['approved', 'rejected', 'fulfilled'])->count();
        $approved = AssetRequest::whereIn('status', ['approved', 'fulfilled'])->count();
        
        return $totalProcessed > 0 ? round(($approved / $totalProcessed) * 100, 1) : 0;
    }

    /**
     * Calculate average processing time in hours
     */
    private function calculateAverageProcessingTime()
    {
        $processedRequests = AssetRequest::whereNotNull('approved_at')
            ->whereIn('status', ['approved', 'rejected', 'fulfilled'])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours')
            ->first();

        return $processedRequests ? round($processedRequests->avg_hours, 1) : 0;
    }

    /**
     * Export approval report
     */
    public function exportReport(Request $request)
    {
        $query = AssetRequest::with(['employee', 'items.asset', 'approver']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->get();

        // Generate CSV
        $filename = 'asset_approvals_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($requests) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Request Number',
                'Employee',
                'Department',
                'Status',
                'Priority',
                'Total Cost',
                'Submitted Date',
                'Approved Date',
                'Approver',
                'Items Count',
                'Business Justification'
            ]);

            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->request_number,
                    $request->employee->full_name,
                    $request->employee->department->name ?? 'N/A',
                    ucfirst($request->status),
                    ucfirst($request->priority),
                    '$' . number_format($request->total_estimated_cost, 2),
                    $request->created_at->format('Y-m-d H:i:s'),
                    $request->approved_at ? $request->approved_at->format('Y-m-d H:i:s') : 'N/A',
                    $request->approver->full_name ?? 'N/A',
                    $request->items->count(),
                    substr($request->business_justification, 0, 100) . '...'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function fulfill($id, Request $request)
{
    $assetRequest = AssetRequest::findOrFail($id);
    
    if ($assetRequest->status !== 'approved') {
        return redirect()->back()->with('error', 'Only approved requests can be fulfilled.');
    }

    $request->validate([
        'fulfillment_notes' => 'nullable|string|max:1000',
        'fulfillment_date' => 'required|date',
        'create_assignments' => 'boolean',
    ]);

    try {
        DB::beginTransaction();

        // Update the main request
        $assetRequest->update([
            'status' => 'fulfilled',
            'fulfilled_by' => auth()->id(),
            'fulfilled_at' => $request->fulfillment_date ?? now(),
            'fulfillment_notes' => $request->fulfillment_notes,
        ]);

        // Process each approved item
        foreach ($assetRequest->items()->where('item_status', 'approved')->get() as $item) {
            // Update item status
            $item->update([
                'quantity_fulfilled' => $item->quantity_approved,
                'item_status' => 'fulfilled'
            ]);

            // Create assignment if requested (default: true)
            if ($request->boolean('create_assignments', true)) {
                $assignment = AssetAssignment::create([
                    'asset_id' => $item->asset_id,
                    'employee_id' => $assetRequest->employee_id,
                    'quantity_assigned' => $item->quantity_approved,
                    'assignment_date' => $request->fulfillment_date ?? now(),
                    'condition_when_assigned' => 'new', // Assuming new items
                    'assigned_by' => auth()->id(),
                    'assignment_notes' => "Auto-assigned from request #{$assetRequest->request_number}",
                    'asset_request_id' => $assetRequest->id,
                    'status' => 'assigned',
                ]);

                Log::info("Created assignment {$assignment->id} for asset {$item->asset_id} to employee {$assetRequest->employee_id}");
            }

            // Update asset stock (reduce available quantity)
            $asset = $item->asset;
            if ($asset->stock_quantity >= $item->quantity_approved) {
                $asset->decrement('stock_quantity', $item->quantity_approved);
                Log::info("Reduced stock for asset {$asset->id} by {$item->quantity_approved}");
            }
        }

        DB::commit();

        $message = $request->boolean('create_assignments', true) 
            ? 'Request fulfilled and assets assigned to employee successfully!' 
            : 'Request fulfilled successfully!';

        return redirect()->route('asset-approvals.index')->with('success', $message);

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Failed to fulfill request: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to fulfill request: ' . $e->getMessage());
    }
}

}