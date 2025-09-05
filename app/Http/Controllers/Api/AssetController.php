<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetAssignment;
use Illuminate\Support\Facades\Log;


class AssetController extends Controller
{
    /**
     * GET /assets
     * List assets for users (read-only).
     * Filters: search, category, status, stock_status (in_stock|low_stock|out_of_stock), requestable_only=1
     * Returns ALL (no pagination) as requested.
     */
    public function index(Request $request)
    {
        $q = Asset::query();

        // Optional: limit to active
        if (!$request->filled('include_inactive')) {
            $q->where('status', 'asset-active');
        }

        if ($request->boolean('requestable_only')) {
            $q->where('is_requestable', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $q->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) $q->where('category', $request->category);
        if ($request->filled('status'))   $q->where('status', $request->status);

        if ($request->filled('stock_status')) {
            $q->where(function($s) use ($request) {
                $s->when($request->stock_status === 'in_stock', function($x) {
                        $x->where('stock_quantity', '>', 0)
                          ->whereColumn('stock_quantity', '>', 'min_stock_level');
                    })
                  ->when($request->stock_status === 'low_stock', function($x) {
                        $x->where('stock_quantity', '>', 0)
                          ->whereColumn('stock_quantity', '<=', 'min_stock_level');
                    })
                  ->when($request->stock_status === 'out_of_stock', function($x) {
                        $x->where('stock_quantity', '<=', 0);
                    });
            });
        }

        $assets = $q->latest('id')->get()->map(function ($a) {
            $assigned = (int)($a->assigned_quantity ?? 0);
            $available = $a->available_quantity ?? max(0, (int)$a->stock_quantity - $assigned);
            $stock_status = $a->stock_quantity <= 0 ? 'out_of_stock'
                          : ($a->stock_quantity <= ($a->min_stock_level ?? 0) ? 'low_stock' : 'in_stock');

            return [
                'id' => $a->id,
                'name' => $a->name,
                'description' => $a->description,
                'category' => $a->category,
                'brand' => $a->brand,
                'model' => $a->model,
                'sku' => $a->sku,
                'barcode' => $a->barcode,
                'unit_price' => $a->unit_price,
                'currency' => $a->currency,
                'stock_quantity' => (int)$a->stock_quantity,
                'assigned_quantity' => $assigned,
                'available_quantity' => $available,
                'min_stock_level' => (int)($a->min_stock_level ?? 0),
                'status' => $a->status,
                'stock_status' => $stock_status,
                'is_requestable' => (bool)$a->is_requestable,
                'requires_approval' => (bool)$a->requires_approval,
                'image_url' => $a->image_url,
                'specifications' => $a->specifications,
                'notes' => $a->notes,
                'created_at' => $a->created_at,
                'updated_at' => $a->updated_at,
            ];
        });

        return response()->json(['success' => true, 'data' => ['assets' => $assets]]);
    }

    /**
     * GET /assets/{asset}
     * Asset details (read-only)
     */
    public function show(Asset $asset)
    {
        $assigned = (int)($asset->assigned_quantity ?? 0);
        $available = $asset->available_quantity ?? max(0, (int)$asset->stock_quantity - $assigned);
        $stock_status = $asset->stock_quantity <= 0 ? 'out_of_stock'
                      : ($asset->stock_quantity <= ($asset->min_stock_level ?? 0) ? 'low_stock' : 'in_stock');

        $data = [
            'id' => $asset->id,
            'name' => $asset->name,
            'description' => $asset->description,
            'category' => $asset->category,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'sku' => $asset->sku,
            'barcode' => $asset->barcode,
            'unit_price' => $asset->unit_price,
            'currency' => $asset->currency,
            'stock_quantity' => (int)$asset->stock_quantity,
            'assigned_quantity' => $assigned,
            'available_quantity' => $available,
            'min_stock_level' => (int)($asset->min_stock_level ?? 0),
            'status' => $asset->status,
            'stock_status' => $stock_status,
            'is_requestable' => (bool)$asset->is_requestable,
            'requires_approval' => (bool)$asset->requires_approval,
            'image_url' => $asset->image_url,
            'specifications' => $asset->specifications,
            'notes' => $asset->notes,
            'created_at' => $asset->created_at,
            'updated_at' => $asset->updated_at,
        ];

        return response()->json(['success' => true, 'data' => ['asset' => $data]]);
    }

    /**
     * POST /assets/{asset}/request
     * Create (or append to) a pending request for this user with a single item.
     * Uses your schema: asset_requests + asset_request_items
     */
    public function requestAsset(Request $request, Asset $asset)
    {
        $v = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'justification' => 'required|string|max:500',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'needed_by_date' => 'sometimes|date|after:today',
            'delivery_instructions' => 'sometimes|string|max:500',
            'department' => 'sometimes|string|max:100',
            'business_justification' => 'sometimes|string|max:500',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $v->errors()], 422);
        }

        if (!$asset->is_requestable || $asset->status !== 'asset-active') {
            return response()->json(['success' => false, 'message' => 'Asset is not requestable'], 422);
        }

        $assigned = (int)($asset->assigned_quantity ?? 0);
        $available = $asset->available_quantity ?? max(0, (int)$asset->stock_quantity - $assigned);
        if ($available < (int)$request->quantity) {
            return response()->json(['success' => false, 'message' => 'Insufficient stock available'], 422);
        }

        $user = $request->user();

        try {
            DB::beginTransaction();

            // Create a fresh request (clearer than firstOrCreate, and matches your richer schema)
            $req = AssetRequest::create([
                'request_number'        => 'REQ-' . now()->format('Ymd') . '-' . str_pad((string)(AssetRequest::count() + 1), 4, '0', STR_PAD_LEFT),
                'employee_id'           => $user->id,
                'status'                => 'pending',
                'priority'              => $request->priority ?? 'medium',
                'business_justification'=> $request->business_justification ?? $request->justification,
                'needed_by_date'        => $request->needed_by_date,
                'delivery_instructions' => $request->delivery_instructions,
                'department'            => $request->department,
                'total_estimated_cost'  => 0,
            ]);

            $itemTotal = (float)$asset->unit_price * (int)$request->quantity;

            $req->items()->create([
                'asset_id'               => $asset->id,
                'quantity_requested'     => (int)$request->quantity,
                'quantity_approved'      => 0,
                'quantity_fulfilled'     => 0,
                'unit_price_at_request'  => (float)$asset->unit_price,
                'total_price'            => $itemTotal,
                'item_status'            => 'pending',
                'notes'                  => $request->justification,
                'assignment_created'     => false,
            ]);

            $req->update(['total_estimated_cost' => $req->items()->sum('total_price')]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset request submitted',
                'data'    => [
                    'request' => [
                        'id' => $req->id,
                        'request_number' => $req->request_number,
                        'status' => $req->status,
                        'total_estimated_cost' => $req->total_estimated_cost,
                    ],
                ]
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('requestAsset error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to submit request'], 500);
        }
    }

    /**
     * GET /assets/requests
     * List the current user's requests (no pagination).
     */
    public function getRequests(Request $request)
    {
        $user = $request->user();

        $reqs = AssetRequest::where('employee_id', $user->id)
            ->with(['items.asset:id,name,category,brand,model'])
            ->latest('id')
            ->get()
            ->map(function($r) {
                return [
                    'id' => $r->id,
                    'request_number' => $r->request_number,
                    'status' => $r->status,
                    'priority' => $r->priority,
                    'business_justification' => $r->business_justification,
                    'needed_by_date' => $r->needed_by_date,
                    'total_estimated_cost' => $r->total_estimated_cost,
                    'items_count' => $r->items->count(),
                    'items' => $r->items->map(function($it) {
                        return [
                            'asset_id' => $it->asset_id,
                            'asset_name' => $it->asset?->name,
                            'category' => $it->asset?->category,
                            'quantity_requested' => $it->quantity_requested,
                            'quantity_approved' => $it->quantity_approved,
                            'quantity_fulfilled' => $it->quantity_fulfilled,
                            'unit_price_at_request' => $it->unit_price_at_request,
                            'total_price' => $it->total_price,
                            'item_status' => $it->item_status,
                            'notes' => $it->notes,
                        ];
                    }),
                    'created_at' => $r->created_at,
                ];
            });

        return response()->json(['success' => true, 'data' => ['requests' => $reqs]]);
    }

    /**
     * POST /assets/requests
     * Create a multi-item request in one go (user-only).
     * Body: { items: [{asset_id, quantity, justification, priority?}], needed_by_date?, delivery_instructions?, department?, business_justification? }
     */
    public function createRequest(Request $request)
    {
        $v = Validator::make($request->all(), [
            'items'                         => 'required|array|min:1',
            'items.*.asset_id'              => 'required|exists:assets,id',
            'items.*.quantity'              => 'required|integer|min:1',
            'items.*.justification'         => 'required|string|max:500',
            'items.*.priority'              => 'sometimes|in:low,medium,high,urgent',
            'needed_by_date'                => 'sometimes|date|after:today',
            'delivery_instructions'         => 'sometimes|string|max:500',
            'department'                    => 'sometimes|string|max:100',
            'business_justification'        => 'sometimes|string|max:500',
            'priority'                      => 'sometimes|in:low,medium,high,urgent', // optional overall priority
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $v->errors()], 422);
        }

        $user = $request->user();

        try {
            DB::beginTransaction();

            $req = AssetRequest::create([
                'request_number'        => 'REQ-' . now()->format('Ymd') . '-' . str_pad((string)(AssetRequest::count() + 1), 4, '0', STR_PAD_LEFT),
                'employee_id'           => $user->id,
                'status'                => 'pending',
                'priority'              => $request->priority ?? 'medium',
                'business_justification'=> $request->business_justification,
                'needed_by_date'        => $request->needed_by_date,
                'delivery_instructions' => $request->delivery_instructions,
                'department'            => $request->department,
                'total_estimated_cost'  => 0,
            ]);

            $total = 0;

            foreach ($request->items as $row) {
                /** @var Asset $asset */
                $asset = Asset::findOrFail($row['asset_id']);

                if (!$asset->is_requestable || $asset->status !== 'asset-active') {
                    throw new \Exception("Asset '{$asset->name}' is not requestable.");
                }

                $assigned = (int)($asset->assigned_quantity ?? 0);
                $available = $asset->available_quantity ?? max(0, (int)$asset->stock_quantity - $assigned);
                if ($available < (int)$row['quantity']) {
                    throw new \Exception("Insufficient stock for '{$asset->name}'.");
                }

                $itemTotal = (float)$asset->unit_price * (int)$row['quantity'];
                $total += $itemTotal;

                $req->items()->create([
                    'asset_id'               => $asset->id,
                    'quantity_requested'     => (int)$row['quantity'],
                    'quantity_approved'      => 0,
                    'quantity_fulfilled'     => 0,
                    'unit_price_at_request'  => (float)$asset->unit_price,
                    'total_price'            => $itemTotal,
                    'item_status'            => 'pending',
                    'notes'                  => $row['justification'],
                    'assignment_created'     => false,
                ]);
            }

            $req->update(['total_estimated_cost' => $total]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset request created',
                'data' => [
                    'request' => [
                        'id' => $req->id,
                        'request_number' => $req->request_number,
                        'status' => $req->status,
                        'total_estimated_cost' => $req->total_estimated_cost,
                        'items_count' => count($request->items),
                    ]
                ]
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('createRequest error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create asset request'], 500);
        }
    }

    /**
     * GET /assets/my-assignments
     * The current user's active assignments (no pagination)
     */
    public function myAssignments(Request $request)
    {
        $user = $request->user();

        $rows = AssetAssignment::where('employee_id', $user->id)
            ->where('status', 'assigned')
            ->with('asset:id,name,category,brand,model,sku')
            ->latest('id')
            ->get()
            ->map(function($a) {
                return [
                    'id' => $a->id,
                    'asset' => [
                        'id' => $a->asset->id,
                        'name' => $a->asset->name,
                        'category' => $a->asset->category,
                        'brand' => $a->asset->brand,
                        'model' => $a->asset->model,
                        'sku' => $a->asset->sku,
                    ],
                    'quantity_assigned' => (int)$a->quantity_assigned,
                    'assignment_date' => $a->assignment_date,
                    'expected_return_date' => $a->expected_return_date,
                    'condition_when_assigned' => $a->condition_when_assigned,
                    'assignment_notes' => $a->assignment_notes,
                    'is_overdue' => $a->expected_return_date ? $a->expected_return_date->isPast() : false,
                    'days_assigned' => $a->assignment_date ? $a->assignment_date->diffInDays(now()) : 0,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'assignments' => $rows,
                'summary' => [
                    'total_assigned' => $rows->count(),
                    'overdue_count'  => $rows->where('is_overdue', true)->count(),
                ]
            ]
        ]);
    }

    /**
     * POST /assets/assignments/{assignment}/return
     * Mark a user’s own assignment as returned (user-scoped).
     */
    public function returnAsset(Request $request, $assignmentId)
    {
        $v = Validator::make($request->all(), [
            'condition_when_returned' => 'required|in:new,good,fair,poor',
            'return_notes' => 'sometimes|string|max:500',
            'return_date'  => 'sometimes|date',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $v->errors()], 422);
        }

        $user = $request->user();

        /** @var AssetAssignment $as */
        $as = AssetAssignment::where('id', $assignmentId)
            ->where('employee_id', $user->id)
            ->where('status', 'assigned')
            ->with('asset')
            ->first();

        if (!$as) {
            return response()->json(['success' => false, 'message' => 'Assignment not found or not returnable'], 404);
        }

        try {
            DB::beginTransaction();

            // Update assignment
            $as->update([
                'status' => 'returned',
                'actual_return_date' => $request->return_date ?? now(),
                'condition_when_returned' => $request->condition_when_returned,
                'return_notes' => $request->return_notes,
                // 'returned_to' left null for backoffice to fill if needed
            ]);

            // Adjust asset counters
            if ($as->asset) {
                $as->asset->decrement('assigned_quantity', (int)$as->quantity_assigned);
                // If you maintain available_quantity as a stored column, uncomment:
                // $as->asset->increment('available_quantity', (int)$as->quantity_assigned);
            }

            // Optional: write assignment history
            DB::table('asset_assignment_histories')->insert([
                'assignment_id'   => $as->id,
                'action'          => 'return',
                'from_employee_id'=> $user->id,
                'to_employee_id'  => null,
                'action_date'     => now(),
                'performed_by'    => $user->id,
                'notes'           => $request->return_notes,
                'old_status'      => 'assigned',
                'new_status'      => 'returned',
                'created_at'      => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Return submitted',
                'data' => [
                    'assignment_id' => $as->id,
                    'status' => $as->status,
                    'return_date' => $as->actual_return_date,
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('returnAsset error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to process return'], 500);
        }
    }

    /**
     * GET /assets/categories
     * Distinct categories for browsing requestable, active assets
     */
    public function getCategories(Request $request)
    {
        $cats = Asset::query()
            ->when(!$request->boolean('include_inactive'), fn($q) => $q->where('status', 'asset-active'))
            ->where('is_requestable', true)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values();

        return response()->json(['success' => true, 'data' => ['categories' => $cats]]);
    }

    /**
     * GET /assets/statistics
     * User-scoped quick stats (no admin/global data)
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        $activeAssigned = AssetAssignment::where('employee_id', $user->id)
            ->where('status', 'assigned')
            ->count();

        $overdue = AssetAssignment::where('employee_id', $user->id)
            ->where('status', 'assigned')
            ->whereNotNull('expected_return_date')
            ->whereDate('expected_return_date', '<', now()->toDateString())
            ->count();

        $pendingReqs = AssetRequest::where('employee_id', $user->id)->where('status', 'pending')->count();
        $approvedReqs = AssetRequest::where('employee_id', $user->id)->where('status', 'approved')->count();
        $rejectedReqs = AssetRequest::where('employee_id', $user->id)->where('status', 'rejected')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'assignments' => [
                    'active'  => $activeAssigned,
                    'overdue' => $overdue,
                ],
                'requests' => [
                    'pending'  => $pendingReqs,
                    'approved' => $approvedReqs,
                    'rejected' => $rejectedReqs,
                ]
            ]
        ]);
    }
}
