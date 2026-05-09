<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_terminals', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_terminals', 'current_status')) {
                $table->string('current_status')->nullable()->after('status');
            }
            if (!Schema::hasColumn('pos_terminals', 'status_info')) {
                $table->json('status_info')->nullable()->after('current_status');
            }
            if (!Schema::hasColumn('pos_terminals', 'coordinates')) {
                $table->json('coordinates')->nullable()->after('status_info');
            }
            if (!Schema::hasColumn('pos_terminals', 'last_updated_by')) {
                $table->unsignedBigInteger('last_updated_by')->nullable()->after('coordinates');
            }
            if (!Schema::hasColumn('pos_terminals', 'region_id')) {
                $table->unsignedBigInteger('region_id')->nullable()->after('region');
            }
            if (!Schema::hasColumn('pos_terminals', 'city')) {
                $table->string('city')->nullable()->after('region_id');
            }
            if (!Schema::hasColumn('pos_terminals', 'province')) {
                $table->string('province')->nullable()->after('city');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pos_terminals', function (Blueprint $table) {
            $cols = ['status_info', 'coordinates', 'current_status', 'last_updated_by', 'region_id', 'city', 'province'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('pos_terminals', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
