<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a device record say "nobody has been to this one yet".
 *
 * `pos_terminals.status` was enum(active, offline, maintenance, faulty,
 * decommissioned) NOT NULL DEFAULT 'active'. Every device created from the
 * office - imported from a bank's spreadsheet or added by hand - therefore
 * arrived claiming to be working, before any technician had seen it. That is
 * the office asserting a device status, which is exactly what it must never
 * do: a device's state is what a field visit found.
 *
 * `unseen` is appended and becomes the default, so a freshly loaded device
 * holds the absence of a reading rather than a false one. The first visit
 * writes `current_status`, and PosTerminal::EFFECTIVE_STATUS_SQL already
 * prefers that over this column.
 *
 * APPENDED, never reordered. MySQL stores an enum as the ordinal position of
 * its value, so inserting 'unseen' at the front would silently turn every
 * existing 'active' row into 'unseen'.
 */
return new class extends Migration
{
    private const NEW_VALUE = 'unseen';

    public function up(): void
    {
        if (! Schema::hasTable('pos_terminals')) {
            return;
        }

        $values = $this->currentEnumValues();
        if ($values === [] || in_array(self::NEW_VALUE, $values, true)) {
            return;
        }

        $values[] = self::NEW_VALUE;
        $this->setEnum($values, self::NEW_VALUE);
    }

    public function down(): void
    {
        if (! Schema::hasTable('pos_terminals')) {
            return;
        }

        $values = $this->currentEnumValues();
        if (! in_array(self::NEW_VALUE, $values, true)) {
            return;
        }

        // Refuse to drop a value rows still hold; that would turn them into ''.
        $inUse = DB::table('pos_terminals')->where('status', self::NEW_VALUE)->count();
        if ($inUse > 0) {
            throw new RuntimeException(
                "Cannot remove the '".self::NEW_VALUE."' status: {$inUse} device(s) still hold it. "
                .'Give those devices a real status first.'
            );
        }

        $values = array_values(array_filter($values, fn ($v) => $v !== self::NEW_VALUE));
        $this->setEnum($values, in_array('active', $values, true) ? 'active' : $values[0]);
    }

    /** The enum's values as the database currently defines them. */
    private function currentEnumValues(): array
    {
        $type = DB::selectOne(
            'SELECT COLUMN_TYPE AS t FROM INFORMATION_SCHEMA.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['pos_terminals', 'status']
        );

        if (! $type || ! preg_match('/^enum\((.*)\)$/i', $type->t, $m)) {
            return [];
        }

        return array_map(
            fn ($v) => str_replace("''", "'", trim($v, "'")),
            str_getcsv($m[1], ',', "'")
        );
    }

    private function setEnum(array $values, string $default): void
    {
        $quoted = implode(',', array_map(fn ($v) => "'".str_replace("'", "''", $v)."'", $values));
        DB::statement(
            "ALTER TABLE `pos_terminals` MODIFY `status` ENUM($quoted) NOT NULL DEFAULT '"
            .str_replace("'", "''", $default)."'"
        );
    }
};
