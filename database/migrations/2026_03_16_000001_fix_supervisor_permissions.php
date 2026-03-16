<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Permissions Supervisor needs to access the failing areas:
        // - view_jobs / manage_jobs  → Field Operations / Job Assignments
        // - view_clients             → Client Management
        // - view_client_dashboards   → Client Dashboard pages
        // - view_visits / manage_visits → Technician site visits
        // - view_terminals           → POS terminal data
        $needed = [
            'view_jobs',
            'manage_jobs',
            'view_clients',
            'view_client_dashboards',
            'view_visits',
            'view_terminals',
        ];

        // Find the Supervisor role (case-insensitive)
        $supervisorRole = DB::table('roles')
            ->whereRaw('LOWER(name) = ?', ['supervisor'])
            ->first();

        if (!$supervisorRole) {
            return; // nothing to do
        }

        foreach ($needed as $permName) {
            // Find the permission (create it if missing)
            $perm = DB::table('permissions')->where('name', $permName)->first();

            if (!$perm) {
                $permId = DB::table('permissions')->insertGetId([
                    'name'         => $permName,
                    'display_name' => ucwords(str_replace('_', ' ', $permName)),
                    'description'  => 'Auto-created by supervisor permissions migration',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            } else {
                $permId = $perm->id;
            }

            // Attach to supervisor role if not already attached
            $exists = DB::table('role_permissions')
                ->where('role_id', $supervisorRole->id)
                ->where('permission_id', $permId)
                ->exists();

            if (!$exists) {
                DB::table('role_permissions')->insert([
                    'role_id'       => $supervisorRole->id,
                    'permission_id' => $permId,
                ]);
            }
        }
    }

    public function down(): void
    {
        // No rollback — permissions are additive and safe to leave
    }
};
