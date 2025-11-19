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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique();
            $table->string('project_name');
            $table->unsignedBigInteger('client_id');
            $table->string('project_type');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['planning', 'active', 'paused', 'completed', 'cancelled', 'closed', 'on_hold'])->default('planning');
            $table->enum('priority', ['low', 'normal', 'high', 'emergency'])->default('normal');
            $table->decimal('budget', 12, 2)->nullable();
            $table->integer('estimated_terminals_count')->nullable();
            $table->integer('actual_terminals_count')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->unsignedBigInteger('project_manager_id')->nullable();
            $table->unsignedBigInteger('previous_project_id')->nullable();
            $table->text('insights_from_previous')->nullable();
            $table->json('terminal_selection_criteria')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->datetime('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->string('closure_reason')->nullable();
            $table->datetime('report_generated_at')->nullable();
            $table->string('report_path')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('client_id');
            $table->index('status');
            $table->index('priority');
            $table->index('project_type');
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['client_id', 'status']);

            // Foreign keys
            if (Schema::hasTable('clients')) {
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            }
            if (Schema::hasTable('employees')) {
                $table->foreign('project_manager_id')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('completed_by')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('closed_by')->references('id')->on('employees')->onDelete('set null');
            }
            $table->foreign('previous_project_id')->references('id')->on('projects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
