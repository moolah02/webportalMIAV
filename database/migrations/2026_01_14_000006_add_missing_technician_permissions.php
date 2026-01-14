<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Create missing permissions
        $updateJobsPerm = Permission::firstOrCreate(
            ['name' => 'update_jobs'],
            [
                'display_name' => 'Update Jobs',
                'description' => 'Update job information',
                'category' => 'technical'
            ]
        );

        $createReportsPerm = Permission::firstOrCreate(
            ['name' => 'create_reports'],
            [
                'display_name' => 'Create Reports',
                'description' => 'Create new reports',
                'category' => 'technical'
            ]
        );

        // Add to technician role
        $techRole = Role::where('name', 'Technician')
            ->orWhere('name', 'technician')
            ->first();

        if ($techRole) {
            // Add update_jobs permission
            if (!DB::table('role_permissions')
                    ->where('role_id', $techRole->id)
                    ->where('permission_id', $updateJobsPerm->id)
                    ->exists()) {
                DB::table('role_permissions')->insert([
                    'role_id' => $techRole->id,
                    'permission_id' => $updateJobsPerm->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Add create_reports permission
            if (!DB::table('role_permissions')
                    ->where('role_id', $techRole->id)
                    ->where('permission_id', $createReportsPerm->id)
                    ->exists()) {
                DB::table('role_permissions')->insert([
                    'role_id' => $techRole->id,
                    'permission_id' => $createReportsPerm->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    public function down(): void
    {
        $techRole = Role::where('name', 'Technician')
            ->orWhere('name', 'technician')
            ->first();

        if ($techRole) {
            $permissionIds = Permission::whereIn('name', ['update_jobs', 'create_reports'])->pluck('id');

            DB::table('role_permissions')
                ->where('role_id', $techRole->id)
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }
    }
};
