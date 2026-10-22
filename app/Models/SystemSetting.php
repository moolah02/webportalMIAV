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

    /**
     * Writes the setting, creating it when it does not exist yet. The previous
     * version only ran an UPDATE, so setting a new key silently did nothing.
     */
    public static function set(string $key, mixed $value, string $type = 'string', ?string $label = null, string $group = 'general'): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = is_array($value) ? json_encode($value) : (string) $value;

        if (! $setting->exists) {
            $setting->type = $type;
            $setting->label = $label ?: ucfirst(str_replace('_', ' ', $key));
            $setting->group = $group;
        }

        $setting->save();
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
