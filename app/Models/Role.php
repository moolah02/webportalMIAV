<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Helper Methods
    public function isTechnician(): bool
    {
        $roleName = strtolower($this->name);

        $technicianKeywords = [
            'technician', 'field', 'maintenance', 'service', 'repair',
            'tech', 'installer', 'hardware', 'field_technician'
        ];

        foreach ($technicianKeywords as $keyword) {
            if (strpos($roleName, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    public function getSpecialization(): string
    {
        $roleName = strtolower($this->name);

        // Map role names to specializations
        if (strpos($roleName, 'hardware') !== false) {
            return 'Hardware Specialist';
        } elseif (strpos($roleName, 'software') !== false) {
            return 'Software Specialist';
        } elseif (strpos($roleName, 'network') !== false) {
            return 'Network Specialist';
        } elseif (strpos($roleName, 'field') !== false) {
            return 'Field Service Technician';
        } elseif (strpos($roleName, 'maintenance') !== false) {
            return 'Maintenance Technician';
        } elseif (strpos($roleName, 'technician') !== false) {
            return 'Technical Support';
        } elseif (strpos($roleName, 'manager') !== false) {
            return 'Manager';
        } elseif (strpos($roleName, 'admin') !== false) {
            return 'Administrator';
        } else {
            return ucwords(str_replace(['_', '-'], ' ', $this->name));
        }
    }

    public function getEmployeeType(): string
    {
        $roleName = strtolower($this->name);

        if (strpos($roleName, 'admin') !== false) {
            return 'admin';
        } elseif (strpos($roleName, 'manager') !== false || strpos($roleName, 'supervisor') !== false) {
            return 'manager';
        } elseif ($this->isTechnician()) {
            return 'technician';
        } else {
            return 'employee';
        }
    }

    // Check if role has specific permission
    public function hasPermission(string $permission): bool
    {
        // If the role has a `permissions` attribute (legacy JSON column), use it.
        if (!empty($this->permissions)) {
            return in_array('all', $this->permissions) || in_array($permission, $this->permissions);
        }

        // Fallback: check the role_permissions pivot + permissions table if present.
        try {
            if (Schema::hasTable('role_permissions') && Schema::hasTable('permissions')) {
                return DB::table('role_permissions')
                    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                    ->where('role_permissions.role_id', $this->id)
                    ->where(function ($q) use ($permission) {
                        $q->where('permissions.name', 'all')
                          ->orWhere('permissions.name', $permission);
                    })->exists();
            }
        } catch (\Exception $e) {
            // If DB isn't available or tables don't exist, treat as no permission.
        }

        return false;
    }

    // Get display name or fallback to formatted name
    public function getDisplayNameAttribute(): string
    {
        // Check if display_name column exists and has value
        if (Schema::hasColumn('roles', 'display_name') && !empty($this->attributes['display_name'])) {
            return $this->attributes['display_name'];
        }

        return ucwords(str_replace(['_', '-'], ' ', $this->name));
    }

    // Check if role is active (handle missing column gracefully)
    public function getIsActiveAttribute(): bool
    {
        if (Schema::hasColumn('roles', 'is_active')) {
            return (bool) $this->attributes['is_active'] ?? true;
        }

        // If column doesn't exist, assume all roles are active
        return true;
    }

    // Override the setIsActiveAttribute to handle missing column
    public function setIsActiveAttribute($value)
    {
        if (Schema::hasColumn('roles', 'is_active')) {
            $this->attributes['is_active'] = (bool) $value;
        }
        // If column doesn't exist, just ignore the assignment
    }
}
