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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index(); // asset_category, asset_status, terminal_status, service_type
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 20)->nullable(); // hex color code
            $table->string('icon', 50)->nullable(); // icon class or name
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable(); // additional flexible data
            $table->timestamps();

            // Composite index for common queries
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
