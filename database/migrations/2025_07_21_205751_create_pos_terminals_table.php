<?php

// ==============================================
// POS TERMINALS MIGRATION
// File: database/migrations/YYYY_MM_DD_XXXXXX_create_pos_terminals_table.php
// ==============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_terminals', function (Blueprint $table) {
            $table->id();
            $table->string('terminal_id')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('merchant_name');
            $table->string('merchant_contact_person')->nullable();
            $table->string('merchant_phone')->nullable();
            $table->string('merchant_email')->nullable();
            $table->text('physical_address')->nullable();
            $table->string('region')->nullable();
            $table->string('area')->nullable();
            $table->string('business_type')->nullable();
            $table->date('installation_date')->nullable();
            $table->string('terminal_model')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('contract_details')->nullable();
            $table->enum('status', ['active', 'offline', 'maintenance', 'faulty', 'decommissioned'])->default('active');
            $table->date('last_service_date')->nullable();
            $table->date('next_service_due')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('client_id');
            $table->index('status');
            $table->index('region');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_terminals');
    }
};