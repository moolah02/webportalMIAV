<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Add missing columns needed by ClientController
            if (!Schema::hasColumn('clients', 'client_code')) {
                $table->string('client_code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('clients', 'email')) {
                $table->string('email')->nullable()->after('contact_person');
            }
            if (!Schema::hasColumn('clients', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('clients', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('clients', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $columns = ['client_code', 'email', 'phone', 'address', 'city'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
