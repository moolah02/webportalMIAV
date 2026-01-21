<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create the pivot table
        Schema::create('employee_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            // Prevent duplicate assignments
            $table->unique(['employee_id', 'role_id']);

            // Indexes for performance
            $table->index('employee_id');
            $table->index('role_id');
        });

        // Migrate existing data from employees.role_id to pivot table
        DB::statement('
            INSERT INTO employee_role (employee_id, role_id, created_at, updated_at)
            SELECT id, role_id, NOW(), NOW()
            FROM employees
            WHERE role_id IS NOT NULL
        ');

        echo "✓ Migrated existing role assignments to pivot table\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_role');
    }
};
