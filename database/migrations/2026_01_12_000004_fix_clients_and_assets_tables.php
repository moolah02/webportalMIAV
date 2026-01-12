<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix clients table - make name nullable
        Schema::table('clients', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
        });

        // Fix assets table - add assigned_quantity
        if (!Schema::hasColumn('assets', 'assigned_quantity')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->integer('assigned_quantity')->default(0)->after('stock_quantity');
            });
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });

        if (Schema::hasColumn('assets', 'assigned_quantity')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('assigned_quantity');
            });
        }
    }
};
