<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'order',
    ];

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, $value): bool
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
            Cache::forget("setting_{$key}");
            Cache::forget('settings_all');
            return true;
        }
        
        return false;
    }

    /**
     * Get all settings
     */
    public static function getAll(): array
    {
        return Cache::remember('settings_all', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group)
    {
        return self::where('group', $group)
            ->orderBy('order')
            ->get();
    }

    /**
     * Clear settings cache
     */
    public static function clearCache(): void
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
        Cache::forget('settings_all');
    }

    /**
     * Get grouped settings for form
     */
    public static function getGrouped(): array
    {
        $settings = self::orderBy('group')->orderBy('order')->get();
        
        $grouped = [];
        foreach ($settings as $setting) {
            $grouped[$setting->group][] = $setting;
        }
        
        return $grouped;
    }

    /**
     * Get group labels
     */
    public static function getGroupLabels(): array
    {
        return [
            'general' => 'Pengaturan Umum',
            'institution' => 'Informasi Institusi',
            'contact' => 'Kontak',
            'email' => 'Email / Mail Server',
            'academic' => 'Akademik',
            'appearance' => 'Tampilan',
            'print' => 'Konfigurasi Cetak',
        ];
    }
}
