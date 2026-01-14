<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Create report builder permissions if they don't exist
        $reportPermissions = [
            'use_report_builder' => [
                'display_name' => 'Use Report Builder',
                'description' => 'Access report builder interface',
                'category' => 'reports'
            ],
            'view_report_builder' => [
                'display_name' => 'View Report Builder',
                'description' => 'View report builder',
                'category' => 'reports'
            ],
            'view_reports' => [
                'display_name' => 'View Reports',
                'description' => 'View generated reports',
                'category' => 'reports'
            ],
        ];

        $createdPermissions = [];
        foreach ($reportPermissions as $name => $data) {
            $permission = Permission::firstOrCreate(
                ['name' => $name],
                $data
            );
            $createdPermissions[] = $permission->id;
        }

        // Assign these permissions to ALL roles
        $roles = Role::all();

        foreach ($roles as $role) {
            foreach ($createdPermissions as $permissionId) {
                // Check if relationship already exists
                $exists = DB::table('role_permissions')
                    ->where('role_id', $role->id)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        echo "✓ Granted report builder access to all roles\n";
    }

    public function down(): void
    {
        // Optionally remove report permissions from all roles
        echo "⚠️  Down migration skipped - keeping report permissions for safety\n";
    }
};
