<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            // Add assignment_date (if not exists)
            if (!Schema::hasColumn('asset_assignments', 'assignment_date')) {
                $table->date('assignment_date')->after('employee_id')->nullable();
            }

            // Add condition_when_assigned
            if (!Schema::hasColumn('asset_assignments', 'condition_when_assigned')) {
                $table->enum('condition_when_assigned', ['new', 'good', 'fair', 'poor'])
                    ->default('good')
                    ->after('quantity_assigned');
            }

            // Add assigned_by (who created the assignment)
            if (!Schema::hasColumn('asset_assignments', 'assigned_by')) {
                $table->unsignedBigInteger('assigned_by')->nullable()->after('employee_id');
                $table->foreign('assigned_by')->references('id')->on('employees')->onDelete('set null');
            }

            // Add assignment_notes
            if (!Schema::hasColumn('asset_assignments', 'assignment_notes')) {
                $table->text('assignment_notes')->nullable()->after('notes');
            }

            // Add condition_when_returned
            if (!Schema::hasColumn('asset_assignments', 'condition_when_returned')) {
                $table->enum('condition_when_returned', ['new', 'good', 'fair', 'poor'])
                    ->nullable()
                    ->after('condition_when_assigned');
            }

            // Add returned_to (who processed the return)
            if (!Schema::hasColumn('asset_assignments', 'returned_to')) {
                $table->unsignedBigInteger('returned_to')->nullable()->after('actual_return_date');
                $table->foreign('returned_to')->references('id')->on('employees')->onDelete('set null');
            }

            // Add return_notes
            if (!Schema::hasColumn('asset_assignments', 'return_notes')) {
                $table->text('return_notes')->nullable()->after('assignment_notes');
            }
        });

        // Migrate existing data: copy assigned_at to assignment_date
        DB::statement('UPDATE asset_assignments SET assignment_date = DATE(assigned_at) WHERE assignment_date IS NULL AND assigned_at IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
            $table->dropForeign(['returned_to']);
            $table->dropColumn([
                'assignment_date',
                'condition_when_assigned',
                'assigned_by',
                'assignment_notes',
                'condition_when_returned',
                'returned_to',
                'return_notes'
            ]);
        });
    }
};
