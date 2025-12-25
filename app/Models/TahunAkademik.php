<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class TahunAkademik extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'tahun_akademik';

    protected $fillable = [
        'tahun',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'mulai_krs',
        'selesai_krs',
        'is_aktif',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'mulai_krs' => 'date',
        'selesai_krs' => 'date',
        'is_aktif' => 'boolean',
    ];

    public function jadwalKuliah()
    {
        return $this->hasMany(JadwalKuliah::class);
    }

    public function krs()
    {
        return $this->hasMany(Krs::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // Get nama lengkap tahun akademik
    public function getNamaLengkapAttribute()
    {
        return $this->tahun . ' ' . $this->semester;
    }

    // Get nama (alias for namaLengkap)
    public function getNamaAttribute()
    {
        return $this->tahun . ' ' . $this->semester;
    }

    // Get tahun akademik aktif
    public static function getAktif()
    {
        return self::where('is_aktif', true)->first();
    }

    // Check apakah periode KRS sedang buka
    public function isPeriodeKrs()
    {
        $today = now()->toDateString();
        return $this->mulai_krs <= $today && $this->selesai_krs >= $today;
    }
}
