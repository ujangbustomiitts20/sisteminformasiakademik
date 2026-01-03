<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapPresensi extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'rekap_presensi';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'tahun',
        'bulan',
        'total_hari_kerja',
        'hadir',
        'terlambat',
        'sakit',
        'izin',
        'cuti',
        'alpha',
        'dinas_luar',
        'persentase_kehadiran',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'total_hari_kerja' => 'integer',
        'hadir' => 'integer',
        'terlambat' => 'integer',
        'sakit' => 'integer',
        'izin' => 'integer',
        'cuti' => 'integer',
        'alpha' => 'integer',
        'dinas_luar' => 'integer',
        'persentase_kehadiran' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $totalHadir = $model->hadir + $model->terlambat + $model->dinas_luar;
            if ($model->total_hari_kerja > 0) {
                $model->persentase_kehadiran = ($totalHadir / $model->total_hari_kerja) * 100;
            }
        });
    }

    // Relationships
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // Accessors
    public function getNamaBulanAttribute()
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $namaBulan[$this->bulan] ?? '-';
    }

    public function getPeriodeAttribute()
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen->nama ?? '-';
        }
        return $this->pegawai->nama ?? '-';
    }

    public function getTotalTidakHadirAttribute()
    {
        return $this->sakit + $this->izin + $this->cuti + $this->alpha;
    }

    // Scopes
    public function scopeByPeriode($query, $bulan, $tahun)
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }

    // Helper untuk generate rekap bulanan
    public static function generateRekap($dosenId = null, $pegawaiId = null, $bulan = null, $tahun = null)
    {
        $bulan = $bulan ?? date('n');
        $tahun = $tahun ?? date('Y');

        $query = PresensiPegawai::whereMonth('tanggal', $bulan)
                                 ->whereYear('tanggal', $tahun);

        if ($dosenId) {
            $query->where('dosen_id', $dosenId);
        } else {
            $query->where('pegawai_id', $pegawaiId);
        }

        $presensi = $query->get();

        // Hitung jumlah hari kerja dalam bulan (exclude weekend)
        $totalHariKerja = 0;
        $startDate = \Carbon\Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        while ($startDate->lte($endDate)) {
            if (!$startDate->isWeekend()) {
                $totalHariKerja++;
            }
            $startDate->addDay();
        }

        $data = [
            'dosen_id' => $dosenId,
            'pegawai_id' => $pegawaiId,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'total_hari_kerja' => $totalHariKerja,
            'hadir' => $presensi->where('status', 'hadir')->count(),
            'terlambat' => $presensi->where('status', 'terlambat')->count(),
            'sakit' => $presensi->where('status', 'sakit')->count(),
            'izin' => $presensi->where('status', 'izin')->count(),
            'cuti' => $presensi->where('status', 'cuti')->count(),
            'alpha' => $presensi->where('status', 'alpha')->count(),
            'dinas_luar' => $presensi->where('status', 'dinas_luar')->count(),
        ];

        return self::updateOrCreate(
            ['dosen_id' => $dosenId, 'pegawai_id' => $pegawaiId, 'tahun' => $tahun, 'bulan' => $bulan],
            $data
        );
    }
}
