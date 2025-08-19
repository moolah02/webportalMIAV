<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetAssignment;
use App\Models\Employee;

class AssetController extends Controller
{
    /**
     * Get paginated list of assets with filtering
     */
    public function index(Request $request)
    {
        $query = Asset::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 0)
                          ->whereColumn('stock_quantity', '>', 'min_stock_level');
                    break;
                case 'low_stock':
                    $query->where('stock_quantity', '>', 0)
                          ->whereColumn('stock_quantity', '<=', 'min_stock_level');
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        if ($request->filled('requestable_only')) {
            $query->where('is_requestable', true)
                  ->where('status', 'asset-active');
        }

        $assets = $query->latest()->paginate($request->get('per_page', 15));

        // Transform the data for API response
        $assets->getCollection()->transform(function ($asset) {
            return [
                'id' => $asset->id,
                'name' => $asset->name,
                'description' => $asset->description,
                'category' => $asset->category,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'sku' => $asset->sku,
                'barcode' => $asset->barcode,
                'unit_price' => $asset->unit_price,
                'formatted_price' => $asset->formatted_price,
                'currency' => $asset->currency,
                'stock_quantity' => $asset->stock_quantity,
                'assigned_quantity' => $asset->assigned_quantity ?? 0,
                'available_quantity' => $asset->stock_quantity - ($asset->assigned_quantity ?? 0),
                'min_stock_level' => $asset->min_stock_level,
                'status' => $asset->status,
                'stock_status' => $asset->stock_status,
                'is_requestable' => $asset->is_requestable,
                'requires_approval' => $asset->requires_approval,
                'image_url' => $asset->image_url,
                'specifications' => $asset->specifications,
                'notes' => $asset->notes,
                'stock_info' => [
                    'is_in_stock' => $asset->isInStock(),
                    'is_low_stock' => $asset->isLowStock(),
                    'can_be_requested' => $asset->canBeRequested(),
                ],
                'created_at' => $asset->created_at,
                'updated_at' => $asset->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'assets' => $assets->items(),
                'pagination' => [
                    'current_page' => $assets->currentPage(),
                    'last_page' => $assets->lastPage(),
                    'per_page' => $assets->perPage(),
                    'total' => $assets->total(),
                ]
            ]
        ]);
    }

    /**
     * Get single asset details
     */
    public function show($id)
    {
        $asset = Asset::findOrFail($id);

        // Get current assignments
        $currentAssignments = [];
        try {
            if (method_exists($asset, 'activeAssignments')) {
                $currentAssignments = $asset->activeAssignments()
                                           ->with('employee:id,first_name,last_name,employee_number')
                                           ->get()
                                           ->map(function($assignment) {
                                               return [
                                                   'id' => $assignment->id,
                                                   'employee' => [
                                                       'id' => $assignment->employee->id,
                                                       'name' => $assignment->employee->first_name . ' ' . $assignment->employee->last_name,
                                                       'employee_number' => $assignment->employee->employee_number,
                                                   ],
                                                   'quantity_assigned' => $assignment->quantity_assigned,
                                                   'assignment_date' => $assignment->assignment_date,
                                                   'expected_return_date' => $assignment->expected_return_date,
                                                   'condition' => $assignment->condition_when_assigned,
                                               ];
                                           });
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load asset assignments: ' . $e->getMessage());
        }

        // Get recent requests
        $recentRequests = [];
        try {
            if (method_exists($asset, 'requestItems')) {
                $recentRequests = $asset->requestItems()
                                        ->with(['assetRequest.employee:id,first_name,last_name'])
                                        ->latest()
                                        ->limit(5)
                                        ->get()
                                        ->map(function($item) {
                                            return [
                                                'id' => $item->assetRequest->id,
                                                'request_number' => $item->assetRequest->request_number,
                                                'employee_name' => $item->assetRequest->employee ? 
                                                    $item->assetRequest->employee->first_name . ' ' . $item->assetRequest->employee->last_name : 'Unknown',
                                                'quantity_requested' => $item->quantity_requested,
                                                'status' => $item->assetRequest->status,
                                                'requested_at' => $item->assetRequest->created_at,
                                            ];
                                        });
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load asset requests: ' . $e->getMessage());
        }

        $assetData = [
            'id' => $asset->id,
            'name' => $asset->name,
            'description' => $asset->description,
            'category' => $asset->category,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'sku' => $asset->sku,
            'barcode' => $asset->barcode,
            'unit_price' => $asset->unit_price,
            'formatted_price' => $asset->formatted_price,
            'currency' => $asset->currency,
            'stock_quantity' => $asset->stock_quantity,
            'assigned_quantity' => $asset->assigned_quantity ?? 0,
            'available_quantity' => $asset->stock_quantity - ($asset->assigned_quantity ?? 0),
            'min_stock_level' => $asset->min_stock_level,
            'status' => $asset->status,
            'stock_status' => $asset->stock_status,
            'is_requestable' => $asset->is_requestable,
            'requires_approval' => $asset->requires_approval,
            'image_url' => $asset->image_url,
            'specifications' => $asset->specifications,
            'notes' => $asset->notes,
            'stock_info' => [
                'is_in_stock' => $asset->isInStock(),
                'is_low_stock' => $asset->isLowStock(),
                'can_be_requested' => $asset->canBeRequested(),
            ],
            'current_assignments' => $currentAssignments,
            'recent_requests' => $recentRequests,
            'created_at' => $asset->created_at,
            'updated_at' => $asset->updated_at,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'asset' => $assetData
            ]
        ]);
    }

    /**
     * Request an asset
     */
    public function requestAsset(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'justification' => 'required|string|max:500',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'expected_usage_date' => 'sometimes|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $asset = Asset::findOrFail($id);
        $user = $request->user();

        // Check if asset can be requested
        if (!$asset->canBeRequested($request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Asset cannot be requested in the specified quantity'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create or get existing pending request for this user
            $assetRequest = AssetRequest::firstOrCreate([
                'employee_id' => $user->id,
                'status' => 'pending'
            ], [
                'request_number' => 'REQ-' . now()->format('Ymd') . '-' . str_pad(AssetRequest::count() + 1, 4, '0', STR_PAD_LEFT),
                'requested_date' => now(),
                'status' => 'pending',
                'total_value' => 0,
            ]);

            // Add item to request
            $requestItem = $assetRequest->items()->create([
                'asset_id' => $asset->id,
                'quantity_requested' => $request->quantity,
                'unit_price' => $asset->unit_price,
                'total_price' => $asset->unit_price * $request->quantity,
                'justification' => $request->justification,
                'priority' => $request->priority ?? 'medium',
                'expected_usage_date' => $request->expected_usage_date,
            ]);

            // Update request total value
            $assetRequest->update([
                'total_value' => $assetRequest->items()->sum('total_price')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset request submitted successfully',
                'data' => [
                    'request' => [
                        'id' => $assetRequest->id,
                        'request_number' => $assetRequest->request_number,
                        'status' => $assetRequest->status,
                    ],
                    'item' => [
                        'id' => $requestItem->id,
                        'asset_name' => $asset->name,
                        'quantity_requested' => $requestItem->quantity_requested,
                        'total_price' => $requestItem->total_price,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Asset request failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit asset request'
            ], 500);
        }
    }

    /**
     * Get user's asset assignments
     */
    public function myAssignments(Request $request)
    {
        $user = $request->user();

        $assignments = [];
        try {
            if (method_exists($user, 'currentAssetAssignments')) {
                $assignments = $user->currentAssetAssignments()
                                   ->with('asset:id,name,category,brand,model,sku')
                                   ->get()
                                   ->map(function($assignment) {
                                       return [
                                           'id' => $assignment->id,
                                           'asset' => [
                                               'id' => $assignment->asset->id,
                                               'name' => $assignment->asset->name,
                                               'category' => $assignment->asset->category,
                                               'brand' => $assignment->asset->brand,
                                               'model' => $assignment->asset->model,
                                               'sku' => $assignment->asset->sku,
                                           ],
                                           'quantity_assigned' => $assignment->quantity_assigned,
                                           'assignment_date' => $assignment->assignment_date,
                                           'expected_return_date' => $assignment->expected_return_date,
                                           'condition_when_assigned' => $assignment->condition_when_assigned,
                                           'assignment_notes' => $assignment->assignment_notes,
                                           'is_overdue' => $assignment->expected_return_date && 
                                                          $assignment->expected_return_date->isPast(),
                                           'days_assigned' => $assignment->assignment_date ? 
                                               $assignment->assignment_date->diffInDays(now()) : 0,
                                       ];
                                   });
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load user assignments: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'assignments' => $assignments,
                'summary' => [
                    'total_assigned' => count($assignments),
                    'overdue_count' => collect($assignments)->where('is_overdue', true)->count(),
                ]
            ]
        ]);
    }

    /**
     * Return assigned asset
     */
    public function returnAsset(Request $request, $assignmentId)
    {
        $validator = Validator::make($request->all(), [
            'condition_when_returned' => 'required|in:new,good,fair,poor',
            'return_notes' => 'sometimes|string|max:500',
            'return_date' => 'sometimes|date',
            'photos' => 'sometimes|array',
            'photos.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        try {
            $assignment = AssetAssignment::where('id', $assignmentId)
                                        ->where('employee_id', $user->id)
                                        ->where('status', 'assigned')
                                        ->firstOrFail();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found or cannot be returned'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Handle photo uploads
            $photos = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('asset-returns', 'public');
                    $photos[] = [
                        'path' => $path,
                        'url' => asset('storage/' . $path),
                        'uploaded_at' => now()->toISOString()
                    ];
                }
            }

            // Update assignment
            $assignment->update([
                'status' => 'returned',
                'actual_return_date' => $request->return_date ?? now(),
                'condition_when_returned' => $request->condition_when_returned,
                'return_notes' => $request->return_notes,
                'return_photos' => $photos,
                'returned_to' => null, // Will be handled by admin
            ]);

            // Update asset assigned quantity
            $assignment->asset->decrement('assigned_quantity', $assignment->quantity_assigned);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset return request submitted successfully',
                'data' => [
                    'assignment_id' => $assignment->id,
                    'return_date' => $assignment->actual_return_date,
                    'status' => $assignment->status,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Asset return failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process asset return'
            ], 500);
        }
    }

    /**
     * Get user's asset requests
     */
    public function getRequests(Request $request)
    {
        $user = $request->user();

        $requests = AssetRequest::where('employee_id', $user->id)
                               ->with(['items.asset:id,name,category,brand'])
                               ->latest()
                               ->paginate($request->get('per_page', 10));

        $requests->getCollection()->transform(function($assetRequest) {
            return [
                'id' => $assetRequest->id,
                'request_number' => $assetRequest->request_number,
                'status' => $assetRequest->status,
                'requested_date' => $assetRequest->requested_date,
                'total_value' => $assetRequest->total_value,
                'items_count' => $assetRequest->items->count(),
                'items' => $assetRequest->items->map(function($item) {
                    return [
                        'asset_name' => $item->asset->name,
                        'category' => $item->asset->category,
                        'quantity_requested' => $item->quantity_requested,
                        'total_price' => $item->total_price,
                        'priority' => $item->priority,
                        'justification' => $item->justification,
                    ];
                }),
                'created_at' => $assetRequest->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests->items(),
                'pagination' => [
                    'current_page' => $requests->currentPage(),
                    'last_page' => $requests->lastPage(),
                    'per_page' => $requests->perPage(),
                    'total' => $requests->total(),
                ]
            ]
        ]);
    }

    /**
     * Create new asset request
     */
    public function createRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.asset_id' => 'required|exists:assets,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.justification' => 'required|string|max:500',
            'items.*.priority' => 'sometimes|in:low,medium,high,urgent',
            'expected_usage_date' => 'sometimes|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        try {
            DB::beginTransaction();

            // Create request
            $assetRequest = AssetRequest::create([
                'employee_id' => $user->id,
                'request_number' => 'REQ-' . now()->format('Ymd') . '-' . str_pad(AssetRequest::count() + 1, 4, '0', STR_PAD_LEFT),
                'requested_date' => now(),
                'status' => 'pending',
                'total_value' => 0,
            ]);

            $totalValue = 0;

            // Add items
            foreach ($request->items as $itemData) {
                $asset = Asset::findOrFail($itemData['asset_id']);

                // Check availability
                if (!$asset->canBeRequested($itemData['quantity'])) {
                    throw new \Exception("Asset '{$asset->name}' cannot be requested in quantity {$itemData['quantity']}");
                }

                $itemPrice = $asset->unit_price * $itemData['quantity'];
                $totalValue += $itemPrice;

                $assetRequest->items()->create([
                    'asset_id' => $asset->id,
                    'quantity_requested' => $itemData['quantity'],
                    'unit_price' => $asset->unit_price,
                    'total_price' => $itemPrice,
                    'justification' => $itemData['justification'],
                    'priority' => $itemData['priority'] ?? 'medium',
                    'expected_usage_date' => $request->expected_usage_date,
                ]);
            }

            // Update total value
            $assetRequest->update(['total_value' => $totalValue]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset request created successfully',
                'data' => [
                    'request' => [
                        'id' => $assetRequest->id,
                        'request_number' => $assetRequest->request_number,
                        'status' => $assetRequest->status,
                        'total_value' => $assetRequest->total_value,
                        'items_count' => count($request->items),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Asset request creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update asset request status (for managers/approvers)
     */
    public function updateRequestStatus(Request $request, $requestId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'sometimes|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Check permissions
        if (!$user->hasPermission('manage_assets') && 
            !$user->hasPermission('approve_requests') && 
            !$user->hasPermission('all')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update request status'
            ], 403);
        }

        try {
            $assetRequest = AssetRequest::findOrFail($requestId);

            $assetRequest->update([
                'status' => $request->status,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'approval_notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request status updated successfully',
                'data' => [
                    'request_id' => $assetRequest->id,
                    'new_status' => $assetRequest->status,
                    'approved_by' => $user->name,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Request status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update request status'
            ], 500);
        }
    }
}