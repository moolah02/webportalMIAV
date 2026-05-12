<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create table if it was never properly migrated
        if (!Schema::hasTable('report_templates')) {
            Schema::create('report_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->json('tags')->nullable();
                $table->boolean('is_global')->default(false);
                $table->json('payload')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->foreign('created_by')->references('id')->on('employees')->onDelete('set null');
            });
            return;
        }

        Schema::table('report_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('report_templates', 'payload')) {
                $table->json('payload')->nullable()->after('is_global');
            }
            if (!Schema::hasColumn('report_templates', 'tags')) {
                $table->json('tags')->nullable()->after('description');
            }
            if (!Schema::hasColumn('report_templates', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('payload');
            }
        });
    }

    public function down(): void
    {
        Schema::table('report_templates', function (Blueprint $table) {
            foreach (['payload', 'tags', 'created_by'] as $col) {
                if (Schema::hasColumn('report_templates', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
