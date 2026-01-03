<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JalurSeleksi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'jalur_seleksi';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'persyaratan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function biayaPendaftaran()
    {
        return $this->hasMany(BiayaPendaftaran::class, 'jalur_seleksi_id');
    }

    public function kuota()
    {
        return $this->hasMany(KuotaPmb::class, 'jalur_seleksi_id');
    }

    public function calonMahasiswa()
    {
        return $this->hasMany(CalonMahasiswa::class, 'jalur_seleksi_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function getTotalPendaftarAttribute()
    {
        return $this->calonMahasiswa()->count();
    }
}
