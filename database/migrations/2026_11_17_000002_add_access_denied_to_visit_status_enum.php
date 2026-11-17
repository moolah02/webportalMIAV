<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a visit record that the technician was refused access to the device.
 *
 * Some clients never let the field team near a terminal, so the technician
 * cannot say whether it works, what condition it is in, or even read its
 * serial. Until now the only honest-ish choice on the tablet was "Not Found",
 * which says something different and wrong: that the device was missing.
 *
 * `visit_terminals.status` is a varchar and needs no change - it keeps the
 * technician's own words. This widens the two reporting enums the mirror
 * writes into, which would otherwise reject the row outright and drop the
 * visit out of every report.
 */
return new class extends Migration
{
    /** The value added, and the columns it is added to. */
    private const COLUMNS = [
        'technician_visits' => 'terminal_status_during_visit',
    ];

    private const EXISTING = ['active', 'inactive', 'not_found', 'relocated', 'replaced'];

    private const ADDED = 'access_denied';

    public function up(): void
    {
        foreach (self::COLUMNS as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            $values = $this->currentValues($table, $column);
            if ($values === null || in_array(self::ADDED, $values, true)) {
                continue; // Not an enum, or already widened - leave it alone.
            }

            $values[] = self::ADDED;
            $this->setValues($table, $column, $values);
        }
    }

    public function down(): void
    {
        foreach (self::COLUMNS as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            $values = $this->currentValues($table, $column);
            if ($values === null || ! in_array(self::ADDED, $values, true)) {
                continue;
            }

            // Rows already using the value would be truncated to '' by the
            // narrowing, so clear them first - they are unreportable either way.
            DB::table($table)->where($column, self::ADDED)->update([$column => null]);

            $this->setValues(
                $table,
                $column,
                array_values(array_diff($values, [self::ADDED])),
            );
        }
    }

    /**
     * The enum's values as the database currently defines them, so a column
     * that has drifted from the original migration is widened rather than
     * silently reset to what this file happens to think it holds.
     *
     * @return array<int, string>|null  null when the column is not an enum.
     */
    private function currentValues(string $table, string $column): ?array
    {
        $row = DB::selectOne(
            'SELECT COLUMN_TYPE AS column_type
               FROM INFORMATION_SCHEMA.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND COLUMN_NAME = ?',
            [$table, $column],
        );

        $type = $row->column_type ?? null;
        if (! is_string($type) || ! preg_match("/^enum\((.*)\)$/i", $type, $m)) {
            return null;
        }

        preg_match_all("/'((?:[^']|'')*)'/", $m[1], $found);
        $values = array_map(fn ($v) => str_replace("''", "'", $v), $found[1]);

        return $values ?: self::EXISTING;
    }

    /** @param  array<int, string>  $values */
    private function setValues(string $table, string $column, array $values): void
    {
        $quoted = implode(',', array_map(
            fn ($v) => "'".str_replace("'", "''", $v)."'",
            $values,
        ));

        // Kept nullable: a visit that never saw the device has no state, and
        // the mirror relies on being able to say so.
        DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` ENUM({$quoted}) NULL");
    }
};
