<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Make employee_id nullable to allow creation without explicit value
            $table->string('employee_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Revert to NOT NULL if needed
            $table->string('employee_id')->nullable(false)->change();
        });
    }
};
