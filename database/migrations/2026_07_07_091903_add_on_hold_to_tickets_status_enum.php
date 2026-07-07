<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open','in_progress','on_hold','pending','resolved','closed','cancelled') NOT NULL DEFAULT 'open'");
    }

    public function down(): void
    {
        DB::statement("UPDATE tickets SET status = 'in_progress' WHERE status = 'on_hold'");
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open','in_progress','pending','resolved','closed','cancelled') NOT NULL DEFAULT 'open'");
    }
};
