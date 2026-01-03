<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranKegiatanLapangan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pendaftaran_kegiatan_lapangan';

    protected $fillable = [
        'nomor_pendaftaran',
        'periode_id',
        'mahasiswa_id',
        'mitra_pilihan_1',
        'mitra_pilihan_2',
        'mitra_pilihan_3',
        'mitra_diterima',
        'dosen_pembimbing_id',
        'pembimbing_lapangan',
        'jabatan_pembimbing_lapangan',
        'rencana_kegiatan',
        'surat_pengantar',
        'dokumen_pendukung',
        'status',
        'catatan',
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodeKegiatanLapangan::class, 'periode_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function mitraPilihan1()
    {
        return $this->belongsTo(MitraKegiatan::class, 'mitra_pilihan_1');
    }

    public function mitraPilihan2()
    {
        return $this->belongsTo(MitraKegiatan::class, 'mitra_pilihan_2');
    }

    public function mitraPilihan3()
    {
        return $this->belongsTo(MitraKegiatan::class, 'mitra_pilihan_3');
    }

    public function mitraDiterima()
    {
        return $this->belongsTo(MitraKegiatan::class, 'mitra_diterima');
    }

    public function dosenPembimbing()
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id');
    }

    public function logKegiatan()
    {
        return $this->hasMany(LogKegiatanLapangan::class, 'pendaftaran_id');
    }

    public function penilaian()
    {
        return $this->hasMany(PenilaianKegiatanLapangan::class, 'pendaftaran_id');
    }

    public function penilaianDosen()
    {
        return $this->hasOne(PenilaianKegiatanLapangan::class, 'pendaftaran_id')
            ->where('jenis_penilai', 'dosen');
    }

    public function penilaianLapangan()
    {
        return $this->hasOne(PenilaianKegiatanLapangan::class, 'pendaftaran_id')
            ->where('jenis_penilai', 'lapangan');
    }

    public static function getStatusOptions()
    {
        return [
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'berlangsung' => 'Sedang Berlangsung',
            'selesai' => 'Selesai',
            'tidak_lulus' => 'Tidak Lulus',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'diajukan' => 'info',
            'disetujui' => 'primary',
            'ditolak' => 'danger',
            'berlangsung' => 'warning',
            'selesai' => 'success',
            'tidak_lulus' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public static function generateNomorPendaftaran($jenisKode)
    {
        $tahun = date('Y');
        $bulan = date('m');
        $prefix = "{$jenisKode}/{$tahun}/{$bulan}/";
        
        $lastNumber = self::where('nomor_pendaftaran', 'like', $prefix . '%')
            ->orderBy('nomor_pendaftaran', 'desc')
            ->first();

        if ($lastNumber) {
            $lastSeq = (int) substr($lastNumber->nomor_pendaftaran, -4);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }

        return $prefix . str_pad($newSeq, 4, '0', STR_PAD_LEFT);
    }

    // Hitung nilai akhir gabungan
    public function getNilaiAkhirAttribute()
    {
        $nilaiDosen = $this->penilaianDosen?->nilai_akhir ?? 0;
        $nilaiLapangan = $this->penilaianLapangan?->nilai_akhir ?? 0;
        
        if ($nilaiDosen == 0 && $nilaiLapangan == 0) return null;
        if ($nilaiDosen == 0) return $nilaiLapangan;
        if ($nilaiLapangan == 0) return $nilaiDosen;
        
        // Bobot: 50% dosen, 50% lapangan
        return ($nilaiDosen * 0.5) + ($nilaiLapangan * 0.5);
    }

    public function getGradeAttribute()
    {
        $nilai = $this->nilai_akhir;
        if ($nilai === null) return null;
        
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_pendaftaran)) {
                $jenisKode = $model->periode?->jenisKegiatan?->kode ?? 'PKL';
                $model->nomor_pendaftaran = self::generateNomorPendaftaran($jenisKode);
            }
        });
    }
}
