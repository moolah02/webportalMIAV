<?php

namespace App\Services;

/**
 * Published MIAV Android apps (APKs), described by JSON files in
 * storage/app/mobile-app/ — written by `php artisan miav:publish-apk`.
 * The APKs live next to them and are only served to logged-in users.
 *
 * Channels:
 *  - current     → the app technicians use today ("MIAV")
 *  - new-design  → the redesigned app, installed as a separate app
 *                  ("MIAV New Design") so both can be tried side by side
 */
class MobileAppRelease
{
    public const DIR = 'mobile-app';

    public const CHANNELS = [
        'current'    => 'latest.json',
        'new-design' => 'latest-new-design.json',
    ];

    private static array $cache = [];

    /** ['version','build','file','size','sha256','released_at','notes','channel'] or null */
    public static function latest(string $channel = 'current'): ?array
    {
        if (!array_key_exists($channel, self::$cache)) {
            $meta = isset(self::CHANNELS[$channel]) ? self::metaFile($channel) : null;
            $data = ($meta && is_file($meta)) ? json_decode((string) file_get_contents($meta), true) : null;
            self::$cache[$channel] = (is_array($data) && !empty($data['file']) && is_file(self::path($data['file'])))
                ? $data
                : null;
        }
        return self::$cache[$channel];
    }

    public static function metaFile(string $channel): string
    {
        return self::dir() . '/' . (self::CHANNELS[$channel] ?? self::CHANNELS['current']);
    }

    public static function dir(): string
    {
        return storage_path('app/' . self::DIR);
    }

    public static function path(string $file): string
    {
        return self::dir() . '/' . basename($file);
    }
}
