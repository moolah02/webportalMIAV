<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'industry')) {
                $table->string('industry')->nullable();
            }
            if (!Schema::hasColumn('clients', 'company_size')) {
                $table->string('company_size')->nullable();
            }
            if (!Schema::hasColumn('clients', 'annual_revenue')) {
                $table->decimal('annual_revenue', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('clients', 'contact_person')) {
                $table->string('contact_person')->nullable();
            }
            if (!Schema::hasColumn('clients', 'contact_title')) {
                $table->string('contact_title')->nullable();
            }
            if (!Schema::hasColumn('clients', 'contact_email')) {
                $table->string('contact_email')->nullable();
            }
            if (!Schema::hasColumn('clients', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
            if (!Schema::hasColumn('clients', 'status')) {
                $table->enum('status', ['active', 'inactive', 'prospect', 'lost'])->default('prospect');
            }
            if (!Schema::hasColumn('clients', 'priority')) {
                $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            }
            if (!Schema::hasColumn('clients', 'lead_source')) {
                $table->string('lead_source')->nullable();
            }
            if (!Schema::hasColumn('clients', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('clients', 'acquired_date')) {
                $table->date('acquired_date')->nullable();
            }
            if (!Schema::hasColumn('clients', 'last_contact_date')) {
                $table->date('last_contact_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $columns = ['industry', 'company_size', 'annual_revenue', 'contact_person',
                       'contact_title', 'contact_email', 'contact_phone', 'status',
                       'priority', 'lead_source', 'notes', 'acquired_date', 'last_contact_date'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};