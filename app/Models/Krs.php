<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class Krs extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'krs';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'jadwal_kuliah_id',
        'status',
        'tanggal_pengajuan',
        'tanggal_persetujuan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_persetujuan' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function nilai()
    {
        return $this->hasOne(Nilai::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    // Hitung persentase kehadiran
    public function persentaseKehadiran()
    {
        $total = $this->absensi()->count();
        if ($total == 0) return 0;

        $hadir = $this->absensi()->whereIn('status', ['Hadir', 'Izin', 'Sakit'])->count();
        return round(($hadir / $total) * 100, 2);
    }
}
