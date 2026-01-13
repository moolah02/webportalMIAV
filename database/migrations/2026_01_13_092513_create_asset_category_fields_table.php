<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_category_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_category_id')->constrained('asset_categories')->onDelete('cascade');
            $table->string('field_name'); // e.g., 'license_plate', 'make', 'model'
            $table->string('field_label'); // e.g., 'License Plate Number'
            $table->enum('field_type', ['text', 'number', 'date', 'select', 'textarea', 'email', 'url', 'tel'])->default('text');
            $table->boolean('is_required')->default(false);
            $table->json('validation_rules')->nullable(); // e.g., {"min": 3, "max": 50, "regex": "..."}
            $table->json('options')->nullable(); // For select fields: ["Option 1", "Option 2"]
            $table->string('placeholder_text')->nullable();
            $table->string('help_text')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['asset_category_id', 'display_order']);
            $table->unique(['asset_category_id', 'field_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_category_fields');
    }
};
