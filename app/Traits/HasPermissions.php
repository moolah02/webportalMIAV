<?php

namespace App\Traits;

trait HasPermissions
{
    /**
     * Check if the employee has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permission);
    }

    /**
     * Check if the employee has any of the given permissions
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