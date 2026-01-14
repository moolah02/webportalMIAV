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
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'company_name')) {
                $table->string('company_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('clients', 'region')) {
                $table->string('region')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('clients', 'contract_start_date')) {
                $table->date('contract_start_date')->nullable();
            }
            if (!Schema::hasColumn('clients', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
};
