<?php

namespace App\Support;

/**
 * Reading and showing the GPS positions the tablets capture.
 *
 * Two shapes arrive from the field: a visit carries latitude and longitude as
 * their own columns, while a terminal keeps them in a JSON `coordinates`
 * column. Both end up here so every page shows a position the same way and
 * links to the same map.
 */
class Geo
{
    /**
     * Is this pair a position someone can actually be shown?
     *
     * Zero/zero is rejected: the tablet sends it when no reading was taken,
     * and the point itself is open ocean off West Africa.
     */
    public static function has($lat, $lng): bool
    {
        if ($lat === null || $lng === null || $lat === '' || $lng === '') {
            return false;
        }

        if (! is_numeric($lat) || ! is_numeric($lng)) {
            return false;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        if (abs($lat) < 0.00001 && abs($lng) < 0.00001) {
            return false;
        }

        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180;
    }

    /**
     * The pair written out for reading, to six decimals - about 10 cm, which is
     * finer than any phone can fix a position, and short enough to read aloud
     * over the phone to someone standing in the field.
     */
    public static function format($lat, $lng): ?string
    {
        if (! self::has($lat, $lng)) {
            return null;
        }

        return number_format((float) $lat, 6, '.', '').', '.number_format((float) $lng, 6, '.', '');
    }

    /**
     * A link that opens the position on a map. Nothing is embedded in the page
     * and no key is needed: the reader's own browser opens it.
     */
    public static function mapUrl($lat, $lng): ?string
    {
        if (! self::has($lat, $lng)) {
            return null;
        }

        return 'https://www.google.com/maps/search/?api=1&query='
            .rawurlencode(number_format((float) $lat, 7, '.', '').','.number_format((float) $lng, 7, '.', ''));
    }

    /**
     * How far apart two positions are, in metres.
     *
     * Used to say whether a visit was recorded at the device or somewhere else.
     * Straight-line distance on a sphere, which at these ranges is as exact as
     * the readings themselves.
     */
    public static function distanceMetres($lat1, $lng1, $lat2, $lng2): ?float
    {
        if (! self::has($lat1, $lng1) || ! self::has($lat2, $lng2)) {
            return null;
        }

        $earthRadius = 6371000.0;

        $phi1 = deg2rad((float) $lat1);
        $phi2 = deg2rad((float) $lat2);
        $dPhi = $phi2 - $phi1;
        $dLam = deg2rad((float) $lng2 - (float) $lng1);

        $a = sin($dPhi / 2) ** 2 + cos($phi1) * cos($phi2) * sin($dLam / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * That distance written for a reader: metres up close, kilometres far out.
     */
    public static function formatDistance(?float $metres): ?string
    {
        if ($metres === null) {
            return null;
        }

        if ($metres < 1000) {
            return number_format($metres, 0).' m away';
        }

        return number_format($metres / 1000, 1).' km away';
    }

    /**
     * Pull a position out of a terminal's `coordinates` column, which may be an
     * array, a JSON string, or nothing at all.
     *
     * @return array{0: float|null, 1: float|null}
     */
    public static function fromCoordinates($coordinates): array
    {
        if (is_string($coordinates)) {
            $coordinates = json_decode($coordinates, true);
        }

        if (! is_array($coordinates)) {
            return [null, null];
        }

        $lat = $coordinates['latitude']  ?? $coordinates['lat'] ?? null;
        $lng = $coordinates['longitude'] ?? $coordinates['lng'] ?? $coordinates['long'] ?? null;

        return self::has($lat, $lng) ? [(float) $lat, (float) $lng] : [null, null];
    }
}
