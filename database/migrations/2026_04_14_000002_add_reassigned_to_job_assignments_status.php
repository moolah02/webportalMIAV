<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `job_assignments` MODIFY COLUMN `status` ENUM('assigned','in_progress','completed','cancelled','reassigned') DEFAULT 'assigned'");
    }

    public function down(): void
    {
        // Revert reassigned rows to cancelled before shrinking the enum
        DB::statement("UPDATE `job_assignments` SET `status` = 'cancelled' WHERE `status` = 'reassigned'");
        DB::statement("ALTER TABLE `job_assignments` MODIFY COLUMN `status` ENUM('assigned','in_progress','completed','cancelled') DEFAULT 'assigned'");
    }
};
