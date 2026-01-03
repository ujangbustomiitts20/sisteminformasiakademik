<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianKegiatanLapangan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'penilaian_kegiatan_lapangan';

    protected $fillable = [
        'pendaftaran_id',
        'jenis_penilai',
        'nilai_kedisiplinan',
        'nilai_kerjasama',
        'nilai_inisiatif',
        'nilai_keterampilan',
        'nilai_hasil_kerja',
        'nilai_laporan',
        'nilai_presentasi',
        'nilai_akhir',
        'grade',
        'catatan',
        'tanggal_penilaian',
    ];

    protected $casts = [
        'nilai_kedisiplinan' => 'decimal:2',
        'nilai_kerjasama' => 'decimal:2',
        'nilai_inisiatif' => 'decimal:2',
        'nilai_keterampilan' => 'decimal:2',
        'nilai_hasil_kerja' => 'decimal:2',
        'nilai_laporan' => 'decimal:2',
        'nilai_presentasi' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'tanggal_penilaian' => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranKegiatanLapangan::class, 'pendaftaran_id');
    }

    public static function getJenisPenilaiOptions()
    {
        return [
            'dosen' => 'Dosen Pembimbing',
            'lapangan' => 'Pembimbing Lapangan',
        ];
    }

    // Hitung nilai akhir dari komponen
    public function hitungNilaiAkhir()
    {
        // Bobot: kedisiplinan 15%, kerjasama 10%, inisiatif 10%, keterampilan 20%, hasil kerja 20%, laporan 15%, presentasi 10%
        $nilai = 0;
        $nilai += ($this->nilai_kedisiplinan ?? 0) * 0.15;
        $nilai += ($this->nilai_kerjasama ?? 0) * 0.10;
        $nilai += ($this->nilai_inisiatif ?? 0) * 0.10;
        $nilai += ($this->nilai_keterampilan ?? 0) * 0.20;
        $nilai += ($this->nilai_hasil_kerja ?? 0) * 0.20;
        $nilai += ($this->nilai_laporan ?? 0) * 0.15;
        $nilai += ($this->nilai_presentasi ?? 0) * 0.10;

        $this->nilai_akhir = round($nilai, 2);
        $this->grade = $this->hitungGrade($this->nilai_akhir);
        
        return $this->nilai_akhir;
    }

    public function hitungGrade($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'A-';
        if ($nilai >= 75) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'B-';
        if ($nilai >= 60) return 'C+';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 50) return 'C-';
        if ($nilai >= 45) return 'D';
        return 'E';
    }
}
