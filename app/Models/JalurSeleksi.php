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

    /**
     * Get biaya pendaftaran dari gelombang aktif
     */
    public function getBiayaPendaftaranAktifAttribute()
    {
        $gelombangAktif = GelombangPmb::where('is_active', true)->first();
        if (!$gelombangAktif) return null;

        $biaya = $this->biayaPendaftaran()
            ->where('gelombang_pmb_id', $gelombangAktif->id)
            ->first();

        return $biaya?->total_biaya;
    }

    /**
     * Get kuota total dari semua prodi untuk jalur ini
     */
    public function getTotalKuotaAttribute()
    {
        return $this->kuota()->sum('kuota');
    }

    /**
     * Get tanggal mulai dari gelombang aktif
     */
    public function getTanggalMulaiDaftarAttribute()
    {
        $gelombangAktif = GelombangPmb::where('is_active', true)->first();
        return $gelombangAktif?->tanggal_mulai_daftar;
    }

    /**
     * Get tanggal selesai dari gelombang aktif
     */
    public function getTanggalSelesaiDaftarAttribute()
    {
        $gelombangAktif = GelombangPmb::where('is_active', true)->first();
        return $gelombangAktif?->tanggal_selesai_daftar;
    }
}
