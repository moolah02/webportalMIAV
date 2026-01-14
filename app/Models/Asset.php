<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssetAssignment;
use App\Models\Employee;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'brand',
        'model',
        'unit_price',
        'currency',
        'stock_quantity',
        'min_stock_level',
        'sku',
        'barcode',
        'specifications',
        'image_url',
        'status',
        'is_requestable',
        'requires_approval',
        'notes',
        'assigned_quantity',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'specifications' => 'array',
        'is_requestable' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    // Relationships
    public function requestItems()
    {
        return $this->hasMany(AssetRequestItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'name');
    }

    /**
     * Get relationships where this asset is the parent
     * (e.g., Vehicle has Insurance)
     */
    public function assetRelationships()
    {
        return $this->hasMany(AssetRelationship::class, 'parent_asset_id');
    }

    /**
     * Get relationships where this asset is related to another
     * (e.g., Insurance belongs to Vehicle)
     */
    public function relatedToAssets()
    {
        return $this->hasMany(AssetRelationship::class, 'related_asset_id');
    }

    /**
     * Get all active relationships for this asset
     */
    public function activeRelationships()
    {
        return $this->assetRelationships()->active()->with('relatedAsset');
    }

    /**
     * Get related assets (e.g., get all licenses/insurance for a vehicle)
     */
    public function relatedAssets($type = null)
    {
        $query = $this->belongsToMany(Asset::class, 'asset_relationships', 'parent_asset_id', 'related_asset_id')
            ->withPivot(['relationship_type', 'starts_at', 'expires_at', 'is_active', 'notes', 'metadata'])
            ->wherePivot('is_active', true);

        if ($type) {
            $query->wherePivot('relationship_type', $type);
        }

        return $query;
    }

    /**
     * Get assets this asset is linked to (reverse relationship)
     */
    public function linkedFromAssets($type = null)
    {
        $query = $this->belongsToMany(Asset::class, 'asset_relationships', 'related_asset_id', 'parent_asset_id')
            ->withPivot(['relationship_type', 'starts_at', 'expires_at', 'is_active', 'notes', 'metadata'])
            ->wherePivot('is_active', true);

        if ($type) {
            $query->wherePivot('relationship_type', $type);
        }

        return $query;
    }

    /**
     * Check if this asset has a specific related asset
     */
    public function hasRelatedAsset($assetId, $type = null)
    {
        $query = $this->assetRelationships()->where('related_asset_id', $assetId)->active();

        if ($type) {
            $query->where('relationship_type', $type);
        }

        return $query->exists();
    }

    /**
     * Link this asset to another asset
     */
    public function linkToAsset($relatedAssetId, $type = 'linked_to', $data = [])
    {
        return AssetRelationship::create([
            'parent_asset_id' => $this->id,
            'related_asset_id' => $relatedAssetId,
            'relationship_type' => $type,
            'starts_at' => $data['starts_at'] ?? now(),
            'expires_at' => $data['expires_at'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => true,
        ]);
    }

    /**
     * Get expiring relationships (e.g., insurance expiring soon)
     */
    public function expiringRelationships($days = 30)
    {
        return $this->assetRelationships()->expiringSoon($days)->with('relatedAsset');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'asset-active');
    }

    public function scopeRequestable($query)
    {
        return $query->where('is_requestable', true)->where('status', 'asset-active');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level')
                    ->where('stock_quantity', '>', 0);
    }

    // Accessors & Helpers
    public function getFormattedPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->unit_price, 2);
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock_level && $this->stock_quantity > 0;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockStatusBadgeAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'status-offline',
            'low_stock' => 'status-pending',
            'in_stock' => 'status-active',
            default => 'status-active'
        };
    }

    public function canBeRequested($quantity = 1)
    {
        return $this->is_requestable &&
               in_array($this->status, ['asset-active', 'active']) &&
               $this->stock_quantity >= $quantity;
    }

    // Static methods for category/status options
    public static function getCategoryOptions()
    {
        return Category::ofType('asset_category')->active()->ordered()->get();
    }

    public static function getStatusOptions()
    {
        return Category::ofType('asset_status')->active()->ordered()->get();
    }
    // Helper method to get vehicle specifications
    public function getVehicleSpecifications()
    {
        if ($this->category !== 'Vehicles' || empty($this->specifications)) {
            return [];
        }

        return [
            'license_plate' => $this->specifications['license_plate'] ?? null,
            'vin_number' => $this->specifications['vin_number'] ?? null,
            'engine_number' => $this->specifications['engine_number'] ?? null,
            'vehicle_year' => $this->specifications['vehicle_year'] ?? null,
            'vehicle_color' => $this->specifications['vehicle_color'] ?? null,
            'fuel_type' => $this->specifications['fuel_type'] ?? null,
            'registration_date' => $this->specifications['registration_date'] ?? null,
            'insurance_expiry' => $this->specifications['insurance_expiry'] ?? null,
        ];
    }

    // Check if insurance is expired (for vehicles)
    public function isInsuranceExpired()
    {
        if ($this->category !== 'Vehicles' || empty($this->specifications['insurance_expiry'])) {
            return false;
        }

        return \Carbon\Carbon::parse($this->specifications['insurance_expiry'])->isPast();
    }

    // Check if license is expired (for software licenses)
    public function isLicenseExpired()
    {
        if ($this->category !== 'Licenses' || empty($this->specifications['license_expiry'])) {
            return false;
        }

        return \Carbon\Carbon::parse($this->specifications['license_expiry'])->isPast();
    }

    /**
 * Asset assignments relationship
 */
public function assignments()
{
    return $this->hasMany(AssetAssignment::class);
}

/**
 * Currently active assignments
 */
public function activeAssignments()
{
    return $this->hasMany(AssetAssignment::class)->where('status', 'assigned');
}

/**
 * Assignment history
 */
public function assignmentHistory()
{
    return $this->hasMany(AssetAssignment::class)->with(['employee', 'assignedBy'])->latest();
}

/**
 * Get employees who currently have this asset
 */
public function currentHolders()
{
    return $this->belongsToMany(Employee::class, 'asset_assignments')
                ->wherePivot('status', 'assigned')
                ->withPivot(['quantity_assigned', 'assignment_date', 'condition_when_assigned', 'assignment_notes']);
}

// Add these new methods to your Asset model:

/**
 * Get available quantity for assignment
 */
public function getAvailableQuantityAttribute()
{
    return $this->stock_quantity - $this->assigned_quantity;
}

/**
 * Check if asset can be assigned in specified quantity
 */
public function canBeAssigned($quantity = 1)
{
    return $this->is_requestable &&
           in_array($this->status, ['asset-active', 'active']) &&
           $this->getAvailableQuantityAttribute() >= $quantity;
}

/**
 * Get assignment status for this asset
 */
public function getAssignmentStatusAttribute()
{
    if ($this->assigned_quantity <= 0) {
        return 'available';
    } elseif ($this->available_quantity <= 0) {
        return 'fully_assigned';
    }
    return 'partially_assigned';
}

/**
 * Get assignment status badge class
 */
public function getAssignmentStatusBadgeAttribute()
{
    return match($this->assignment_status) {
        'available' => 'status-active',
        'partially_assigned' => 'status-pending',
        'fully_assigned' => 'status-offline',
        default => 'status-active'
    };
}

/**
 * Assign asset to employee
 */
public function assignToEmployee(Employee $employee, $quantity, $assignedBy, $data = [])
{
    if (!$this->canBeAssigned($quantity)) {
        throw new \Exception('Asset cannot be assigned in requested quantity');
    }

    return $this->assignments()->create([
        'employee_id' => $employee->id,
        'quantity_assigned' => $quantity,
        'assignment_date' => $data['assignment_date'] ?? now(),
        'expected_return_date' => $data['expected_return_date'] ?? null,
        'condition_when_assigned' => $data['condition_when_assigned'] ?? 'good',
        'assigned_by' => $assignedBy,
        'assignment_notes' => $data['assignment_notes'] ?? null,
        'asset_request_id' => $data['asset_request_id'] ?? null,
        'status' => 'assigned',
    ]);
}

/**
 * Get overdue assignments for this asset
 */
public function getOverdueAssignments()
{
    return $this->assignments()
                ->where('status', 'assigned')
                ->where('expected_return_date', '<', now())
                ->whereNull('actual_return_date')
                ->with('employee')
                ->get();
}

/**
 * Update the getStats method to include assignment data
 */
public static function getStats()
{
    return [
        'total_assets' => self::count(),
        'active_assets' => self::where('status', 'asset-active')->count(),
        'low_stock' => self::whereColumn('stock_quantity', '<=', 'min_stock_level')
                          ->where('stock_quantity', '>', 0)->count(),
        'total_value' => self::sum(\DB::raw('unit_price * stock_quantity')) ?? 0,
        'assigned_assets' => self::where('assigned_quantity', '>', 0)->count(),
        'fully_assigned' => self::whereColumn('assigned_quantity', '>=', 'stock_quantity')->count(),
        'overdue_assignments' => AssetAssignment::overdue()->count(),
    ];
}







// Add after your existing index method, modify it to handle tabs:

public function index(Request $request)
{
    try {
        // Get the active tab (default to 'assets')
        $activeTab = $request->get('tab', 'assets');

        // Common data for all tabs
        $assetCategories = Category::ofType('asset_category')->active()->ordered()->get();
        $assetStatuses = Category::ofType('asset_status')->active()->ordered()->get();

        $data = compact('assetCategories', 'assetStatuses', 'activeTab');

        switch ($activeTab) {
            case 'assignments':
                return $this->assignmentsTab($request, $data);
            case 'history':
                return $this->assignmentHistoryTab($request, $data);
            case 'assign':
                return $this->assignAssetsTab($request, $data);
            default:
                return $this->assetsTab($request, $data);
        }

    } catch (\Exception $e) {
        // Fallback for any errors
        return $this->assetsTab($request, ['assetCategories' => collect(), 'assetStatuses' => collect(), 'activeTab' => 'assets']);
    }
}

// Your existing assets tab logic (extracted from current index method)
private function assetsTab(Request $request, $data = [])
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

    return view('assets.index', array_merge($data, compact('assets', 'stats')));
}

// New assignments tab
private function assignmentsTab(Request $request, $data = [])
{
    $query = AssetAssignment::with(['asset', 'employee.department', 'assignedBy'])
                            ->where('status', 'assigned');

    // Filters for assignments
    if ($request->filled('employee_search')) {
        $search = $request->employee_search;
        $query->whereHas('employee', function($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('employee_number', 'like', "%{$search}%");
        });
    }

    if ($request->filled('asset_search')) {
        $search = $request->asset_search;
        $query->whereHas('asset', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    if ($request->filled('department')) {
        $query->whereHas('employee', function($q) use ($request) {
            $q->where('department_id', $request->department);
        });
    }

    if ($request->filled('overdue_only')) {
        $query->where('expected_return_date', '<', now())
              ->whereNull('actual_return_date');
    }

    $assignments = $query->latest('assignment_date')->paginate(15);

    // Get departments for filter
    $departments = Department::all();

    $assignmentStats = AssetAssignment::getStats();

    return view('assets.index', array_merge($data, compact('assignments', 'departments', 'assignmentStats')));
}

// Assignment history tab
private function assignmentHistoryTab(Request $request, $data = [])
{
    $query = AssetAssignment::with(['asset', 'employee.department', 'assignedBy', 'returnedTo']);

    // Filters for history
    if ($request->filled('employee_search')) {
        $search = $request->employee_search;
        $query->whereHas('employee', function($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('employee_number', 'like', "%{$search}%");
        });
    }

    if ($request->filled('status_filter')) {
        $query->where('status', $request->status_filter);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('assignment_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('assignment_date', '<=', $request->date_to);
    }

    $history = $query->latest('assignment_date')->paginate(15);

    $statusOptions = AssetAssignment::getStatusOptions();

    return view('assets.index', array_merge($data, compact('history', 'statusOptions')));
}

// Assign assets tab
private function assignAssetsTab(Request $request, $data = [])
{
    // Get available assets (those that can still be assigned)
    $availableAssets = Asset::where('is_requestable', true)
                           ->where('status', 'asset-active')
                           ->whereRaw('stock_quantity > assigned_quantity')
                           ->with('activeAssignments.employee')
                           ->paginate(15);

    // Get active employees
    $employees = Employee::active()
                        ->with('department')
                        ->orderBy('first_name')
                        ->get();

    $conditionOptions = AssetAssignment::getConditionOptions();

    return view('assets.index', array_merge($data, compact('availableAssets', 'employees', 'conditionOptions')));
}

// Assign asset to employee
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
        if (!$asset->canBeAssigned($request->quantity)) {
            return back()->with('error', 'Asset cannot be assigned in requested quantity.');
        }

        // Check if employee can receive assignment
        if (!$employee->canReceiveAssetAssignment()) {
            return back()->with('error', 'Employee cannot receive asset assignments.');
        }

        DB::beginTransaction();

        $assignment = $asset->assignToEmployee($employee, $request->quantity, auth()->id(), [
            'assignment_date' => $request->assignment_date,
            'expected_return_date' => $request->expected_return_date,
            'condition_when_assigned' => $request->condition_when_assigned,
            'assignment_notes' => $request->assignment_notes,
        ]);

        DB::commit();

        return redirect()->route('assets.index', ['tab' => 'assignments'])
                        ->with('success', 'Asset assigned successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Asset assignment failed: ' . $e->getMessage());
        return back()->with('error', 'Failed to assign asset: ' . $e->getMessage());
    }
}

// Return asset
public function returnAsset(Request $request, AssetAssignment $assignment)
{
    $request->validate([
        'condition_when_returned' => 'required|in:new,good,fair,poor',
        'return_notes' => 'nullable|string|max:1000',
    ]);

    if (!$assignment->canBeReturned()) {
        return back()->with('error', 'This asset cannot be returned.');
    }

    try {
        DB::beginTransaction();

        $assignment->update([
            'status' => 'returned',
            'actual_return_date' => now(),
            'condition_when_returned' => $request->condition_when_returned,
            'return_notes' => $request->return_notes,
            'returned_to' => auth()->id(),
        ]);

        DB::commit();

        return back()->with('success', 'Asset returned successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Asset return failed: ' . $e->getMessage());
        return back()->with('error', 'Failed to return asset: ' . $e->getMessage());
    }
}

// Transfer asset between employees
public function transferAsset(Request $request, AssetAssignment $assignment)
{
    $request->validate([
        'new_employee_id' => 'required|exists:employees,id|different:' . $assignment->employee_id,
        'transfer_notes' => 'nullable|string|max:1000',
    ]);

    if (!$assignment->canBeTransferred()) {
        return back()->with('error', 'This asset cannot be transferred.');
    }

    try {
        $newEmployee = Employee::findOrFail($request->new_employee_id);

        if (!$newEmployee->canReceiveAssetAssignment()) {
            return back()->with('error', 'Target employee cannot receive asset assignments.');
        }

        DB::beginTransaction();

        // Mark current assignment as transferred
        $assignment->update([
            'status' => 'transferred',
            'actual_return_date' => now(),
            'return_notes' => 'Transferred to ' . $newEmployee->full_name . '. ' . ($request->transfer_notes ?? ''),
            'returned_to' => auth()->id(),
        ]);

        // Create new assignment for the new employee
        $assignment->asset->assignToEmployee($newEmployee, $assignment->quantity_assigned, auth()->id(), [
            'assignment_date' => now(),
            'condition_when_assigned' => $assignment->condition_when_assigned,
            'assignment_notes' => 'Transferred from ' . $assignment->employee->full_name . '. ' . ($request->transfer_notes ?? ''),
        ]);

        DB::commit();

        return back()->with('success', 'Asset transferred successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Asset transfer failed: ' . $e->getMessage());
        return back()->with('error', 'Failed to transfer asset: ' . $e->getMessage());
    }
}

// Get assignment data for AJAX calls
public function getAssignmentData(AssetAssignment $assignment)
{
    $assignment->load(['asset', 'employee.department', 'assignedBy']);

    return response()->json([
        'assignment' => $assignment,
        'can_return' => $assignment->canBeReturned(),
        'can_transfer' => $assignment->canBeTransferred(),
        'is_overdue' => $assignment->isOverdue(),
        'days_assigned' => (int)$assignment->days_assigned,

    ]);
}

// Get available employees for assignment (AJAX)
public function getAvailableEmployees(Request $request)
{
    $employees = Employee::active()
                        ->with('department')
                        ->when($request->search, function($query, $search) {
                            $query->where(function($q) use ($search) {
                                $q->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('employee_number', 'like', "%{$search}%");
                            });
                        })
                        ->orderBy('first_name')
                        ->limit(20)
                        ->get();

    return response()->json($employees->map(function($employee) {
        return [
            'id' => $employee->id,
            'name' => $employee->full_name,
            'employee_number' => $employee->employee_number,
            'department' => $employee->department->name ?? 'N/A',
            'assigned_assets_count' => $employee->assigned_assets_count,
        ];
    }));
}



}
