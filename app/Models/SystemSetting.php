<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'label', 'group', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        return static::castValue($setting->value, $setting->type);
    }

    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => (string) $value]);
    }

    public static function group(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->get()->keyBy('key');
    }

    private static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'int'      => (int) $value,
            'bool'     => (bool) (int) $value,
            'json'     => json_decode($value, true),
            'password' => $value, // stored as plain text; encrypt in future if needed
            default    => $value,
        };
    }
}
