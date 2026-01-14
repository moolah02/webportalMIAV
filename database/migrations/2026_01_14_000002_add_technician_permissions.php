<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get or create technician role
        $technicianRole = Role::where('name', 'technician')->first();
        
        if (!$technicianRole) {
            return; // Exit if technician role doesn't exist
        }

        // Define all permissions needed for technician
        $permissionsNeeded = [
            'view_own_data' => [
                'display_name' => 'View Own Data',
                'description' => 'View own profile and dashboard data',
                'category' => 'general'
            ],
            'view_jobs' => [
                'display_name' => 'View Jobs',
                'description' => 'View assigned technical jobs and tickets',
                'category' => 'technical'
            ],
            'view_terminals' => [
                'display_name' => 'View Terminals',
                'description' => 'View POS terminals information',
                'category' => 'technical'
            ],
            'update_terminals' => [
                'display_name' => 'Update Terminals',
                'description' => 'Update POS terminal information',
                'category' => 'technical'
            ],
            'view_clients' => [
                'display_name' => 'View Clients',
                'description' => 'View client information',
                'category' => 'clients'
            ],
        ];

        // Create permissions and assign to technician role
        foreach ($permissionsNeeded as $permissionName => $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                $permissionData
            );

            // Check if permission is already assigned to technician role
            $exists = DB::table('role_permissions')
                ->where('role_id', $technicianRole->id)
                ->where('permission_id', $permission->id)
                ->exists();

            if (!$exists) {
                DB::table('role_permissions')->insert([
                    'role_id' => $technicianRole->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get technician role
        $technicianRole = Role::where('name', 'technician')->first();
        
        if (!$technicianRole) {
            return;
        }

        // Remove specific permissions from technician role
        $permissionNames = ['view_own_data', 'view_jobs', 'view_terminals', 'update_terminals', 'view_clients'];
        $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id');

        DB::table('role_permissions')
            ->where('role_id', $technicianRole->id)
            ->whereIn('permission_id', $permissionIds)
            ->delete();
    }
};
