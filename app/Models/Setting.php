<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $value = Cache::remember("setting.{$key}", 3600, function () use ($key) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : null;
        });
        
        // If value is null or empty string, return default
        if ($value === null || $value === '') {
            return $default;
        }
        
        return $value;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        Cache::forget("setting.{$key}");
    }

    /**
     * Get multiple settings as an array
     */
    public static function getMultiple(array $keys): array
    {
        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = static::get($key);
        }
        return $settings;
    }

    /**
     * Clear settings cache
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }
}
