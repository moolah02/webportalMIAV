<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the permission
        $permission = Permission::firstOrCreate(
            ['name' => 'view_own_data'],
            [
                'guard_name' => 'web',
                'description' => 'View own profile and dashboard data'
            ]
        );

        // Assign to all roles except maybe admin (admin has all permissions anyway)
        $rolesToAssign = ['technician', 'employee', 'manager', 'supervisor'];

        foreach ($rolesToAssign as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role && !$role->hasPermissionTo('view_own_data')) {
                $role->givePermissionTo('view_own_data');
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
            $permission->delete();
        }
    }
};
