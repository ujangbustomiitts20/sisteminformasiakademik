<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraKegiatan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'mitra_kegiatan';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'kota',
        'provinsi',
        'telepon',
        'email',
        'nama_kontak',
        'jabatan_kontak',
        'bidang_usaha',
        'kuota_mahasiswa',
        'is_active',
    ];

    protected $casts = [
        'kuota_mahasiswa' => 'integer',
        'is_active' => 'boolean',
    ];

    public function pendaftaranPilihan1()
    {
        return $this->hasMany(PendaftaranKegiatanLapangan::class, 'mitra_pilihan_1');
    }

    public function pendaftaranPilihan2()
    {
        return $this->hasMany(PendaftaranKegiatanLapangan::class, 'mitra_pilihan_2');
    }

    public function pendaftaranPilihan3()
    {
        return $this->hasMany(PendaftaranKegiatanLapangan::class, 'mitra_pilihan_3');
    }

    public function pendaftaranDiterima()
    {
        return $this->hasMany(PendaftaranKegiatanLapangan::class, 'mitra_diterima');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAlamatLengkapAttribute()
    {
        return "{$this->alamat}, {$this->kota}, {$this->provinsi}";
    }

    public static function generateKode()
    {
        $lastMitra = self::orderBy('id', 'desc')->first();
        $lastNumber = $lastMitra ? (int) substr($lastMitra->kode, 3) : 0;
        return 'MTR' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
