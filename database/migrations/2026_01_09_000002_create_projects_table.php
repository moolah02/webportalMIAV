<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique();
            $table->string('project_name');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->string('project_type');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'paused', 'cancelled', 'closed', 'on_hold'])->default('active');
            $table->enum('priority', ['emergency', 'high', 'normal', 'low'])->default('normal');
            $table->decimal('budget', 15, 2)->nullable();
            $table->integer('estimated_terminals_count')->default(0);
            $table->integer('actual_terminals_count')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->foreignId('project_manager_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('previous_project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->text('insights_from_previous')->nullable();
            $table->json('terminal_selection_criteria')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('closure_reason')->nullable();
            $table->timestamp('report_generated_at')->nullable();
            $table->string('report_path')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('project_type');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
