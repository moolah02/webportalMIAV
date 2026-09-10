<?php

namespace App\Services;

/**
 * The latest published MIAV Android app (APK), described by
 * storage/app/mobile-app/latest.json — written by `php artisan miav:publish-apk`.
 * The APK itself lives next to it and is only served to logged-in users.
 */
class MobileAppRelease
{
    public const DIR = 'mobile-app';

    private static ?array $cache = null;
    private static bool $loaded = false;

    /** ['version','build','file','size','sha256','released_at','notes'] or null */
    public static function latest(): ?array
    {
        if (!self::$loaded) {
            self::$loaded = true;
            $meta = self::dir() . '/latest.json';
            $data = is_file($meta) ? json_decode((string) file_get_contents($meta), true) : null;
            self::$cache = (is_array($data) && !empty($data['file']) && is_file(self::path($data['file'])))
                ? $data
                : null;
        }
        return self::$cache;
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
