<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technician_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('technician_visits', 'job_assignment_id')) {
                $table->unsignedBigInteger('job_assignment_id')->nullable()->after('pos_terminal_id');
            }
            if (!Schema::hasColumn('technician_visits', 'started_at')) {
                $table->datetime('started_at')->nullable()->after('job_assignment_id');
            }
            if (!Schema::hasColumn('technician_visits', 'ended_at')) {
                $table->datetime('ended_at')->nullable()->after('started_at');
            }
            if (!Schema::hasColumn('technician_visits', 'terminal_status_during_visit')) {
                $table->string('terminal_status_during_visit')->nullable()->after('ended_at');
            }
            if (!Schema::hasColumn('technician_visits', 'condition_notes')) {
                $table->text('condition_notes')->nullable()->after('terminal_status_during_visit');
            }
            if (!Schema::hasColumn('technician_visits', 'issues_found')) {
                $table->text('issues_found')->nullable()->after('condition_notes');
            }
            if (!Schema::hasColumn('technician_visits', 'corrective_action')) {
                $table->text('corrective_action')->nullable()->after('issues_found');
            }
            if (!Schema::hasColumn('technician_visits', 'visit_summary')) {
                $table->text('visit_summary')->nullable()->after('corrective_action');
            }
            if (!Schema::hasColumn('technician_visits', 'visit_id')) {
                $table->string('visit_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('technician_visits', function (Blueprint $table) {
            $table->dropColumn(['job_assignment_id', 'started_at', 'ended_at', 'terminal_status_during_visit', 'condition_notes', 'issues_found', 'corrective_action', 'visit_summary', 'visit_id']);
        });
    }
};
