<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaEvaluasi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kriteria_evaluasi';

    protected $fillable = [
        'nama',
        'deskripsi',
        'bobot',
        'kategori',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    const KATEGORI = [
        'dosen' => 'Khusus Dosen',
        'pegawai' => 'Khusus Pegawai',
        'semua' => 'Semua',
    ];

    public function evaluasiKinerjaDetail()
    {
        return $this->hasMany(EvaluasiKinerjaDetail::class);
    }

    public function getKategoriLabelAttribute(): string
    {
        return self::KATEGORI[$this->kategori] ?? $this->kategori;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDosen($query)
    {
        return $query->whereIn('kategori', ['dosen', 'semua']);
    }

    public function scopeForPegawai($query)
    {
        return $query->whereIn('kategori', ['pegawai', 'semua']);
    }
}
