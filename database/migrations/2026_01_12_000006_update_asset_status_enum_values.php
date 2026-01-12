<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update the status ENUM to include more realistic asset statuses
        DB::statement("ALTER TABLE assets MODIFY COLUMN status ENUM('active','inactive','discontinued','available','in-use','under-repair','maintenance','retired') DEFAULT 'available'");
    }

    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE assets MODIFY COLUMN status ENUM('active','inactive','discontinued') DEFAULT 'active'");
    }
};
