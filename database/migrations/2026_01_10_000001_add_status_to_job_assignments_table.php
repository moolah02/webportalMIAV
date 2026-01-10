<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('job_assignments', 'status')) {
                $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])
                      ->default('assigned')
                      ->after('technician_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('job_assignments', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
