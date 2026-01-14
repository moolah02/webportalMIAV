<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Get the technician role
        $technicianRole = Role::where('name', 'technician')
            ->orWhere('name', 'Technician')
            ->first();

        if ($technicianRole) {
            // Get current permissions array
            $currentPermissions = $technicianRole->permissions ?? [];

            // Add missing permissions
            $permissionsToAdd = ['view_own_data', 'view_assets'];

            foreach ($permissionsToAdd as $permission) {
                if (!in_array($permission, $currentPermissions)) {
                    $currentPermissions[] = $permission;
                }
            }

            // Update the role with new permissions
            $technicianRole->permissions = $currentPermissions;
            $technicianRole->save();
        }
    }

    public function down(): void
    {
        // Get the technician role
        $technicianRole = Role::where('name', 'technician')
            ->orWhere('name', 'Technician')
            ->first();

        if ($technicianRole) {
            // Get current permissions array
            $currentPermissions = $technicianRole->permissions ?? [];

            // Remove the permissions
            $permissionsToRemove = ['view_own_data', 'view_assets'];
            $currentPermissions = array_diff($currentPermissions, $permissionsToRemove);

            // Update the role
            $technicianRole->permissions = array_values($currentPermissions);
            $technicianRole->save();
        }
    }
};
