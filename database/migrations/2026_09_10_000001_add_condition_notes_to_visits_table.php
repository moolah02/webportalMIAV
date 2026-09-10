<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Free-text notes on the terminal's condition, captured on the tablet.
    // Mirrored to technician_visits.condition_notes for the report builder.
    public function up(): void
    {
        if (!Schema::hasColumn('visits', 'condition_notes')) {
            Schema::table('visits', function (Blueprint $table) {
                $table->text('condition_notes')->nullable()->after('terminal_comments');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('visits', 'condition_notes')) {
            Schema::table('visits', function (Blueprint $table) {
                $table->dropColumn('condition_notes');
            });
        }
    }
};
