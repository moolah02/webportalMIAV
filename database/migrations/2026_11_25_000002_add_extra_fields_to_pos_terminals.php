<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gives a device somewhere to keep the columns a spreadsheet carried that the
 * system has no field for.
 *
 * The importer has always ended with `$data['extra_fields'] = ...` and the
 * API has always validated `extra_fields` as an array - but the column was
 * never created and it was never fillable, so Eloquent dropped it in silence.
 * Every unmapped column of every import so far has been discarded without a
 * word. The import page even counts them and tells the user they are being
 * kept.
 *
 * It matters more now: a status column from a bank's sheet is deliberately
 * not allowed onto the device record, and this is where it is kept instead,
 * under a `sheet_` prefix, as the bank's paperwork rather than a reading off
 * the device.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pos_terminals') || Schema::hasColumn('pos_terminals', 'extra_fields')) {
            return;
        }

        Schema::table('pos_terminals', function (Blueprint $table) {
            $table->json('extra_fields')
                ->nullable()
                ->after('status_info')
                ->comment('Columns an import carried that have no field of their own. Reference only, never the device state.');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pos_terminals') || ! Schema::hasColumn('pos_terminals', 'extra_fields')) {
            return;
        }

        Schema::table('pos_terminals', function (Blueprint $table) {
            $table->dropColumn('extra_fields');
        });
    }
};
