<?php
// ==============================================
// 1. PERMISSIONS MIGRATION
// Run: php artisan make:migration create_permissions_table
// ==============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'manage_assets', 'view_dashboard'
            $table->string('display_name'); // e.g., 'Manage Assets', 'View Dashboard'
            $table->text('description')->nullable();
            $table->string('category')->default('general'); // e.g., 'assets', 'admin', 'reports'
            $table->timestamps();
            
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};