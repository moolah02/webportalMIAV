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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->unique();
            $table->boolean('mobile_created')->default(false);
            $table->string('offline_sync_id')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->unsignedBigInteger('pos_terminal_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->string('issue_type');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->integer('estimated_resolution_time')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('resolution')->nullable();
            $table->json('attachments')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('priority');
            $table->index('issue_type');
            $table->index('technician_id');
            $table->index('assigned_to');

            // Foreign keys
            if (Schema::hasTable('employees')) {
                $table->foreign('technician_id')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('assigned_to')->references('id')->on('employees')->onDelete('set null');
            }
            if (Schema::hasTable('pos_terminals')) {
                $table->foreign('pos_terminal_id')->references('id')->on('pos_terminals')->onDelete('cascade');
            }
            if (Schema::hasTable('clients')) {
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
