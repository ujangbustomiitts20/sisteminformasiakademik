<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuUjian extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kartu_ujian';

    protected $fillable = [
        'nomor_kartu',
        'mahasiswa_id',
        'periode_ujian_id',
        'persentase_kehadiran',
        'eligible',
        'alasan_tidak_eligible',
        'pembayaran_lunas',
        'tanggal_cetak',
        'qr_code',
        'status',
    ];

    protected $casts = [
        'persentase_kehadiran' => 'decimal:2',
        'eligible' => 'boolean',
        'pembayaran_lunas' => 'boolean',
        'tanggal_cetak' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function periodeUjian()
    {
        return $this->belongsTo(PeriodeUjian::class);
    }

    public function detailKartuUjian()
    {
        return $this->hasMany(DetailKartuUjian::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_kartu)) {
                $model->nomor_kartu = self::generateNomorKartu($model->periode_ujian_id);
            }
        });
    }

    public static function generateNomorKartu($periodeUjianId)
    {
        $periode = PeriodeUjian::find($periodeUjianId);
        $prefix = 'KU' . ($periode ? $periode->jenis : '') . date('Ymd');
        $count = self::where('periode_ujian_id', $periodeUjianId)->count() + 1;
        
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cek eligibilitas mahasiswa untuk ujian
     */
    public static function cekEligibilitas($mahasiswaId, $periodeUjianId)
    {
        $periode = PeriodeUjian::find($periodeUjianId);
        if (!$periode) {
            return ['eligible' => false, 'alasan' => 'Periode ujian tidak ditemukan'];
        }

        $mahasiswa = Mahasiswa::find($mahasiswaId);
        if (!$mahasiswa) {
            return ['eligible' => false, 'alasan' => 'Mahasiswa tidak ditemukan'];
        }

        // Cek pembayaran jika diperlukan
        $pembayaranLunas = true;
        if ($periode->cek_pembayaran) {
            $tagihan = Tagihan::where('mahasiswa_id', $mahasiswaId)
                ->where('tahun_akademik_id', $periode->tahun_akademik_id)
                ->where('status', '!=', 'Lunas')
                ->exists();
            
            $pembayaranLunas = !$tagihan;
        }

        // Hitung rata-rata kehadiran
        $krs = Krs::where('mahasiswa_id', $mahasiswaId)
            ->where('tahun_akademik_id', $periode->tahun_akademik_id)
            ->pluck('id');

        $totalKehadiran = 0;
        $totalPertemuan = 0;

        foreach ($krs as $krsId) {
            $absensi = Absensi::where('krs_id', $krsId)->get();
            $hadir = $absensi->whereIn('status', ['Hadir', 'Izin'])->count();
            $total = $absensi->count();
            
            if ($total > 0) {
                $totalKehadiran += $hadir;
                $totalPertemuan += $total;
            }
        }

        $persentaseKehadiran = $totalPertemuan > 0 ? ($totalKehadiran / $totalPertemuan) * 100 : 0;

        $eligible = true;
        $alasan = [];

        if ($persentaseKehadiran < $periode->minimal_kehadiran) {
            $eligible = false;
            $alasan[] = 'Kehadiran kurang dari ' . $periode->minimal_kehadiran . '%';
        }

        if ($periode->cek_pembayaran && !$pembayaranLunas) {
            $eligible = false;
            $alasan[] = 'Masih ada tagihan yang belum lunas';
        }

        return [
            'eligible' => $eligible,
            'alasan' => implode(', ', $alasan),
            'persentase_kehadiran' => $persentaseKehadiran,
            'pembayaran_lunas' => $pembayaranLunas,
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'approved' => 'success',
            'printed' => 'info',
            'revoked' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }
}
