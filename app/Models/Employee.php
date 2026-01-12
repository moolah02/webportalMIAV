<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // Spatie guard (matches config/permission.php)
    protected string $guard_name = 'web';

    protected $fillable = [
        'employee_id',
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
        'hire_date'          => 'date',
        'last_login_at'      => 'datetime',
        'two_factor_enabled' => 'boolean',
        'salary'             => 'decimal:2',
    ];

    /* ===================== Relationships ===================== */

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

    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class, 'technician_id');
    }

    public function assetRequests()
    {
        return $this->hasMany(AssetRequest::class, 'employee_id');
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class, 'employee_id');
    }

    public function assignedAssets()
    {
        return $this->hasMany(AssetAssignment::class, 'assigned_by');
    }

    public function assetsReturnedTo()
    {
        return $this->hasMany(AssetAssignment::class, 'returned_to');
    }

    /**
     * Current assignments (status = assigned)
     */
    public function currentAssetAssignments()
    {
        return $this->hasMany(AssetAssignment::class, 'employee_id')
                    ->where('asset_assignments.status', 'assigned')
                    ->with('asset');
    }

    /**
     * Many-to-many convenience for currently assigned assets
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
                        'assignment_notes',
                    ]);
    }

    /* ===================== Accessors / Helpers ===================== */

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Total value of currently assigned assets
     * (FIXED: call the correct relation method)
     */
    public function getAssignedAssetsValueAttribute(): float
    {
        return (float) $this->currentAssetAssignments()
            ->join('assets', 'assets.id', '=', 'asset_assignments.asset_id')
            ->sum(DB::raw('assets.unit_price * asset_assignments.quantity_assigned'));
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isFieldTechnician(): bool
    {
        return $this->role ? $this->role->isTechnician() : false;
    }

    public function getSpecialization()
    {
        return $this->role->name ?? 'General';
    }

    public function getEmployeeType(): string
    {
        return $this->role ? $this->role->getEmployeeType() : 'employee';
    }

    /**
     * Keep your existing app-level permission check via Role model
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role ? $this->role->hasPermission($permission) : false;
    }

    public function isManager(): bool
    {
        return $this->getEmployeeType() === 'manager';
    }

    public function isAdmin(): bool
    {
        return $this->getEmployeeType() === 'admin';
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /* ===================== Scopes ===================== */

    public function scopeFieldTechnicians($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where(function ($subQ) {
                foreach (['technician','field','maintenance','service','repair','tech','installer','hardware'] as $kw) {
                    $subQ->orWhere('name', 'like', "%{$kw}%");
                }
            });
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeManagers($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('name', 'like', '%manager%')
              ->orWhere('name', 'like', '%supervisor%')
              ->orWhere('name', 'like', '%admin%');
        });
    }

    public function scopeWithAssignedAssets($query)
    {
        return $query->whereHas('currentAssetAssignments');
    }

    public function scopeWithOverdueReturns($query)
    {
        return $query->whereHas('assetAssignments', function ($q) {
            $q->where('asset_assignments.status', 'assigned')
              ->where('expected_return_date', '<', now())
              ->whereNull('actual_return_date');
        });
    }

    /**
     * Check if employee can access a specific module
     */
    public function canAccessModule(string $module): bool
    {
        $modulePermissions = [
            'dashboard' => ['all', 'view_dashboard', 'manage_team'],
            'assets' => ['all', 'manage_assets', 'view_own_data'],
            'terminals' => ['all', 'view_terminals', 'update_terminals'],
            'technicians' => ['all', 'manage_team', 'view_jobs'],
            'reports' => ['all', 'view_reports'],
            'employees' => ['all', 'manage_team'],
            'clients' => ['all', 'view_clients'],
        ];

        if (!isset($modulePermissions[$module])) {
            return false;
        }

        foreach ($modulePermissions[$module] as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }
}
