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
        Schema::table('tickets', function (Blueprint $table) {
            // Add ticket_type: 'pos_terminal' or 'internal'
            $table->enum('ticket_type', ['pos_terminal', 'internal'])->default('pos_terminal')->after('ticket_id');

            // Add assignment_type: 'public' (any employee can attend) or 'direct' (assigned to specific employee)
            $table->enum('assignment_type', ['public', 'direct'])->default('public')->after('ticket_type');

            // Add index for faster filtering
            $table->index('ticket_type');
            $table->index('assignment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['ticket_type']);
            $table->dropIndex(['assignment_type']);
            $table->dropColumn(['ticket_type', 'assignment_type']);
        });
    }
};
