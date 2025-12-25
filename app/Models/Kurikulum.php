<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kurikulum extends Model
{
    use HashidsTrait;
    protected $table = 'kurikulum';

    protected $fillable = [
        'program_studi_id',
        'kode',
        'nama',
        'tahun_mulai',
        'tahun_selesai',
        'total_sks_wajib',
        'total_sks_pilihan',
        'total_sks_lulus',
        'minimal_semester',
        'maksimal_semester',
        'deskripsi',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function mataKuliah(): BelongsToMany
    {
        return $this->belongsToMany(MataKuliah::class, 'kurikulum_mata_kuliah')
            ->withPivot('semester_rekomendasi', 'kategori')
            ->withTimestamps();
    }

    public function kurikulumMataKuliah(): HasMany
    {
        return $this->hasMany(KurikulumMataKuliah::class);
    }

    // Accessor untuk total SKS
    public function getTotalSksAttribute(): int
    {
        return $this->total_sks_wajib + $this->total_sks_pilihan;
    }

    // Get MK per semester
    public function getMataKuliahBySemester(int $semester)
    {
        return $this->mataKuliah()
            ->wherePivot('semester_rekomendasi', $semester)
            ->orderBy('kode')
            ->get();
    }

    // Hitung total SKS dari MK yang terdaftar
    public function hitungTotalSks(): array
    {
        $wajib = $this->mataKuliah()->wherePivotIn('kategori', ['Wajib', 'Wajib Prodi', 'MKU'])->sum('sks');
        $pilihan = $this->mataKuliah()->wherePivotIn('kategori', ['Pilihan', 'Pilihan Prodi'])->sum('sks');
        
        return [
            'wajib' => $wajib,
            'pilihan' => $pilihan,
            'total' => $wajib + $pilihan,
        ];
    }

    // Scope aktif
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // Scope by prodi
    public function scopeByProdi($query, $prodiId)
    {
        return $query->where('program_studi_id', $prodiId);
    }
}
