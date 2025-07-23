<?php

namespace App\Providers;

use App\Models\Employee;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define Gates for common permissions
        Gate::define('manage-employees', function (Employee $employee) {
            return $employee->hasPermission('all') || $employee->hasPermission('manage_team');
        });

        Gate::define('approve-requests', function (Employee $employee) {
            return $employee->hasPermission('all') || $employee->hasPermission('approve_requests');
        });

        Gate::define('manage-assets', function (Employee $employee) {
            return $employee->hasPermission('all') || $employee->hasPermission('manage_assets');
        });

        Gate::define('view-reports', function (Employee $employee) {
            return $employee->hasPermission('all') || $employee->hasPermission('view_reports');
        });

        Gate::define('manage-technicians', function (Employee $employee) {
            return $employee->hasPermission('all') || $employee->hasPermission('manage_team');
        });

        Gate::define('view-dashboard', function (Employee $employee) {
            return $employee->hasPermission('all') || 
                   $employee->hasPermission('view_dashboard') ||
                   $employee->hasPermission('manage_team');
        });

        // Super admin gate
        Gate::define('admin-access', function (Employee $employee) {
            return $employee->hasPermission('all');
        });

        // Department-specific gates
        Gate::define('manage-department', function (Employee $employee, $departmentId = null) {
            if ($employee->hasPermission('all')) {
                return true;
            }
            
            if ($departmentId && $employee->department_id == $departmentId) {
                return $employee->hasPermission('manage_team');
            }
            
            return false;
        });
    }
}