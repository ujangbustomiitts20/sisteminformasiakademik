<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKegiatanLapangan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'jenis_kegiatan_lapangan';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'sks',
        'durasi_minggu',
        'semester_minimal',
        'sks_minimal',
        'is_active',
    ];

    protected $casts = [
        'sks' => 'integer',
        'durasi_minggu' => 'integer',
        'semester_minimal' => 'integer',
        'sks_minimal' => 'integer',
        'is_active' => 'boolean',
    ];

    public function periodeKegiatan()
    {
        return $this->hasMany(PeriodeKegiatanLapangan::class, 'jenis_kegiatan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
