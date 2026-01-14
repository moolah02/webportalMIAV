<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        // Create the permission using custom Permission model
        $permission = Permission::firstOrCreate(
            ['name' => 'view_own_data'],
            [
                'display_name' => 'View Own Data',
                'description' => 'View own profile and dashboard data',
                'category' => 'general'
            ]
        );

        // Assign to all roles that need it
        $rolesToAssign = ['technician', 'employee', 'manager', 'supervisor'];

        foreach ($rolesToAssign as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                // Check if permission is already assigned to role
                $exists = DB::table('role_permissions')
                    ->where('role_id', $role->id)
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (!$exists) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'view_own_data')->first();
        if ($permission) {
            // Remove role associations
            DB::table('role_permissions')
                ->where('permission_id', $permission->id)
                ->delete();

            // Delete the permission
            $permission->delete();
        }
    }
};
