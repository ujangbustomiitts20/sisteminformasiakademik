<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKartuUjian extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'detail_kartu_ujian';

    protected $fillable = [
        'kartu_ujian_id',
        'jadwal_ujian_id',
        'krs_id',
        'persentase_kehadiran',
        'eligible',
        'alasan_tidak_eligible',
        'paraf_pengawas',
    ];

    protected $casts = [
        'persentase_kehadiran' => 'decimal:2',
        'eligible' => 'boolean',
    ];

    public function kartuUjian()
    {
        return $this->belongsTo(KartuUjian::class);
    }

    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class);
    }

    public function krs()
    {
        return $this->belongsTo(Krs::class);
    }

    /**
     * Hitung persentase kehadiran per mata kuliah
     */
    public static function hitungKehadiran($krsId)
    {
        $absensi = Absensi::where('krs_id', $krsId)->get();
        
        if ($absensi->isEmpty()) {
            return 0;
        }

        $hadir = $absensi->whereIn('status', ['Hadir', 'Izin'])->count();
        $total = $absensi->count();

        return $total > 0 ? ($hadir / $total) * 100 : 0;
    }
}
