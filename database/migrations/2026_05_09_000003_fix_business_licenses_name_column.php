<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The production table has a `name` column (NOT NULL, no default) left over from
        // the original schema before the rename to `license_name`. Every insert fails because
        // the code never populates `name`. Make it nullable so existing rows and new inserts work.
        if (Schema::hasColumn('business_licenses', 'name')) {
            DB::statement('ALTER TABLE business_licenses MODIFY name VARCHAR(255) NULL DEFAULT NULL');
        }
    }

    public function down(): void {}
};
