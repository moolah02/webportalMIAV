<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'permissions')) {
                // JSON column to store permissions (nullable for backward compatibility)
                $table->json('permissions')->nullable()->after('display_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'permissions')) {
                $table->dropColumn('permissions');
            }
        });
    }
};
