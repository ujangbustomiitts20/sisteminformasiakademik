<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingDokumenPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'setting_dokumen_pmb';

    protected $fillable = [
        'kode',
        'nama_dokumen',
        'deskripsi',
        'format_file',
        'max_size_kb',
        'is_wajib',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_wajib' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWajib($query)
    {
        return $query->where('is_wajib', true);
    }

    public function scopeOrdered($query)
    {
        // Urutkan: wajib dulu (DESC karena 1=wajib, 0=opsional), lalu urutan, lalu nama
        return $query->orderByDesc('is_wajib')->orderBy('urutan')->orderBy('nama_dokumen');
    }

    // Helper Methods
    public static function getAllActive()
    {
        return self::active()->ordered()->get();
    }

    public static function getWajib()
    {
        return self::active()->wajib()->ordered()->get();
    }

    public function getFormatArrayAttribute()
    {
        return array_map('trim', explode(',', $this->format_file));
    }

    public function getMaxSizeMbAttribute()
    {
        return round($this->max_size_kb / 1024, 2);
    }

    public function isValidFormat($extension)
    {
        return in_array(strtolower($extension), $this->format_array);
    }

    public function isValidSize($sizeInBytes)
    {
        return $sizeInBytes <= ($this->max_size_kb * 1024);
    }
}
