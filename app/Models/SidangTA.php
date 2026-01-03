<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidangTA extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'sidang_ta';

    protected $fillable = [
        'nomor_sidang',
        'tugas_akhir_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan',
        'ketua_penguji_id',
        'penguji_1_id',
        'penguji_2_id',
        'status',
        'nilai_presentasi',
        'nilai_penguasaan_materi',
        'nilai_tanya_jawab',
        'nilai_dokumen',
        'nilai_ketua',
        'nilai_penguji_1',
        'nilai_penguji_2',
        'nilai_pembimbing_1',
        'nilai_pembimbing_2',
        'nilai_akhir',
        'grade',
        'hasil',
        'catatan_revisi',
        'deadline_revisi',
        'revisi_selesai',
        'tanggal_revisi_selesai',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'deadline_revisi' => 'date',
        'tanggal_revisi_selesai' => 'datetime',
        'revisi_selesai' => 'boolean',
        'nilai_presentasi' => 'decimal:2',
        'nilai_penguasaan_materi' => 'decimal:2',
        'nilai_tanya_jawab' => 'decimal:2',
        'nilai_dokumen' => 'decimal:2',
        'nilai_ketua' => 'decimal:2',
        'nilai_penguji_1' => 'decimal:2',
        'nilai_penguji_2' => 'decimal:2',
        'nilai_pembimbing_1' => 'decimal:2',
        'nilai_pembimbing_2' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];

    public function tugasAkhir()
    {
        return $this->belongsTo(TugasAkhir::class, 'tugas_akhir_id');
    }

    public function ketuaPenguji()
    {
        return $this->belongsTo(Dosen::class, 'ketua_penguji_id');
    }

    public function penguji1()
    {
        return $this->belongsTo(Dosen::class, 'penguji_1_id');
    }

    public function penguji2()
    {
        return $this->belongsTo(Dosen::class, 'penguji_2_id');
    }

    public function revisi()
    {
        return $this->hasMany(RevisiTA::class, 'sidang_ta_id');
    }

    public static function getStatusOptions()
    {
        return [
            'diajukan' => 'Diajukan',
            'dijadwalkan' => 'Dijadwalkan',
            'berlangsung' => 'Berlangsung',
            'selesai' => 'Selesai',
            'ditunda' => 'Ditunda',
            'dibatalkan' => 'Dibatalkan',
        ];
    }

    public static function getHasilOptions()
    {
        return [
            'lulus' => 'Lulus',
            'lulus_revisi' => 'Lulus dengan Revisi',
            'tidak_lulus' => 'Tidak Lulus',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'diajukan' => 'info',
            'dijadwalkan' => 'primary',
            'berlangsung' => 'warning',
            'selesai' => 'success',
            'ditunda' => 'secondary',
            'dibatalkan' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getHasilBadgeAttribute()
    {
        $badges = [
            'lulus' => 'success',
            'lulus_revisi' => 'warning',
            'tidak_lulus' => 'danger',
        ];

        return $badges[$this->hasil] ?? 'secondary';
    }

    // Hitung nilai akhir
    public function hitungNilaiAkhir()
    {
        // Bobot: Ketua (15%), Penguji 1 (15%), Penguji 2 (15%), Pembimbing 1 (30%), Pembimbing 2 (25%)
        $nilai = 0;
        $nilai += ($this->nilai_ketua ?? 0) * 0.15;
        $nilai += ($this->nilai_penguji_1 ?? 0) * 0.15;
        $nilai += ($this->nilai_penguji_2 ?? 0) * 0.15;
        $nilai += ($this->nilai_pembimbing_1 ?? 0) * 0.30;
        $nilai += ($this->nilai_pembimbing_2 ?? 0) * 0.25;

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

    public static function generateNomorSidang()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $prefix = "SDG/{$tahun}/{$bulan}/";
        
        $lastNumber = self::where('nomor_sidang', 'like', $prefix . '%')
            ->orderBy('nomor_sidang', 'desc')
            ->first();

        if ($lastNumber) {
            $lastSeq = (int) substr($lastNumber->nomor_sidang, -4);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }

        return $prefix . str_pad($newSeq, 4, '0', STR_PAD_LEFT);
    }

    // Cek apakah semua revisi sudah selesai
    public function cekRevisiSelesai()
    {
        $belumSelesai = $this->revisi()->where('sudah_diperbaiki', false)->count();
        return $belumSelesai == 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_sidang)) {
                $model->nomor_sidang = self::generateNomorSidang();
            }
        });
    }
}
