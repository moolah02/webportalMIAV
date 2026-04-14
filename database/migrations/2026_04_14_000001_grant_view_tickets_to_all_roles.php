<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure view_tickets and view_jobs permissions exist
        $permissions = [
            'view_tickets' => [
                'display_name' => 'View Tickets',
                'description'  => 'View support tickets',
                'category'     => 'tickets',
            ],
            'view_jobs' => [
                'display_name' => 'View Jobs',
                'description'  => 'View job assignments',
                'category'     => 'jobs',
            ],
        ];

        $permissionIds = [];
        foreach ($permissions as $name => $data) {
            $permission = Permission::firstOrCreate(['name' => $name], $data);
            $permissionIds[] = $permission->id;
        }

        // Grant these permissions to ALL roles (Supervisor, Technician, Employee, etc.)
        $roles = Role::all();
        foreach ($roles as $role) {
            foreach ($permissionIds as $permissionId) {
                $exists = DB::table('role_permissions')
                    ->where('role_id', $role->id)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_permissions')->insert([
                        'role_id'       => $role->id,
                        'permission_id' => $permissionId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }

        echo "✓ Granted view_tickets and view_jobs to all roles\n";
    }

    public function down(): void
    {
        echo "⚠️  Down migration skipped — keeping permissions for safety\n";
    }
};
