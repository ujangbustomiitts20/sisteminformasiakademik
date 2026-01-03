<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingJamKerja extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'setting_jam_kerja';

    protected $fillable = [
        'nama_setting',
        'jam_masuk',
        'jam_keluar',
        'toleransi_terlambat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'toleransi_terlambat' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper untuk mendapatkan setting aktif
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}
