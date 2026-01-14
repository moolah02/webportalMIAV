<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get the Technician role ID
        $technicianRole = DB::table('roles')
            ->where('name', 'LIKE', '%technician%')
            ->orWhere('name', 'LIKE', '%Technician%')
            ->first();

        if (!$technicianRole) {
            echo "⚠️  Warning: Technician role not found. Please create it first.\n";
            return;
        }

        // Assign Technician role to ALL employees who don't already have it
        $affectedRows = DB::statement("
            INSERT IGNORE INTO employee_role (employee_id, role_id, created_at, updated_at)
            SELECT id, ?, NOW(), NOW()
            FROM employees
            WHERE NOT EXISTS (
                SELECT 1
                FROM employee_role
                WHERE employee_role.employee_id = employees.id
                AND employee_role.role_id = ?
            )
        ", [$technicianRole->id, $technicianRole->id]);

        // Count how many employees now have technician role
        $count = DB::table('employee_role')
            ->where('role_id', $technicianRole->id)
            ->count();

        echo "✓ Assigned Technician role to employees (Total with Technician: {$count})\n";
        echo "✓ All employees can now handle jobs and technical work\n";
    }

    public function down(): void
    {
        // Optionally remove technician role assignments
        // We don't do this by default to be safe
        echo "⚠️  Down migration skipped - keeping technician role assignments for safety\n";
    }
};
