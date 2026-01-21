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
        Schema::table('employees', function (Blueprint $table) {
            // Add emergency contact relationship field
            if (!Schema::hasColumn('employees', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            }

            // Add work location field
            if (!Schema::hasColumn('employees', 'work_location')) {
                $table->string('work_location')->nullable()->after('emergency_contact_relationship');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['emergency_contact_relationship', 'work_location']);
        });
    }
};
