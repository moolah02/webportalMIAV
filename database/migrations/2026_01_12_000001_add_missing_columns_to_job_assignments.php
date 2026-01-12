<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('job_assignments', 'assignment_id')) {
                $table->string('assignment_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('job_assignments', 'region_id')) {
                $table->unsignedBigInteger('region_id')->nullable()->after('technician_id');
            }
            if (!Schema::hasColumn('job_assignments', 'pos_terminals')) {
                $table->json('pos_terminals')->nullable()->after('region_id');
            }
            if (!Schema::hasColumn('job_assignments', 'client_id')) {
                $table->unsignedBigInteger('client_id')->nullable()->after('pos_terminals');
            }
            if (!Schema::hasColumn('job_assignments', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('job_assignments', 'scheduled_date')) {
                $table->date('scheduled_date')->nullable()->after('project_id');
            }
            if (!Schema::hasColumn('job_assignments', 'service_type')) {
                $table->string('service_type')->nullable()->after('scheduled_date');
            }
            if (!Schema::hasColumn('job_assignments', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'emergency'])->default('normal')->after('service_type');
            }
            if (!Schema::hasColumn('job_assignments', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('job_assignments', 'estimated_duration_hours')) {
                $table->decimal('estimated_duration_hours', 5, 2)->nullable()->after('notes');
            }
            if (!Schema::hasColumn('job_assignments', 'actual_start_time')) {
                $table->dateTime('actual_start_time')->nullable()->after('estimated_duration_hours');
            }
            if (!Schema::hasColumn('job_assignments', 'actual_end_time')) {
                $table->dateTime('actual_end_time')->nullable()->after('actual_start_time');
            }
            if (!Schema::hasColumn('job_assignments', 'completion_notes')) {
                $table->text('completion_notes')->nullable()->after('actual_end_time');
            }
            if (!Schema::hasColumn('job_assignments', 'completed_date')) {
                $table->date('completed_date')->nullable()->after('completion_notes');
            }
            if (!Schema::hasColumn('job_assignments', 'assignment_history')) {
                $table->json('assignment_history')->nullable()->after('completed_date');
            }
            if (!Schema::hasColumn('job_assignments', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('assignment_history');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            $columns = [
                'assignment_id', 'region_id', 'pos_terminals', 'client_id', 'project_id',
                'scheduled_date', 'service_type', 'priority', 'notes', 'estimated_duration_hours',
                'actual_start_time', 'actual_end_time', 'completion_notes', 'completed_date',
                'assignment_history', 'created_by'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('job_assignments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
