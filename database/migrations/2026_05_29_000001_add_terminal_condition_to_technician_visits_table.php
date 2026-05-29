<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('technician_visits', 'terminal_condition')) {
            Schema::table('technician_visits', function (Blueprint $table) {
                $table->string('terminal_condition')->nullable()->after('terminal_status_during_visit');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('technician_visits', 'terminal_condition')) {
            Schema::table('technician_visits', function (Blueprint $table) {
                $table->dropColumn('terminal_condition');
            });
        }
    }
};
