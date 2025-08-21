<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth', 'can:manage_assets']);
    }

    public function index(Request $request)
    {
        try {
            // Get the active tab (default to 'assets')
            $activeTab = $request->get('tab', 'assets');
            
            // Get categories from database for filters
            $assetCategories = Category::ofType('asset_category')->active()->ordered()->get();
            $assetStatuses = Category::ofType('asset_status')->active()->ordered()->get();
            
            // Handle different tabs with actual data
            switch ($activeTab) {
                case 'assignments':
                    return $this->handleAssignmentsTab($request, $assetCategories, $assetStatuses);
                    
                case 'history':
                    return $this->handleHistoryTab($request, $assetCategories, $assetStatuses);
                    
                case 'assign':
                    return $this->handleAssignTab($request, $assetCategories, $assetStatuses);
                    
                default:
                    return $this->handleAssetsTab($request, $assetCategories, $assetStatuses);
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('AssetController index error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback if anything fails
            $activeTab = 'assets';
            $assets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $assetCategories = collect([]);
            $assetStatuses = collect([]);
            $stats = [
                'total_assets' => 0,
                'active_assets' => 0,
                'low_stock' => 0,
                'total_value' => 0,
            ];

            return view('assets.index', compact('assets', 'assetCategories', 'assetStatuses', 'stats', 'activeTab'));
        }
    }

    private function handleAssetsTab($request, $assetCategories, $assetStatuses)
    {
        $assetsQuery = Asset::query();

        // Apply existing filters
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
                    $assetsQuery->where('stock_quantity', '>', 0)
                              ->whereColumn('stock_quantity', '>', 'min_stock_level');
                    break;
                case 'low_stock':
                    $assetsQuery->where('stock_quantity', '>', 0)
                              ->whereColumn('stock_quantity', '<=', 'min_stock_level');
                    break;
                case 'out_of_stock':
                    $assetsQuery->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        $assets = $assetsQuery->latest()->paginate(15);
        $stats = Asset::getStats();
        $activeTab = 'assets';

        return view('assets.index', compact('assets', 'assetCategories', 'assetStatuses', 'stats', 'activeTab'));
    }

    private function handleAssignmentsTab($request, $assetCategories, $assetStatuses)
    {
        // Load current assignments with relationships
        $assignmentsQuery = AssetAssignment::with([
            'asset:id,name,category,brand,model,sku',
            'employee:id,first_name,last_name,employee_number,department_id',
            'employee.department:id,name',
            'assignedBy:id,first_name,last_name'
        ])->where('status', 'assigned'); // Only active assignments

        // Apply filters
        if ($request->filled('employee_search')) {
            $search = $request->employee_search;
            $assignmentsQuery->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('asset_search')) {
            $search = $request->asset_search;
            $assignmentsQuery->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $assignmentsQuery->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('overdue_only')) {
            $assignmentsQuery->whereNotNull('expected_return_date')
                ->where('expected_return_date', '<', now());
        }

        $assignments = $assignmentsQuery->latest('assignment_date')->paginate(15);
        
        // Transform assignments to add computed properties
        $assignments->getCollection()->transform(function ($assignment) {
            // Add full name to employee
            if ($assignment->employee) {
                $assignment->employee->full_name = $assignment->employee->first_name . ' ' . $assignment->employee->last_name;
            }
            
            // Add computed properties
            $assignment->days_assigned = $assignment->assignment_date ? 
                $assignment->assignment_date->diffInDays(now()) : 0;
            
            $assignment->is_overdue = $assignment->expected_return_date && 
                $assignment->expected_return_date->isPast() && 
                $assignment->status === 'assigned';
            
            if ($assignment->is_overdue) {
                $assignment->days_overdue = $assignment->expected_return_date->diffInDays(now());
            }
            
            return $assignment;
        });
        
        // Load departments for filter
        $departments = Department::orderBy('name')->get();
        
        // Calculate assignment stats
        $assignmentStats = [
            'active_assignments' => AssetAssignment::where('status', 'assigned')->count(),
            'overdue_assignments' => AssetAssignment::where('status', 'assigned')
                ->whereNotNull('expected_return_date')
                ->where('expected_return_date', '<', now())
                ->count(),
            'returned_this_month' => AssetAssignment::where('status', 'returned')
                ->whereMonth('actual_return_date', now()->month)
                ->whereYear('actual_return_date', now()->year)
                ->count(),
            'total_assignments' => AssetAssignment::count()
        ];

        $activeTab = 'assignments';
        $assets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15); // Empty for this tab
        $stats = Asset::getStats();

        return view('assets.index', compact(
            'assets', 'assetCategories', 'assetStatuses', 'stats', 'activeTab',
            'assignments', 'departments', 'assignmentStats'
        ));
    }

    private function handleHistoryTab($request, $assetCategories, $assetStatuses)
    {
        // Load assignment history with relationships
        $historyQuery = AssetAssignment::with([
            'asset:id,name,category,brand,model,sku',
            'employee:id,first_name,last_name,employee_number,department_id',
            'employee.department:id,name',
            'assignedBy:id,first_name,last_name',
            'returnedTo:id,first_name,last_name'
        ]);

        // Apply filters
        if ($request->filled('employee_search')) {
            $search = $request->employee_search;
            $historyQuery->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_filter')) {
            $historyQuery->where('status', $request->status_filter);
        }

        if ($request->filled('date_from')) {
            $historyQuery->where('assignment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $historyQuery->where('assignment_date', '<=', $request->date_to);
        }

        $history = $historyQuery->latest('assignment_date')->paginate(15);
        
        // Transform history to add computed properties
        $history->getCollection()->transform(function ($assignment) {
            // Add full name to employee
            if ($assignment->employee) {
                $assignment->employee->full_name = $assignment->employee->first_name . ' ' . $assignment->employee->last_name;
            }
            
            // Add computed properties
            $endDate = $assignment->actual_return_date ?? now();
            $assignment->days_assigned = $assignment->assignment_date ? 
                $assignment->assignment_date->diffInDays($endDate) : 0;
            
            // Status badge
            $assignment->status_badge = match($assignment->status) {
                'assigned' => 'status-active',
                'returned' => 'status-pending',
                'lost' => 'status-offline',
                'damaged' => 'status-offline',
                'transferred' => 'status-pending',
                default => 'status-pending'
            };
            
            return $assignment;
        });
        
        // Status options for filter
        $statusOptions = [
            'assigned' => 'Currently Assigned',
            'returned' => 'Returned',
            'transferred' => 'Transferred',
            'lost' => 'Lost',
            'damaged' => 'Damaged'
        ];

        $activeTab = 'history';
        $assets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15); // Empty for this tab
        $stats = Asset::getStats();

        return view('assets.index', compact(
            'assets', 'assetCategories', 'assetStatuses', 'stats', 'activeTab',
            'history', 'statusOptions'
        ));
    }

    private function handleAssignTab($request, $assetCategories, $assetStatuses)
    {
        // Get available assets for assignment
        $availableAssetsQuery = Asset::where('is_requestable', true)
            ->where('status', 'asset-active')
            ->where('stock_quantity', '>', 0);

        $availableAssets = $availableAssetsQuery->with(['activeAssignments.employee:id,first_name,last_name'])
            ->paginate(15);

        // Add available quantity to each asset
        $availableAssets->getCollection()->transform(function ($asset) {
            $assignedQuantity = $asset->activeAssignments->sum('quantity_assigned');
            $asset->available_quantity = $asset->stock_quantity - $assignedQuantity;
            $asset->assigned_quantity = $assignedQuantity;
            return $asset;
        });

        // Load employees for assignment
        $employees = Employee::with('department:id,name')
            ->where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'employee_number', 'department_id')
            ->orderBy('first_name')
            ->get();
            
        // Add full name to employees
        $employees->transform(function ($employee) {
            $employee->full_name = $employee->first_name . ' ' . $employee->last_name;
            return $employee;
        });
        
        // Load departments
        $departments = Department::orderBy('name')->get();
        
        // Condition options
        $conditionOptions = [
            'new' => 'New',
            'good' => 'Good', 
            'fair' => 'Fair',
            'poor' => 'Poor'
        ];

        $assignmentStats = [
            'active_assignments' => AssetAssignment::where('status', 'assigned')->count(),
            'overdue_assignments' => AssetAssignment::where('status', 'assigned')
                ->whereNotNull('expected_return_date')
                ->where('expected_return_date', '<', now())
                ->count(),
            'returned_this_month' => AssetAssignment::where('status', 'returned')
                ->whereMonth('actual_return_date', now()->month)
                ->whereYear('actual_return_date', now()->year)
                ->count(),
            'total_assignments' => AssetAssignment::count()
        ];

        $activeTab = 'assign';
        $assets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15); // Empty for this tab
        $stats = Asset::getStats();

        return view('assets.index', compact(
            'assets', 'assetCategories', 'assetStatuses', 'stats', 'activeTab',
            'availableAssets', 'employees', 'departments', 'assignmentStats', 'conditionOptions'
        ));
    }

    // Create new asset form
    public function create()
    {
        try {
            // Get categories from database for dropdown
            $assetCategories = Category::ofType('asset_category')->active()->ordered()->get();
            $assetStatuses = Category::ofType('asset_status')->active()->ordered()->get();
        } catch (\Exception $e) {
            // Fallback to empty collections if categories table doesn't exist
            $assetCategories = collect([]);
            $assetStatuses = collect([]);
        }

        return view('assets.create', compact('assetCategories', 'assetStatuses'));
    }

    // Store new asset
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:USD,EUR,GBP,ZWL',
            'status' => 'required|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:assets,sku',
            'barcode' => 'nullable|string',
            'image_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'is_requestable' => 'boolean',
            'requires_approval' => 'boolean',
        ];

        // Add category-specific validation rules
        if ($request->category === 'Vehicles') {
            $rules = array_merge($rules, [
                'license_plate' => 'required|string|max:20|unique:assets,specifications->license_plate',
                'vin_number' => 'nullable|string|max:50',
                'engine_number' => 'nullable|string|max:50',
                'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'vehicle_color' => 'nullable|string|max:10',
                'fuel_type' => 'nullable|string|in:Petrol,Diesel,Electric,Hybrid',
                'registration_date' => 'nullable|date',
                'insurance_expiry' => 'nullable|date',
            ]);
        } elseif ($request->category === 'POS Terminals') {
            $rules = array_merge($rules, [
                'terminal_id' => 'nullable|string|max:100',
                'software_version' => 'nullable|string|max:50',
            ]);
        } elseif ($request->category === 'Computer and IT Equipment') {
            $rules = array_merge($rules, [
                'processor' => 'nullable|string|max:100',
                'ram' => 'nullable|string|max:50',
                'storage' => 'nullable|string|max:50',
                'operating_system' => 'nullable|string|max:100',
            ]);
        } elseif ($request->category === 'Licenses') {
            $rules = array_merge($rules, [
                'license_key' => 'nullable|string|max:255',
                'license_expiry' => 'nullable|date',
                'max_users' => 'nullable|integer|min:1',
                'subscription_type' => 'nullable|string|in:Monthly,Annual,Perpetual',
            ]);
        }

        $validatedData = $request->validate($rules);

        try {
            // Build specifications array based on category
            $specifications = $this->buildSpecifications($request);

            $asset = Asset::create([
                'name' => $request->name,
                'category' => $request->category,
                'brand' => $request->brand,
                'model' => $request->model,
                'description' => $request->description,
                'unit_price' => $request->unit_price,
                'currency' => $request->currency,
                'status' => $request->status,
                'stock_quantity' => $request->stock_quantity,
                'min_stock_level' => $request->min_stock_level,
                'sku' => $request->sku,
                'barcode' => $request->barcode,
                'image_url' => $request->image_url,
                'notes' => $request->notes,
                'is_requestable' => $request->boolean('is_requestable', false),
                'requires_approval' => $request->boolean('requires_approval', true),
                'specifications' => $specifications,
                'assigned_quantity' => 0, // Initialize assigned quantity
            ]);

            return redirect()->route('assets.index')
                ->with('success', 'Asset created successfully!');

        } catch (\Exception $e) {
            \Log::error('Asset creation failed: ' . $e->getMessage());
            return back()
                ->with('error', 'Failed to create asset: ' . $e->getMessage())
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
        try {
            // Get categories from database for dropdown
            $assetCategories = Category::ofType('asset_category')->active()->ordered()->get();
            $assetStatuses = Category::ofType('asset_status')->active()->ordered()->get();
        } catch (\Exception $e) {
            // Fallback to empty collections if categories table doesn't exist
            $assetCategories = collect([]);
            $assetStatuses = collect([]);
        }

        return view('assets.edit', compact('asset', 'assetCategories', 'assetStatuses'));
    }

    // Update asset
    public function update(Request $request, Asset $asset)
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:USD,EUR,GBP,ZWL',
            'status' => 'required|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:assets,sku,' . $asset->id,
            'barcode' => 'nullable|string',
            'image_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'is_requestable' => 'boolean',
            'requires_approval' => 'boolean',
        ];

        // Add category-specific validation rules
        if ($request->category === 'Vehicles') {
            $rules = array_merge($rules, [
                'license_plate' => 'required|string|max:20',
                'vin_number' => 'nullable|string|max:50',
                'engine_number' => 'nullable|string|max:50',
                'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'vehicle_color' => 'nullable|string|max:50',
                'fuel_type' => 'nullable|string|in:Petrol,Diesel,Electric,Hybrid',
                'registration_date' => 'nullable|date',
                'insurance_expiry' => 'nullable|date',
            ]);
        } elseif ($request->category === 'POS Terminals') {
            $rules = array_merge($rules, [
                'terminal_id' => 'nullable|string|max:100',
                'software_version' => 'nullable|string|max:50',
            ]);
        } elseif ($request->category === 'Computer and IT Equipment') {
            $rules = array_merge($rules, [
                'processor' => 'nullable|string|max:100',
                'ram' => 'nullable|string|max:50',
                'storage' => 'nullable|string|max:50',
                'operating_system' => 'nullable|string|max:100',
            ]);
        } elseif ($request->category === 'Licenses') {
            $rules = array_merge($rules, [
                'license_key' => 'nullable|string|max:255',
                'license_expiry' => 'nullable|date',
                'max_users' => 'nullable|integer|min:1',
                'subscription_type' => 'nullable|string|in:Monthly,Annual,Perpetual',
            ]);
        }

        $request->validate($rules);

        try {
            // Build specifications array based on category
            $specifications = $this->buildSpecifications($request);

            $asset->update([
                'name' => $request->name,
                'category' => $request->category,
                'brand' => $request->brand,
                'model' => $request->model,
                'description' => $request->description,
                'unit_price' => $request->unit_price,
                'currency' => $request->currency,
                'status' => $request->status,
                'stock_quantity' => $request->stock_quantity,
                'min_stock_level' => $request->min_stock_level,
                'sku' => $request->sku,
                'barcode' => $request->barcode,
                'image_url' => $request->image_url,
                'notes' => $request->notes,
                'is_requestable' => $request->boolean('is_requestable', false),
                'requires_approval' => $request->boolean('requires_approval', true),
                'specifications' => $specifications,
            ]);

            return redirect()->route('assets.show', $asset)
                ->with('success', 'Asset updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Asset update failed: ' . $e->getMessage());
            return back()
                ->with('error', 'Failed to update asset: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Build specifications array based on category
    private function buildSpecifications(Request $request)
    {
        $specifications = [];

        if ($request->category === 'Vehicles') {
            $specifications = array_filter([
                'license_plate' => $request->license_plate,
                'vin_number' => $request->vin_number,
                'engine_number' => $request->engine_number,
                'vehicle_year' => $request->vehicle_year,
                'vehicle_color' => $request->vehicle_color,
                'fuel_type' => $request->fuel_type,
                'registration_date' => $request->registration_date,
                'insurance_expiry' => $request->insurance_expiry,
            ]);
        } elseif ($request->category === 'POS Terminals') {
            $specifications = array_filter([
                'terminal_id' => $request->terminal_id,
                'software_version' => $request->software_version,
            ]);
        } elseif ($request->category === 'Computer and IT Equipment') {
            $specifications = array_filter([
                'processor' => $request->processor,
                'ram' => $request->ram,
                'storage' => $request->storage,
                'operating_system' => $request->operating_system,
            ]);
        } elseif ($request->category === 'Licenses') {
            $specifications = array_filter([
                'license_key' => $request->license_key,
                'license_expiry' => $request->license_expiry,
                'max_users' => $request->max_users,
                'subscription_type' => $request->subscription_type,
            ]);
        }

        return $specifications;
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
            \Log::error('Asset deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete asset: ' . $e->getMessage());
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

            return response()->json(['success' => true, 'message' => 'Stock quantities updated successfully!']);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Bulk stock update failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update stock quantities.'], 500);
        }
    }

    // Update single asset stock
    public function updateStock(Request $request, Asset $asset)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        try {
            $asset->update([
                'stock_quantity' => $request->stock_quantity
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Stock updated successfully!',
                'new_stock' => $asset->stock_quantity
            ]);

        } catch (\Exception $e) {
            \Log::error('Stock update failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update stock.'], 500);
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
                'Status', 'Is Requestable', 'License Plate', 'VIN Number', 'Created At'
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
                    $asset->specifications['license_plate'] ?? '',
                    $asset->specifications['vin_number'] ?? '',
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
            ->where('status', 'asset-active')
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
                    'license_plate' => $asset->specifications['license_plate'] ?? null,
                ];
            })
        ]);
    }

    // Get vehicle-specific information
    public function getVehicleInfo(Asset $asset)
    {
        if ($asset->category !== 'Vehicles') {
            return response()->json(['error' => 'Asset is not a vehicle'], 400);
        }

        return response()->json([
            'license_plate' => $asset->specifications['license_plate'] ?? null,
            'vin_number' => $asset->specifications['vin_number'] ?? null,
            'engine_number' => $asset->specifications['engine_number'] ?? null,
            'vehicle_year' => $asset->specifications['vehicle_year'] ?? null,
            'vehicle_color' => $asset->specifications['vehicle_color'] ?? null,
            'fuel_type' => $asset->specifications['fuel_type'] ?? null,
            'registration_date' => $asset->specifications['registration_date'] ?? null,
            'insurance_expiry' => $asset->specifications['insurance_expiry'] ?? null,
        ]);
    }

    // Assignment management methods
    public function assignAsset(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'quantity' => 'required|integer|min:1',
            'assignment_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after:assignment_date',
            'condition_when_assigned' => 'required|in:new,good,fair,poor',
            'assignment_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $asset = Asset::findOrFail($request->asset_id);
            $employee = Employee::findOrFail($request->employee_id);

            // Check if asset can be assigned
            $availableQuantity = $asset->stock_quantity - $asset->assigned_quantity;
            if ($availableQuantity < $request->quantity) {
                return back()->with('error', 'Asset cannot be assigned in requested quantity. Available: ' . $availableQuantity);
            }

            DB::beginTransaction();

            $assignment = AssetAssignment::create([
                'asset_id' => $asset->id,
                'employee_id' => $employee->id,
                'quantity_assigned' => $request->quantity,
                'assignment_date' => $request->assignment_date,
                'expected_return_date' => $request->expected_return_date,
                'condition_when_assigned' => $request->condition_when_assigned,
                'assigned_by' => auth()->id() ?? 1,
                'assignment_notes' => $request->assignment_notes,
                'status' => 'assigned',
            ]);

            // Update asset assigned quantity
            $asset->increment('assigned_quantity', $request->quantity);

            DB::commit();

            return redirect()->route('assets.index', ['tab' => 'assignments'])
                            ->with('success', 'Asset assigned successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Asset assignment failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to assign asset: ' . $e->getMessage());
        }
    }

    // Get available employees for AJAX
    public function getAvailableEmployees(Request $request)
    {
        $employees = Employee::where('status', 'active')
                            ->with('department:id,name')
                            ->when($request->search, function($query, $search) {
                                $query->where(function($q) use ($search) {
                                    $q->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%")
                                      ->orWhere('employee_number', 'like', "%{$search}%");
                                });
                            })
                            ->select('id', 'first_name', 'last_name', 'employee_number', 'department_id')
                            ->orderBy('first_name')
                            ->limit(20)
                            ->get();

        return response()->json($employees->map(function($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'employee_number' => $employee->employee_number,
                'department' => $employee->department->name ?? 'N/A',
                'assigned_assets_count' => AssetAssignment::where('employee_id', $employee->id)
                                                          ->where('status', 'assigned')
                                                          ->count(),
            ];
        }));
    }

    // Return asset method
    public function returnAsset(Request $request, AssetAssignment $assignment)
    {
        $request->validate([
            'return_date' => 'required|date',
            'condition_when_returned' => 'required|in:new,good,fair,poor',
            'return_notes' => 'nullable|string|max:1000',
            'update_asset_status' => 'required|in:available,maintenance,damaged,retired',
        ]);

        if ($assignment->status !== 'assigned') {
            return response()->json([
                'success' => false,
                'message' => 'This asset cannot be returned.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $assignment->update([
                'status' => 'returned',
                'actual_return_date' => $request->return_date,
                'condition_when_returned' => $request->condition_when_returned,
                'return_notes' => $request->return_notes,
                'returned_to' => auth()->id() ?? 1,
            ]);

            // Update asset assigned quantity
            $assignment->asset->decrement('assigned_quantity', $assignment->quantity_assigned);

            // Update asset status if needed
            if ($request->update_asset_status !== 'available') {
                $assignment->asset->update(['status' => 'asset-' . $request->update_asset_status]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset returned successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Asset return failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to return asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assignment data for AJAX calls (for Details button)
     */
    public function getAssignmentData(AssetAssignment $assignment)
    {
        try {
            $assignment->load([
                'asset:id,name,category,brand,model,sku',
                'employee:id,first_name,last_name,employee_number,department_id',
                'employee.department:id,name',
                'assignedBy:id,first_name,last_name'
            ]);
            
            // Add computed properties
            $assignment->days_assigned = $assignment->assignment_date ? 
                $assignment->assignment_date->diffInDays(now()) : 0;
            
            $assignment->is_overdue = $assignment->expected_return_date && 
                $assignment->expected_return_date->isPast() && 
                $assignment->status === 'assigned';
            
            if ($assignment->is_overdue) {
                $assignment->days_overdue = $assignment->expected_return_date->diffInDays(now());
            }
            
            return response()->json([
                'success' => true,
                'assignment' => $assignment,
                'days_assigned' => $assignment->days_assigned,
                'is_overdue' => $assignment->is_overdue,
                'days_overdue' => $assignment->days_overdue ?? null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading assignment data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load assignment details'
            ], 500);
        }
    }

    /**
     * Transfer asset (for Transfer button)
     */
    public function transferAsset(Request $request, AssetAssignment $assignment)
    {
        $request->validate([
            'new_employee_id' => 'required|exists:employees,id',
            'transfer_date' => 'required|date',
            'transfer_reason' => 'required|string',
            'transfer_notes' => 'nullable|string|max:1000',
        ]);

        if ($assignment->status !== 'assigned') {
            return response()->json([
                'success' => false,
                'message' => 'This asset cannot be transferred.'
            ], 422);
        }

        if ($assignment->employee_id == $request->new_employee_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot transfer to the same employee.'
            ], 422);
        }

        try {
            $newEmployee = Employee::findOrFail($request->new_employee_id);

            DB::beginTransaction();

            // Mark current assignment as transferred
            $assignment->update([
                'status' => 'transferred',
                'actual_return_date' => $request->transfer_date,
                'return_notes' => 'Transferred to ' . $newEmployee->first_name . ' ' . $newEmployee->last_name . '. ' . ($request->transfer_notes ?? ''),
                'returned_to' => auth()->id() ?? 1,
            ]);

            // Create new assignment for the new employee
            AssetAssignment::create([
                'asset_id' => $assignment->asset_id,
                'employee_id' => $request->new_employee_id,
                'quantity_assigned' => $assignment->quantity_assigned,
                'assignment_date' => $request->transfer_date,
                'condition_when_assigned' => $assignment->condition_when_assigned,
                'assigned_by' => auth()->id() ?? 1,
                'assignment_notes' => 'Transferred from ' . $assignment->employee->first_name . ' ' . $assignment->employee->last_name . '. ' . ($request->transfer_notes ?? ''),
                'status' => 'assigned',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset transferred successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Asset transfer failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer asset: ' . $e->getMessage()
            ], 500);
        }
    }
}