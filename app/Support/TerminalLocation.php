<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Where a device is.
 *
 * The portal has three location fields and only two ideas behind them:
 *
 *   region / province - the same thing. The `regions` table holds Zimbabwe's
 *                       ten provinces, and that list is what the Region
 *                       dropdown offers. `region` is the one the system reads:
 *                       the device-list filter, the dashboard's regional
 *                       figures, job assignment and the mobile API all compare
 *                       against it. `province` is only read by deployment
 *                       planning's grouping.
 *   city              - the town (Gweru, Karoi, Mutare). A different thing.
 *
 * A sheet, or a person filling in the form, will name one of the first two and
 * not the other. Rather than leave the field the system reads empty, fill each
 * from the other and resolve `region_id` to match.
 */
final class TerminalLocation
{
    /** name (lowercased) => ['id' => int, 'name' => string]. Looked up once. */
    private static ?array $regions = null;

    /**
     * The regions as the dropdown offers them, keyed by lowercased name.
     */
    public static function regions(): array
    {
        if (self::$regions === null) {
            self::$regions = [];
            foreach (DB::table('regions')->select('id', 'name')->get() as $row) {
                $name = trim((string) $row->name);
                if ($name === '') {
                    continue;
                }
                self::$regions[mb_strtolower($name)] = ['id' => (int) $row->id, 'name' => $name];
            }
        }

        return self::$regions;
    }

    /**
     * Forget the cached list. For tests, and after seeding a new region.
     */
    public static function forget(): void
    {
        self::$regions = null;
    }

    /**
     * The region's name as the `regions` table spells it, or null if it is not
     * one of them. "Bulawayo Province" and "bulawayo" both give "Bulawayo",
     * so a sheet that writes the long form still lands on the dropdown's value
     * and the filters match.
     */
    public static function canonicalRegionName(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $regions = self::regions();

        foreach ([$value, preg_replace('/\s+province$/i', '', $value)] as $candidate) {
            $key = mb_strtolower(trim((string) $candidate));
            if (isset($regions[$key])) {
                return $regions[$key]['name'];
            }
        }

        return null;
    }

    /**
     * The id of the region with this name, or null if it is not one of them.
     */
    public static function regionId(?string $value): ?int
    {
        $name = self::canonicalRegionName($value);

        return $name === null ? null : self::regions()[mb_strtolower($name)]['id'];
    }

    /**
     * Fill region from province and province from region, and resolve
     * region_id. Only ever fills a blank - whatever was already set stays.
     *
     * A value that is not one of the ten is kept exactly as written rather
     * than discarded; it simply will not match the dropdown, and region_id
     * stays empty.
     */
    public static function pair(array &$data): void
    {
        $region = trim((string) ($data['region'] ?? ''));
        $province = trim((string) ($data['province'] ?? ''));

        // Whichever of the two the sheet gave, that is the location.
        $asWritten = $region !== '' ? $region : $province;

        if ($region === '' && $province !== '') {
            $region = $province;
        }

        // Store the name the dropdown offers. A sheet writes "Harare Province"
        // and a person types "harare"; both are the Harare region, and unless
        // they are written down the same way every filter, every grouping and
        // every count compares them as different places.
        $canonical = self::canonicalRegionName($region);
        if ($canonical !== null) {
            $region = $canonical;
        }

        if ($region !== '') {
            $data['region'] = $region;
        }

        // The province column keeps the sheet's own wording where it has none
        // of its own, so nothing about how the office writes it is lost.
        if ($province === '' && $asWritten !== '') {
            $data['province'] = $asWritten;
        }

        if ($region !== '' && empty($data['region_id'])) {
            $id = self::regionId($region);
            if ($id !== null) {
                $data['region_id'] = $id;
            }
        }
    }
}
