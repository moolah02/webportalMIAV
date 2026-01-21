<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->boolean('requires_individual_entry')->default(false)->after('is_active');
        });

        // Set requires_individual_entry = true for Vehicles and IT Equipment
        DB::table('asset_categories')
            ->whereIn('name', ['Vehicles', 'IT Equipment', 'Computer and IT Equipment'])
            ->update(['requires_individual_entry' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn('requires_individual_entry');
        });
    }
};
