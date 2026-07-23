<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->string('new_contact_person', 255)->nullable()->after('phone_number');
            $table->string('new_phone_number', 50)->nullable()->after('new_contact_person');
            $table->string('new_physical_address', 500)->nullable()->after('new_phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['new_contact_person', 'new_phone_number', 'new_physical_address']);
        });
    }
};
