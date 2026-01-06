<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanGaji extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pengaturan_gaji';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'gaji_pokok',
        'aktif',
        'berlaku_mulai',
        'berlaku_sampai',
        'catatan',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'aktif' => 'boolean',
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
    ];

    /**
     * Relasi ke Dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    /**
     * Relasi ke Pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Relasi ke detail pengaturan gaji
     */
    public function details()
    {
        return $this->hasMany(PengaturanGajiDetail::class);
    }

    /**
     * Get nama pegawai
     */
    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen) {
            return $this->dosen->nama;
        }
        if ($this->pegawai) {
            return $this->pegawai->nama;
        }
        return '-';
    }

    /**
     * Get tipe pegawai
     */
    public function getTipePegawaiAttribute()
    {
        if ($this->dosen_id) {
            return 'Dosen';
        }
        if ($this->pegawai_id) {
            return 'Tendik';
        }
        return '-';
    }

    /**
     * Scope untuk pengaturan aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Get atau create pengaturan gaji untuk pegawai
     */
    public static function getOrCreate($dosenId = null, $pegawaiId = null)
    {
        return self::firstOrCreate(
            ['dosen_id' => $dosenId, 'pegawai_id' => $pegawaiId],
            ['gaji_pokok' => 0, 'aktif' => true]
        );
    }
}
