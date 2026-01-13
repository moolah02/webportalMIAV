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
        Schema::table('project_terminals', function (Blueprint $table) {
            // Add is_active column with default value of true
            if (!Schema::hasColumn('project_terminals', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('terminal_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_terminals', function (Blueprint $table) {
            if (Schema::hasColumn('project_terminals', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
