<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasPermissions;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable, HasPermissions;

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
        'time_zone',
        'language',
        'two_factor_enabled',
        'status',
        'hire_date'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'hire_date' => 'date',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function assignedAssets()
    {
        return $this->hasMany(InternalAsset::class, 'assigned_to');
    }

    public function assetRequests()
    {
        return $this->hasMany(AssetRequest::class, 'employee_id');
    }

    public function approvedRequests()
    {
        return $this->hasMany(AssetRequest::class, 'approved_by');
    }

    public function reportedTickets()
    {
        return $this->hasMany(Ticket::class, 'reported_by');
    }

    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class, 'assigned_by');
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function dataImports()
    {
        return $this->hasMany(DataImport::class, 'uploaded_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Technician relationship (if employee is a technician)
    public function technician()
    {
        return $this->hasOne(Technician::class);
    }

    // Helper methods
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function hasPermission($permission)
    {
        return $this->role ? $this->role->hasPermission($permission) : false;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isTechnician()
    {
        return $this->technician()->exists();
    }

    public function isManager()
    {
        return $this->subordinates()->exists();
    }

    public function canApprove()
    {
        return $this->hasPermission('approve_requests') || $this->hasPermission('all');
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    public function scopeManagers($query)
    {
        return $query->whereHas('subordinates');
    }

    public function scopeTechnicians($query)
    {
        return $query->whereHas('technician');
    }

    /**
     * Check if the employee has a specific permission
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the employee has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
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

        return $this->hasAnyPermission($modulePermissions[$module]);
    }

    /**
     * Get employee's dashboard route based on role
     */
    
    public function getDashboardRoute(): string
    {
        if ($this->hasPermission('all') || $this->hasPermission('manage_team')) {
            return 'dashboard';
        }
        
        if ($this->hasPermission('view_jobs')) {
            return 'technician.dashboard';
        }
        
        return 'employee.dashboard';
    }
}