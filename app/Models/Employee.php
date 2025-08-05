<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_number',
        'first_name',
        'last_name', 
        'email',
        'password',
        'phone',
        'department_id',
        'role_id',
        'manager_id',
        'status',
        'hire_date',
        'time_zone',
        'language',
        'two_factor_enabled',
        'position',
        'salary',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'salary' => 'decimal:2',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    // Job Assignments relationship
    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class, 'technician_id');
    }

    // Asset Requests relationship - THIS WAS MISSING!
    public function assetRequests()
    {
        return $this->hasMany(AssetRequest::class, 'employee_id');
    }

    // Helper Methods - Now using role instead of redundant fields

    /**
     * Check if employee is a field technician (based on role)
     */
    public function isFieldTechnician(): bool
    {
        return $this->role ? $this->role->isTechnician() : false;
    }

    /**
     * Get employee specialization (based on role)
     */
    public function getSpecialization()
    {
        return $this->role->name ?? 'General';
    }

    /**
     * Get employee type (based on role)
     */
    public function getEmployeeType(): string
    {
        return $this->role ? $this->role->getEmployeeType() : 'employee';
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get name (alias for full_name)
     */
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Check if employee has specific permission (via role)
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role ? $this->role->hasPermission($permission) : false;
    }

    /**
     * Check if employee is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if employee is manager/supervisor
     */
    public function isManager(): bool
    {
        return $this->getEmployeeType() === 'manager';
    }

    /**
     * Check if employee is admin
     */
    public function isAdmin(): bool
    {
        return $this->getEmployeeType() === 'admin';
    }

    // Scopes for querying

    /**
     * Scope to get only field technicians
     */
    public function scopeFieldTechnicians($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where(function($subQ) {
                $technicianKeywords = ['technician', 'field', 'maintenance', 'service', 'repair', 'tech', 'installer', 'hardware'];
                foreach ($technicianKeywords as $keyword) {
                    $subQ->orWhere('name', 'LIKE', "%{$keyword}%");
                }
            });
        });
    }

    /**
     * Scope to get active employees
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get managers
     */
    public function scopeManagers($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'LIKE', '%manager%')
              ->orWhere('name', 'LIKE', '%supervisor%')
              ->orWhere('name', 'LIKE', '%admin%');
        });
    }

    public function updateLastLogin()
    {
        $this->update([
            'last_login_at' => now()
        ]);
    }

    
    /**
 * Asset assignments relationship - assets currently assigned to this employee
 */
public function assetAssignments()
{
    return $this->hasMany(AssetAssignment::class, 'employee_id');
}

/**
 * Currently assigned assets
 */
public function currentAssetAssignments()
{
    return $this->hasMany(AssetAssignment::class, 'employee_id')
                ->where('status', 'assigned')
                ->with('asset');
}

/**
 * Assets assigned by this employee (if they're a manager/admin)
 */
public function assignedAssets()
{
    return $this->hasMany(AssetAssignment::class, 'assigned_by');
}

/**
 * Assets returned to this employee (if they handle returns)
 */
public function assetsReturnedTo()
{
    return $this->hasMany(AssetAssignment::class, 'returned_to');
}

/**
 * Get all assets currently assigned to this employee
 */
public function getCurrentAssets()
{
    return $this->belongsToMany(Asset::class, 'asset_assignments')
                ->wherePivot('status', 'assigned')
                ->withPivot([
                    'id',
                    'quantity_assigned',
                    'assignment_date',
                    'expected_return_date',
                    'condition_when_assigned',
                    'assignment_notes'
                ]);
}

/**
 * Get assignment history for this employee
 */
public function getAssignmentHistory()
{
    return $this->assetAssignments()
                ->with(['asset', 'assignedBy'])
                ->latest('assignment_date')
                ->get();
}

/**
 * Check if employee has any assigned assets
 */
public function hasAssignedAssets(): bool
{
    return $this->currentAssetAssignments()->exists();
}

/**
 * Get overdue asset returns for this employee
 */
public function getOverdueAssets()
{
    return $this->assetAssignments()
                ->where('status', 'assigned')
                ->where('expected_return_date', '<', now())
                ->whereNull('actual_return_date')
                ->with('asset')
                ->get();
}

/**
 * Get count of assets assigned to this employee
 */
public function getAssignedAssetsCountAttribute()
{
    return $this->currentAssetAssignments()->count();
}

/**
 * Get total value of assets assigned to this employee
 */
public function getAssignedAssetsValueAttribute()
{
    return $this->currentAssetAssignments()
                ->join('assets', 'asset_assignments.asset_id', '=', 'assets.id')
                ->sum(\DB::raw('assets.unit_price * asset_assignments.quantity_assigned')) ?? 0;
}

/**
 * Check if employee can be assigned more assets (based on role/limits)
 */
public function canReceiveAssetAssignment(): bool
{
    // Basic check - employee must be active
    if (!$this->isActive()) {
        return false;
    }

    // Add any role-based or department-based limits here
    // For example, temporary employees might have limits
    
    return true;
}

/**
 * Check if employee can assign assets to others
 */
public function canAssignAssets(): bool
{
    return $this->hasPermission('manage_assets') || 
           $this->hasPermission('all') ||
           $this->isManager() || 
           $this->isAdmin();
}

/**
 * Scope to get employees with assigned assets
 */
public function scopeWithAssignedAssets($query)
{
    return $query->whereHas('currentAssetAssignments');
}

/**
 * Scope to get employees with overdue returns
 */
public function scopeWithOverdueReturns($query)
{
    return $query->whereHas('assetAssignments', function($q) {
        $q->where('status', 'assigned')
          ->where('expected_return_date', '<', now())
          ->whereNull('actual_return_date');
    });
}


}