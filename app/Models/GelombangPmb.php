<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GelombangPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'gelombang_pmb';

    protected $fillable = [
        'periode_pmb_id',
        'nama',
        'nomor_gelombang',
        'tanggal_mulai_daftar',
        'tanggal_selesai_daftar',
        'tanggal_ujian',
        'tanggal_pengumuman',
        'tanggal_daftar_ulang_mulai',
        'tanggal_daftar_ulang_selesai',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai_daftar' => 'date',
        'tanggal_selesai_daftar' => 'date',
        'tanggal_ujian' => 'date',
        'tanggal_pengumuman' => 'date',
        'tanggal_daftar_ulang_mulai' => 'date',
        'tanggal_daftar_ulang_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function periodePmb()
    {
        return $this->belongsTo(PeriodePmb::class, 'periode_pmb_id');
    }

    public function biayaPendaftaran()
    {
        return $this->hasMany(BiayaPendaftaran::class, 'gelombang_pmb_id');
    }

    public function kuota()
    {
        return $this->hasMany(KuotaPmb::class, 'gelombang_pmb_id');
    }

    public function calonMahasiswa()
    {
        return $this->hasMany(CalonMahasiswa::class, 'gelombang_pmb_id');
    }

    public function jadwalUjian()
    {
        return $this->hasMany(JadwalUjianPmb::class, 'gelombang_pmb_id');
    }

    public function hasilSeleksi()
    {
        return $this->hasMany(HasilSeleksi::class, 'gelombang_pmb_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePendaftaranBuka($query)
    {
        return $query->where('tanggal_mulai_daftar', '<=', now())
                     ->where('tanggal_selesai_daftar', '>=', now());
    }

    // Helper Methods
    public static function getActive()
    {
        return self::whereHas('periodePmb', function($q) {
            $q->where('is_active', true);
        })->where('is_active', true)->first();
    }

    public function isPendaftaranBuka()
    {
        $now = now();
        return $this->tanggal_mulai_daftar <= $now && $this->tanggal_selesai_daftar >= $now;
    }

    public function getStatusPendaftaranAttribute()
    {
        $now = now();
        if ($this->tanggal_mulai_daftar > $now) {
            return 'Akan Dibuka';
        } elseif ($this->tanggal_selesai_daftar < $now) {
            return 'Ditutup';
        }
        return 'Dibuka';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status_pendaftaran) {
            'Dibuka' => 'success',
            'Akan Dibuka' => 'info',
            'Ditutup' => 'secondary',
            default => 'secondary',
        };
    }

    public function getTotalPendaftarAttribute()
    {
        return $this->calonMahasiswa()->count();
    }

    public function getTotalLulusAttribute()
    {
        return $this->hasilSeleksi()->where('status', 'lulus')->count();
    }
}
