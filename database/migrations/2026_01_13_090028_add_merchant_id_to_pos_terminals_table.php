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
        Schema::table('pos_terminals', function (Blueprint $table) {
            // Only add merchant_id if it doesn't exist
            if (!Schema::hasColumn('pos_terminals', 'merchant_id')) {
                $table->string('merchant_id')->nullable()->after('terminal_id');
            }
            // merchant_name already exists, skip it
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_terminals', function (Blueprint $table) {
            if (Schema::hasColumn('pos_terminals', 'merchant_id')) {
                $table->dropColumn('merchant_id');
            }
        });
    }
};
