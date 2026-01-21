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
        // Change estimated_resolution_time to store days instead of minutes
        Schema::table('tickets', function (Blueprint $table) {
            // Drop the old column and add new one with comment
            $table->dropColumn('estimated_resolution_time');
            $table->integer('estimated_resolution_days')->nullable()->after('assignment_type')->comment('Estimated resolution time in days');
        });

        // Create ticket_steps table for tracking staged resolution
        Schema::create('ticket_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('restrict');
            $table->integer('step_number')->default(1);
            $table->string('status')->default('in_progress'); // in_progress, completed, transferred, resolved
            $table->text('description')->comment('What work was done in this step');
            $table->text('notes')->nullable()->comment('Additional notes about this step');
            $table->text('resolution_notes')->nullable()->comment('What was resolved in this step');
            $table->text('transferred_reason')->nullable()->comment('Why was this transferred to next person');
            $table->foreignId('transferred_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes for fast queries
            $table->index('ticket_id');
            $table->index('employee_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropTableIfExists('ticket_steps');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('estimated_resolution_days');
            $table->integer('estimated_resolution_time')->nullable()->after('assignment_type');
        });
    }
};
