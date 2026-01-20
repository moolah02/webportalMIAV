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
        Schema::table('technician_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('technician_visits', 'outcome')) {
                $table->enum('outcome', ['successful', 'failed', 'partial', 'rescheduled', 'cancelled'])
                    ->nullable()
                    ->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technician_visits', function (Blueprint $table) {
            $table->dropColumn('outcome');
        });
    }
};
