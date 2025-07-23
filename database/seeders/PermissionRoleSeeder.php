<?php

// ==============================================
// 7. SEEDER FOR DEFAULT PERMISSIONS AND ROLES
// Run: php artisan make:seeder PermissionRoleSeeder
// ==============================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Super Admin
            ['name' => 'all', 'display_name' => 'All Permissions', 'description' => 'Full system access', 'category' => 'admin'],
            
            // Dashboard & General
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'description' => 'Access main dashboard', 'category' => 'general'],
            ['name' => 'view_own_data', 'display_name' => 'View Own Data', 'description' => 'View personal information', 'category' => 'general'],
            
            // Asset Management
            ['name' => 'manage_assets', 'display_name' => 'Manage Assets', 'description' => 'Full asset management', 'category' => 'assets'],
            ['name' => 'request_assets', 'display_name' => 'Request Assets', 'description' => 'Request company assets', 'category' => 'assets'],
            ['name' => 'approve_requests', 'display_name' => 'Approve Requests', 'description' => 'Approve asset requests', 'category' => 'assets'],
            
            // Client Management
            ['name' => 'view_clients', 'display_name' => 'View Clients', 'description' => 'View client information', 'category' => 'clients'],
            ['name' => 'manage_clients', 'display_name' => 'Manage Clients', 'description' => 'Full client management', 'category' => 'clients'],
            
            // Team Management
            ['name' => 'manage_team', 'display_name' => 'Manage Team', 'description' => 'Manage team members', 'category' => 'management'],
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'description' => 'Access reporting system', 'category' => 'management'],
            
            // Technical
            ['name' => 'view_jobs', 'display_name' => 'View Jobs', 'description' => 'View technical jobs/tickets', 'category' => 'technical'],
            ['name' => 'manage_terminals', 'display_name' => 'Manage Terminals', 'description' => 'Manage POS terminals', 'category' => 'technical'],
            ['name' => 'view_terminals', 'display_name' => 'View Terminals', 'description' => 'View POS terminals', 'category' => 'technical'],
            ['name' => 'update_terminals', 'display_name' => 'Update Terminals', 'description' => 'Update terminal information', 'category' => 'technical'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']], 
                $permission
            );
        }

        // Create Roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access',
                'permissions' => ['all']
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative access',
                'permissions' => ['view_dashboard', 'manage_assets', 'manage_clients', 'manage_team', 'view_reports', 'approve_requests']
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Team management access',
                'permissions' => ['view_dashboard', 'manage_team', 'view_clients', 'view_reports', 'approve_requests', 'view_own_data']
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Standard employee access',
                'permissions' => ['view_own_data', 'request_assets', 'view_clients']
            ],
            [
                'name' => 'technician',
                'display_name' => 'Technician',
                'description' => 'Technical staff access',
                'permissions' => ['view_jobs', 'view_terminals', 'update_terminals', 'view_own_data']
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']], 
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description']
                ]
            );

            // Attach permissions to role
            $permissionIds = Permission::whereIn('name', $roleData['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}