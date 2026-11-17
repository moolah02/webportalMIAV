<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Where the technician was standing when the visit was recorded.
    //
    // The tablet has been sending these with every visit as
    // coordinates: { latitude, longitude }, but the visits table had nowhere
    // to put them and validate() drops any key without a rule, so every
    // reading was thrown away on arrival. technician_visits already had the
    // columns, sitting empty, because nothing upstream kept the value.
    //
    // decimal(10,8) / (11,8) is the usual pair: latitude needs two digits
    // before the point, longitude three, and eight after gives about a
    // millimetre - far finer than a phone can fix a position.
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            if (! Schema::hasColumn('visits', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('condition_notes');
            }
            if (! Schema::hasColumn('visits', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            foreach (['latitude', 'longitude'] as $column) {
                if (Schema::hasColumn('visits', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
