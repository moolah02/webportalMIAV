<?php

// ==============================================
// 1. ASSETS MIGRATION
// ==============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // 'Hardware', 'Software', 'Office Supplies', etc.
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
            $table->string('barcode')->nullable();
            $table->json('specifications')->nullable(); // JSON for flexible specs
            $table->string('image_url')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->boolean('is_requestable')->default(true);
            $table->boolean('requires_approval')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('category');
            $table->index('status');
            $table->index('is_requestable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};