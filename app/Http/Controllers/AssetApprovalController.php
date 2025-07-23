<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AssetRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetApprovalController extends Controller
{
    public function __construct()
    {
       // $this->middleware(['auth', 'can:manage_assets']);
    }

    // Dashboard for approvals
    public function index(Request $request)
    {
        $query = AssetRequest::with(['employee', 'items.asset']);

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

    // View specific request for approval
    public function show(AssetRequest $assetRequest)
    {
        $assetRequest->load(['items.asset', 'employee.department', 'employee.role', 'approver', 'fulfiller']);

        return view('asset-approvals.show', compact('assetRequest'));
    }

    // Approve request
    public function approve(AssetRequest $assetRequest, Request $request)
    {
        if ($assetRequest->status !== 'pending') {
            return back()->with('error', 'This request cannot be approved.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
            'item_approvals' => 'required|array',
            'item_approvals.*.quantity_approved' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Update main request
            $assetRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes,
            ]);

            // Update individual items
            $totalApprovedValue = 0;
            foreach ($request->item_approvals as $itemId => $approval) {
                $item = AssetRequestItem::find($itemId);
                if ($item) {
                    $quantityApproved = (int) $approval['quantity_approved'];
                    
                    $item->update([
                        'quantity_approved' => $quantityApproved,
                        'item_status' => $quantityApproved > 0 ? 
                            ($quantityApproved == $item->quantity_requested ? 'approved' : 'partially_approved') 
                            : 'rejected'
                    ]);

                    $totalApprovedValue += $quantityApproved * $item->unit_price_at_request;
                }
            }

            // Update total estimated cost to approved amount
            $assetRequest->update(['total_estimated_cost' => $totalApprovedValue]);

            DB::commit();

            return redirect()->route('asset-approvals.index')
                ->with('success', 'Request approved successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve request. Please try again.');
        }
    }

    // Reject request
    public function reject(AssetRequest $assetRequest, Request $request)
    {
        if ($assetRequest->status !== 'pending') {
            return back()->with('error', 'This request cannot be rejected.');
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
                ->with('success', 'Request rejected.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reject request. Please try again.');
        }
    }

    // Bulk actions (approve/reject multiple requests)
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
            return back()->with('error', 'No valid requests selected.');
        }

        try {
            DB::beginTransaction();

            foreach ($requests as $assetRequest) {
                if ($request->action === 'approve') {
                    // Auto-approve all items with requested quantities
                    $assetRequest->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'approval_notes' => $request->bulk_notes,
                    ]);

                    $assetRequest->items()->update([
                        'quantity_approved' => DB::raw('quantity_requested'),
                        'item_status' => 'approved'
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
            }

            DB::commit();

            $actionText = $request->action === 'approve' ? 'approved' : 'rejected';
            return back()->with('success', "Successfully {$actionText} {$requests->count()} request(s).");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to process bulk action. Please try again.');
        }
    }
}