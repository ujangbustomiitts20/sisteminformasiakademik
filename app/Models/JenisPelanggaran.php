<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'jenis_pelanggaran';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'tingkat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    const TINGKAT = [
        'ringan' => 'Ringan',
        'sedang' => 'Sedang',
        'berat' => 'Berat',
        'sangat_berat' => 'Sangat Berat',
    ];

    public function pelanggaranPegawai()
    {
        return $this->hasMany(PelanggaranPegawai::class);
    }

    public function getTingkatLabelAttribute(): string
    {
        return self::TINGKAT[$this->tingkat] ?? $this->tingkat;
    }

    public function getTingkatColorAttribute(): string
    {
        return match($this->tingkat) {
            'ringan' => 'info',
            'sedang' => 'warning',
            'berat' => 'danger',
            'sangat_berat' => 'dark',
            default => 'secondary',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
