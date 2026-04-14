<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_runs', function (Blueprint $table) {
            $table->string('action')->default('preview')->after('executed_at');  // preview | export
            $table->string('format')->nullable()->after('action');               // csv | pdf | null
            $table->string('ip_address', 45)->nullable()->after('format');
            $table->text('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('report_runs', function (Blueprint $table) {
            $table->dropColumn(['action', 'format', 'ip_address', 'user_agent']);
        });
    }
};
