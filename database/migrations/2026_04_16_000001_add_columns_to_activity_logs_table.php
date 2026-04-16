<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('id');
            $table->string('action', 100)->after('employee_id');          // e.g. 'approved', 'rejected', 'created'
            $table->string('model_type', 100)->nullable()->after('action'); // e.g. 'AssetRequest', 'Asset'
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            $table->string('description')->after('model_id');
            $table->json('old_values')->nullable()->after('description');
            $table->json('new_values')->nullable()->after('old_values');
            $table->string('ip_address', 45)->nullable()->after('new_values');
            $table->string('user_agent')->nullable()->after('ip_address');

            $table->index(['employee_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn([
                'employee_id', 'action', 'model_type', 'model_id',
                'description', 'old_values', 'new_values', 'ip_address', 'user_agent',
            ]);
        });
    }
};
