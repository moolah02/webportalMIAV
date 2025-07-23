<?php

// ==============================================
// 4. TECHNICIANS MIGRATION
// File: database/migrations/2024_01_02_000004_create_technicians_table.php
// ==============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('employee_code')->nullable();
            $table->json('specializations')->nullable();
            $table->json('regions')->nullable(); // Array of region IDs they cover
            $table->enum('availability_status', ['available', 'busy', 'off_duty'])->default('available');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('hire_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};