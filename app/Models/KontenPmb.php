<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontenPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'konten_pmb';

    protected $fillable = [
        'key',
        'type',
        'value',
        'label',
        'group',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    // Helper Methods
    public static function getValue($key, $default = null)
    {
        $konten = self::where('key', $key)->where('is_active', true)->first();
        return $konten ? $konten->value : $default;
    }

    public static function getByGroup($group)
    {
        return self::where('group', $group)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->pluck('value', 'key');
    }

    public static function getAllGrouped()
    {
        return self::where('is_active', true)
            ->orderBy('group')
            ->orderBy('order')
            ->get()
            ->groupBy('group');
    }

    /**
     * Get all konten as flat key-value array for views
     * Maps database keys to view-expected keys
     */
    public static function getAllFlat()
    {
        $konten = self::where('is_active', true)
            ->get()
            ->pluck('value', 'key')
            ->toArray();

        // Map keys to expected view keys for backwards compatibility
        $mapped = [];
        foreach ($konten as $key => $value) {
            $mapped[$key] = $value;
        }

        // Add mapped aliases for common view keys
        if (isset($mapped['nama_institusi'])) {
            $mapped['nama_universitas'] = $mapped['nama_institusi'];
        }
        if (isset($mapped['singkatan_institusi'])) {
            $mapped['singkatan'] = $mapped['singkatan_institusi'];
        }

        return $mapped;
    }
}
