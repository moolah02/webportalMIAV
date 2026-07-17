<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'company_name')) {
                $table->string('company_name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('clients', 'client_code')) {
                $table->string('client_code')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('clients', 'email')) {
                $table->string('email')->nullable()->after('contact_email');
            }
            if (!Schema::hasColumn('clients', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('clients', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('clients', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('clients', 'region')) {
                $table->string('region')->nullable()->after('city');
            }
            if (!Schema::hasColumn('clients', 'contract_start_date')) {
                $table->date('contract_start_date')->nullable()->after('acquired_date');
            }
            if (!Schema::hasColumn('clients', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable()->after('contract_start_date');
            }
        });

        DB::statement('UPDATE clients SET company_name = name WHERE company_name IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'client_code',
                'email',
                'phone',
                'address',
                'city',
                'region',
                'contract_start_date',
                'contract_end_date'
            ]);
        });
    }
};
