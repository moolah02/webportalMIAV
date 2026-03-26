<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open','in_progress','pending','resolved','closed','cancelled') NOT NULL DEFAULT 'open'");
    }

    public function down(): void
    {
        // Revert rows that would be invalid before shrinking enum
        DB::statement("UPDATE tickets SET status = 'open' WHERE status IN ('pending','cancelled')");
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open'");
    }
};
