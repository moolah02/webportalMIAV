<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth', 'can:manage_assets']);
    }

    // Asset Management Dashboard
   public function index(Request $request)
{
    try {
        // Check if Asset model exists and table has data
        $assetsQuery = Asset::query();

        // Apply filters only if request has them
        if ($request->filled('search')) {
            $search = $request->search;
            $assetsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $assetsQuery->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $assetsQuery->where('status', $request->status);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $assetsQuery->where('stock_quantity', '>', 10);
                    break;
                case 'low_stock':
                    $assetsQuery->whereBetween('stock_quantity', [1, 10]);
                    break;
                case 'out_of_stock':
                    $assetsQuery->where('stock_quantity', 0);
                    break;
            }
        }

        // Use paginate for $assets (has total() method)
        $assets = $assetsQuery->latest()->paginate(15);
        
        // Use get() for categories (Collection, no total() method)
        $categories = Asset::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');

        $stats = [
            'total_assets' => Asset::count(),
            'active_assets' => Asset::where('status', 'active')->count(),
            'low_stock' => Asset::where('stock_quantity', '<=', 10)->count(),
            'total_value' => Asset::sum('unit_price') ?? 0,
        ];

    } catch (\Exception $e) {
        // Fallback if Asset model/table doesn't exist
        $assets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        $categories = collect([]);
        $stats = [
            'total_assets' => 0,
            'active_assets' => 0,
            'low_stock' => 0,
            'total_value' => 0,
        ];
    }

    return view('assets.index', compact('assets', 'categories', 'stats'));
}
    // Create new asset form
    public function create()
    {
        return view('assets.create');
    }

    // Store new asset
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:USD,EUR,GBP',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:assets,sku',
            'barcode' => 'nullable|string',
            'image_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,discontinued',
            'is_requestable' => 'boolean',
            'requires_approval' => 'boolean',
        ]);

        try {
            Asset::create([
                'name' => $request->name,
                'category' => $request->category,
                'brand' => $request->brand,
                'model' => $request->model,
                'description' => $request->description,
                'unit_price' => $request->unit_price,
                'currency' => $request->currency,
                'stock_quantity' => $request->stock_quantity,
                'min_stock_level' => $request->min_stock_level,
                'sku' => $request->sku,
                'barcode' => $request->barcode,
                'image_url' => $request->image_url,
                'notes' => $request->notes,
                'status' => $request->status,
                'is_requestable' => $request->has('is_requestable'),
                'requires_approval' => $request->has('requires_approval'),
            ]);

            return redirect()->route('assets.index')
                ->with('success', 'Asset created successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to create asset. Please try again.')
                ->withInput();
        }
    }

    // View single asset
    public function show(Asset $asset)
    {
        $asset->load(['requestItems.assetRequest.employee']);
        
        // Get recent requests for this asset
        $recentRequests = $asset->requestItems()
            ->with(['assetRequest.employee'])
            ->latest()
            ->limit(10)
            ->get();

        return view('assets.show', compact('asset', 'recentRequests'));
    }

    // Edit asset form
    public function edit(Asset $asset)
    {
        return view('assets.edit', compact('asset'));
    }

    // Update asset
    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:USD,EUR,GBP',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:assets,sku,' . $asset->id,
            'barcode' => 'nullable|string',
            'image_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,discontinued',
            'is_requestable' => 'boolean',
            'requires_approval' => 'boolean',
        ]);

        try {
            $asset->update([
                'name' => $request->name,
                'category' => $request->category,
                'brand' => $request->brand,
                'model' => $request->model,
                'description' => $request->description,
                'unit_price' => $request->unit_price,
                'currency' => $request->currency,
                'stock_quantity' => $request->stock_quantity,
                'min_stock_level' => $request->min_stock_level,
                'sku' => $request->sku,
                'barcode' => $request->barcode,
                'image_url' => $request->image_url,
                'notes' => $request->notes,
                'status' => $request->status,
                'is_requestable' => $request->has('is_requestable'),
                'requires_approval' => $request->has('requires_approval'),
            ]);

            return redirect()->route('assets.index')
                ->with('success', 'Asset updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update asset. Please try again.')
                ->withInput();
        }
    }

    // Delete asset
    public function destroy(Asset $asset)
    {
        try {
            // Check if asset has any pending requests
            $pendingRequests = $asset->requestItems()
                ->whereHas('assetRequest', function($query) {
                    $query->whereIn('status', ['pending', 'approved']);
                })
                ->count();

            if ($pendingRequests > 0) {
                return back()->with('error', 'Cannot delete asset with pending or approved requests.');
            }

            $asset->delete();

            return redirect()->route('assets.index')
                ->with('success', 'Asset deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete asset. Please try again.');
        }
    }

    // Bulk update stock quantities
    public function bulkUpdateStock(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.asset_id' => 'required|exists:assets,id',
            'updates.*.stock_quantity' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->updates as $update) {
                Asset::where('id', $update['asset_id'])
                    ->update(['stock_quantity' => $update['stock_quantity']]);
            }

            DB::commit();

            return back()->with('success', 'Stock quantities updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update stock quantities. Please try again.');
        }
    }

    // Export assets to CSV
    public function export(Request $request)
    {
        $assets = Asset::all();

        $filename = 'assets_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($assets) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'ID', 'Name', 'Category', 'Brand', 'Model', 'SKU', 
                'Unit Price', 'Currency', 'Stock Quantity', 'Min Stock Level',
                'Status', 'Is Requestable', 'Created At'
            ]);

            // Data rows
            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->id,
                    $asset->name,
                    $asset->category,
                    $asset->brand,
                    $asset->model,
                    $asset->sku,
                    $asset->unit_price,
                    $asset->currency,
                    $asset->stock_quantity,
                    $asset->min_stock_level,
                    $asset->status,
                    $asset->is_requestable ? 'Yes' : 'No',
                    $asset->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Get low stock alerts
    public function lowStockAlerts()
    {
        $lowStockAssets = Asset::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('status', 'active')
            ->orderBy('stock_quantity')
            ->get();

        return response()->json([
            'count' => $lowStockAssets->count(),
            'assets' => $lowStockAssets->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'current_stock' => $asset->stock_quantity,
                    'min_level' => $asset->min_stock_level,
                    'category' => $asset->category,
                ];
            })
        ]);
    }
}