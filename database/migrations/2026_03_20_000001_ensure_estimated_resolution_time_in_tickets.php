<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tickets', 'estimated_resolution_time')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->integer('estimated_resolution_time')->nullable()->after('priority');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tickets', 'estimated_resolution_time')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('estimated_resolution_time');
            });
        }
    }
};
