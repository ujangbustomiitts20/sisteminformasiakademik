<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapKehadiran extends Model
{
    use HashidsTrait;
    
    protected $table = 'rekap_kehadiran';

    protected $fillable = [
        'mahasiswa_id',
        'mata_kuliah_id',
        'tahun_akademik_id',
        'jadwal_kuliah_id',
        'total_pertemuan',
        'jumlah_hadir',
        'jumlah_izin',
        'jumlah_sakit',
        'jumlah_alpa',
        'persentase_kehadiran',
    ];

    protected $casts = [
        'persentase_kehadiran' => 'decimal:2',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    // Hitung persentase kehadiran
    public function hitungPersentase(): float
    {
        if ($this->total_pertemuan == 0) {
            return 0;
        }
        
        return round(($this->jumlah_hadir / $this->total_pertemuan) * 100, 2);
    }

    // Update persentase
    public function updatePersentase(): void
    {
        $this->persentase_kehadiran = $this->hitungPersentase();
        $this->save();
    }

    // Accessor untuk badge persentase
    public function getPersentaseBadgeAttribute(): string
    {
        if ($this->persentase_kehadiran >= 80) {
            return 'success';
        } elseif ($this->persentase_kehadiran >= 70) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    // Cek apakah memenuhi syarat ujian (minimal 75% kehadiran)
    public function getMemenuhiSyaratUjianAttribute(): bool
    {
        return $this->persentase_kehadiran >= 75;
    }

    // Static method untuk update rekap dari absensi
    public static function updateFromAbsensi(int $mahasiswaId, int $mataKuliahId, int $tahunAkademikId, ?int $kelasId = null): void
    {
        $absensi = Absensi::where('mahasiswa_id', $mahasiswaId)
            ->whereHas('pertemuan.jadwalKuliah', function ($q) use ($mataKuliahId, $tahunAkademikId) {
                $q->where('mata_kuliah_id', $mataKuliahId)
                  ->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->get();

        $rekap = self::firstOrCreate([
            'mahasiswa_id' => $mahasiswaId,
            'mata_kuliah_id' => $mataKuliahId,
            'tahun_akademik_id' => $tahunAkademikId,
        ], [
            'kelas_id' => $kelasId,
        ]);

        $rekap->total_pertemuan = $absensi->count();
        $rekap->jumlah_hadir = $absensi->where('status', 'Hadir')->count();
        $rekap->jumlah_izin = $absensi->where('status', 'Izin')->count();
        $rekap->jumlah_sakit = $absensi->where('status', 'Sakit')->count();
        $rekap->jumlah_alpa = $absensi->where('status', 'Alpa')->count();
        $rekap->updatePersentase();
    }
}
