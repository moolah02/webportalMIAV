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
        Schema::create('asset_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('related_asset_id')->constrained('assets')->onDelete('cascade');
            $table->enum('relationship_type', [
                'has_insurance',
                'has_license',
                'has_permit',
                'requires',
                'depends_on',
                'linked_to',
                'attached_to'
            ])->default('linked_to');
            $table->json('metadata')->nullable(); // Relationship-specific data
            $table->date('starts_at')->nullable(); // When relationship became active
            $table->date('expires_at')->nullable(); // When relationship expires
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['parent_asset_id', 'is_active']);
            $table->index(['related_asset_id', 'is_active']);
            $table->index('expires_at'); // For expiry tracking

            // Prevent duplicate relationships
            $table->unique(['parent_asset_id', 'related_asset_id', 'relationship_type'], 'asset_relationship_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_relationships');
    }
};
