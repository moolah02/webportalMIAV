<?php

// ==============================================
// 5. EMPLOYEE ROLES PIVOT TABLE
// Run: php artisan make:migration create_employee_roles_table
// ==============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['employee_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_roles');
    }
};
