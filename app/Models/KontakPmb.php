<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontakPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kontak_pmb';

    protected $fillable = [
        'type',
        'label',
        'value',
        'icon',
        'link',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public static function getSosialMedia()
    {
        return self::active()
            ->whereIn('type', ['facebook', 'instagram', 'youtube', 'twitter', 'tiktok', 'linkedin'])
            ->get();
    }

    public static function getKontak()
    {
        return self::active()
            ->whereIn('type', ['phone', 'email', 'whatsapp', 'address'])
            ->get();
    }

    public static function getByType()
    {
        return self::active()->get()->groupBy('type');
    }
}
