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
        Schema::create('job_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('assignment_id')->unique();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->json('pos_terminals')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->date('scheduled_date');
            $table->string('service_type')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'emergency'])->default('normal');
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])->default('assigned');
            $table->text('notes')->nullable();
            $table->decimal('estimated_duration_hours', 5, 2)->nullable();
            $table->datetime('actual_start_time')->nullable();
            $table->datetime('actual_end_time')->nullable();
            $table->text('completion_notes')->nullable();
            $table->date('completed_date')->nullable();
            $table->json('assignment_history')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Add foreign keys only if the referenced tables exist
            if (Schema::hasTable('employees')) {
                $table->foreign('technician_id')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('employees')->onDelete('set null');
            }
            if (Schema::hasTable('regions')) {
                $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
            }
            if (Schema::hasTable('clients')) {
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            }
            if (Schema::hasTable('projects')) {
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            }

            // Indexes for better query performance
            $table->index('technician_id');
            $table->index('status');
            $table->index('scheduled_date');
            $table->index(['technician_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_assignments');
    }
};
