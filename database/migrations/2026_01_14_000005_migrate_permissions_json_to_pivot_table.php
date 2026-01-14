<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Get all roles
        $roles = Role::all();

        foreach ($roles as $role) {
            // Get permissions from JSON column
            $permissionNames = $role->getRawOriginal('permissions');

            if (is_string($permissionNames)) {
                $permissionNames = json_decode($permissionNames, true);
            }

            if (!is_array($permissionNames) || empty($permissionNames)) {
                continue;
            }

            // Get permission IDs
            $permissions = Permission::whereIn('name', $permissionNames)->get();

            foreach ($permissions as $permission) {
                // Check if relationship already exists
                $exists = DB::table('role_permissions')
                    ->where('role_id', $role->id)
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (!$exists) {
                    // Insert into pivot table
                    DB::table('role_permissions')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        // Create missing permissions if they don't exist
        $this->ensurePermissionsExist();
    }

    public function down(): void
    {
        // Optionally clear the pivot table
        // DB::table('role_permissions')->truncate();
    }

    /**
     * Ensure all common permissions exist in the database
     */
    private function ensurePermissionsExist(): void
    {
        $commonPermissions = [
            ['name' => 'all', 'display_name' => 'All Permissions', 'description' => 'Full system access', 'category' => 'admin'],
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'description' => 'Access main dashboard', 'category' => 'general'],
            ['name' => 'view_own_data', 'display_name' => 'View Own Data', 'description' => 'View personal information', 'category' => 'general'],
            ['name' => 'manage_assets', 'display_name' => 'Manage Assets', 'description' => 'Full asset management', 'category' => 'assets'],
            ['name' => 'view_assets', 'display_name' => 'View Assets', 'description' => 'View company assets', 'category' => 'assets'],
            ['name' => 'request_assets', 'display_name' => 'Request Assets', 'description' => 'Request company assets', 'category' => 'assets'],
            ['name' => 'approve_requests', 'display_name' => 'Approve Requests', 'description' => 'Approve asset requests', 'category' => 'assets'],
            ['name' => 'view_clients', 'display_name' => 'View Clients', 'description' => 'View client information', 'category' => 'clients'],
            ['name' => 'manage_clients', 'display_name' => 'Manage Clients', 'description' => 'Full client management', 'category' => 'clients'],
            ['name' => 'manage_team', 'display_name' => 'Manage Team', 'description' => 'Manage team members', 'category' => 'management'],
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'description' => 'Access reporting system', 'category' => 'management'],
            ['name' => 'view_jobs', 'display_name' => 'View Jobs', 'description' => 'View technical jobs/tickets', 'category' => 'technical'],
            ['name' => 'update_jobs', 'display_name' => 'Update Jobs', 'description' => 'Update job information', 'category' => 'technical'],
            ['name' => 'manage_terminals', 'display_name' => 'Manage Terminals', 'description' => 'Manage POS terminals', 'category' => 'technical'],
            ['name' => 'view_terminals', 'display_name' => 'View Terminals', 'description' => 'View POS terminals', 'category' => 'technical'],
            ['name' => 'update_terminals', 'display_name' => 'Update Terminals', 'description' => 'Update terminal information', 'category' => 'technical'],
            ['name' => 'create_reports', 'display_name' => 'Create Reports', 'description' => 'Create new reports', 'category' => 'technical'],
        ];

        foreach ($commonPermissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }
    }
};
