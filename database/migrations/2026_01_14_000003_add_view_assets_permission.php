<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Create view_assets permission
        $permission = Permission::firstOrCreate(
            ['name' => 'view_assets'],
            [
                'display_name' => 'View Assets',
                'description' => 'View company assets',
                'category' => 'assets'
            ]
        );

        // Assign to technician role
        $technicianRole = Role::where('name', 'technician')->first();

        if ($technicianRole) {
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

    public function down(): void
    {
        $permission = Permission::where('name', 'view_assets')->first();
        if ($permission) {
            $technicianRole = Role::where('name', 'technician')->first();
            if ($technicianRole) {
                DB::table('role_permissions')
                    ->where('role_id', $technicianRole->id)
                    ->where('permission_id', $permission->id)
                    ->delete();
            }
        }
    }
};
