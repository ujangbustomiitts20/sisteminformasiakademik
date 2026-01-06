<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NamaJabatan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'nama_jabatan';

    protected $fillable = [
        'kode',
        'nama',
        'nama_singkat',
        'kategori',
        'level',
        'deskripsi',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Kategori jabatan
     */
    public const KATEGORI = [
        'pimpinan' => 'Pimpinan Institusi',
        'akademik' => 'Akademik',
        'keuangan' => 'Keuangan',
        'sdm' => 'SDM/Kepegawaian',
        'kemahasiswaan' => 'Kemahasiswaan',
        'umum' => 'Umum',
    ];

    /**
     * Level jabatan
     */
    public const LEVEL = [
        0 => 'Pimpinan Tertinggi',
        1 => 'Wakil Pimpinan',
        2 => 'Dekan/Direktur',
        3 => 'Wakil Dekan/Wakil Direktur',
        4 => 'Ketua Program Studi',
        5 => 'Sekretaris Program Studi',
        6 => 'Kepala Bagian/Unit',
        7 => 'Staff',
    ];

    /**
     * Relasi ke pejabat penandatangan
     */
    public function pejabatPenandatangan()
    {
        return $this->hasMany(PejabatPenandatangan::class, 'nama_jabatan_id');
    }

    /**
     * Scope aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope by kategori
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Get status badge attribute
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->aktif ? 'success' : 'secondary';
    }

    /**
     * Get kategori badge attribute
     */
    public function getKategoriBadgeAttribute(): string
    {
        return match($this->kategori) {
            'pimpinan' => 'danger',
            'akademik' => 'info',
            'keuangan' => 'warning',
            'sdm' => 'primary',
            'kemahasiswaan' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get kategori label
     */
    public function getKategoriLabelAttribute(): string
    {
        return self::KATEGORI[$this->kategori] ?? $this->kategori;
    }

    /**
     * Get level label
     */
    public function getLevelLabelAttribute(): string
    {
        return self::LEVEL[$this->level] ?? 'Level ' . $this->level;
    }
}
