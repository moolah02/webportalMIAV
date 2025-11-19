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
            $table->string('company_name')->nullable()->after('id');
            $table->string('client_code')->nullable()->after('company_name');
            $table->string('email')->nullable()->after('contact_email');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('region')->nullable()->after('city');
            $table->date('contract_start_date')->nullable()->after('acquired_date');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
        });

        // Copy data from 'name' to 'company_name'
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
