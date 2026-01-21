<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AssetRequestController extends Controller
{
    /**
     * Get all asset requests for the authenticated user
     * GET /api/asset-requests
     */
    public function index(Request $request)
    {
        try {
            $query = AssetRequest::where('employee_id', $request->user()->id)
                ->with(['items.asset', 'approver', 'fulfiller']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $requests = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $requests->items(),
                'pagination' => [
                    'current_page' => $requests->currentPage(),
                    'last_page' => $requests->lastPage(),
                    'per_page' => $requests->perPage(),
                    'total' => $requests->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch asset requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific asset request
     * GET /api/asset-requests/{id}
     */
    public function show($id, Request $request)
    {
        try {
            $assetRequest = AssetRequest::with([
                'items.asset',
                'employee.department',
                'employee.roles',
                'approver',
                'fulfiller'
            ])->findOrFail($id);

            // Check if user can view this request
            if ($assetRequest->employee_id !== $request->user()->id &&
                !$request->user()->hasPermission('manage_assets') &&
                !$request->user()->hasPermission('all')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this request'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $assetRequest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch asset request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new asset request
     * POST /api/asset-requests
     *
     * Body:
     * {
     *   "items": [
     *     {"asset_id": 1, "quantity": 2},
     *     {"asset_id": 3, "quantity": 1}
     *   ],
     *   "business_justification": "Required for project work",
     *   "needed_by_date": "2026-02-01",
     *   "delivery_instructions": "Office location",
     *   "priority": "normal"
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.asset_id' => 'required|exists:assets,id',
            'items.*.quantity' => 'required|integer|min:1',
            'business_justification' => 'required|string',
            'needed_by_date' => 'nullable|date|after:today',
            'delivery_instructions' => 'nullable|string',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Calculate total cost
            $totalCost = 0;
            foreach ($request->items as $item) {
                $asset = Asset::find($item['asset_id']);
                if ($asset) {
                    $totalCost += $item['quantity'] * $asset->unit_price;
                }
            }

            // Get user's department
            $user = $request->user();
            $departmentName = $user->department ?
                (is_object($user->department) ? $user->department->name : $user->department) :
                null;

            // Create the main request
            $assetRequest = AssetRequest::create([
                'employee_id' => $user->id,
                'business_justification' => $request->business_justification,
                'needed_by_date' => $request->needed_by_date,
                'delivery_instructions' => $request->delivery_instructions,
                'priority' => $request->priority,
                'total_estimated_cost' => $totalCost,
                'department' => $departmentName,
                'status' => 'pending',
            ]);

            // Create request items
            foreach ($request->items as $item) {
                $asset = Asset::find($item['asset_id']);

                if ($asset && $asset->canBeRequested($item['quantity'])) {
                    AssetRequestItem::create([
                        'asset_request_id' => $assetRequest->id,
                        'asset_id' => $asset->id,
                        'quantity_requested' => $item['quantity'],
                        'unit_price_at_request' => $asset->unit_price,
                        'total_price' => $item['quantity'] * $asset->unit_price,
                        'item_status' => 'pending',
                    ]);
                }
            }

            DB::commit();

            // Load relationships for response
            $assetRequest->load(['items.asset', 'employee']);

            return response()->json([
                'success' => true,
                'message' => 'Asset request created successfully',
                'data' => $assetRequest
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an asset request
     * POST /api/asset-requests/{id}/cancel
     */
    public function cancel($id, Request $request)
    {
        try {
            $assetRequest = AssetRequest::findOrFail($id);

            // Check ownership
            if ($assetRequest->employee_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this request'
                ], 403);
            }

            // Check if can be cancelled
            if (!in_array($assetRequest->status, ['pending', 'draft'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel this request. Current status: ' . $assetRequest->status
                ], 400);
            }

            $assetRequest->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Request cancelled successfully',
                'data' => $assetRequest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available assets for requesting
     * GET /api/asset-requests/available-assets
     */
    public function availableAssets(Request $request)
    {
        try {
            $query = Asset::where('is_requestable', true)
                ->whereIn('status', ['asset-active', 'active', 'Available', 'available'])
                ->where('stock_quantity', '>', 0);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
                });
            }

            // Category filter
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            $perPage = $request->get('per_page', 20);
            $assets = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $assets->items(),
                'pagination' => [
                    'current_page' => $assets->currentPage(),
                    'last_page' => $assets->lastPage(),
                    'per_page' => $assets->perPage(),
                    'total' => $assets->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch available assets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get request statistics for the user
     * GET /api/asset-requests/stats
     */
    public function stats(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $stats = [
                'total_requests' => AssetRequest::where('employee_id', $userId)->count(),
                'pending' => AssetRequest::where('employee_id', $userId)->where('status', 'pending')->count(),
                'approved' => AssetRequest::where('employee_id', $userId)->where('status', 'approved')->count(),
                'rejected' => AssetRequest::where('employee_id', $userId)->where('status', 'rejected')->count(),
                'fulfilled' => AssetRequest::where('employee_id', $userId)->where('status', 'fulfilled')->count(),
                'cancelled' => AssetRequest::where('employee_id', $userId)->where('status', 'cancelled')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
