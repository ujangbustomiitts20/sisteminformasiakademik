<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaUjianPmb extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'peserta_ujian_pmb';

    protected $fillable = [
        'calon_mahasiswa_id',
        'jadwal_ujian_pmb_id',
        'no_peserta',
        'ruangan',
        'no_kursi',
        'hadir',
        'waktu_hadir',
    ];

    protected $casts = [
        'hadir' => 'boolean',
        'waktu_hadir' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_peserta)) {
                $model->no_peserta = self::generateNoPeserta($model->jadwal_ujian_pmb_id);
            }
        });
    }

    // Generate Nomor Peserta
    public static function generateNoPeserta($jadwalId)
    {
        $count = self::where('jadwal_ujian_pmb_id', $jadwalId)->count();
        return str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function calonMahasiswa()
    {
        return $this->belongsTo(CalonMahasiswa::class, 'calon_mahasiswa_id');
    }

    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjianPmb::class, 'jadwal_ujian_pmb_id');
    }

    // Helper Methods
    public function getStatusKehadiranAttribute()
    {
        return $this->hadir ? 'Hadir' : 'Belum Hadir';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->hadir ? 'success' : 'secondary';
    }

    public function setHadir()
    {
        $this->update([
            'hadir' => true,
            'waktu_hadir' => now(),
        ]);
    }
}
