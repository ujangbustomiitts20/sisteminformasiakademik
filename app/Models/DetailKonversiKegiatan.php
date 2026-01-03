<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class DetailKonversiKegiatan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'detail_konversi_kegiatan';

    protected $fillable = [
        'pengajuan_konversi_kegiatan_id',
        'jenis_kegiatan',
        'nama_kegiatan',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'no_sertifikat',
        'durasi_jam',
        'deskripsi_kegiatan',
        'bukti_dokumen',
        'mata_kuliah_id',
        'sks_diakui',
        'nilai_huruf',
        'nilai_angka',
        'status_detail',
        'catatan_verifikasi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'nilai_angka' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKonversiKegiatan::class, 'pengajuan_konversi_kegiatan_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function krs()
    {
        return $this->hasOne(Krs::class, 'detail_konversi_kegiatan_id');
    }

    // ========== HELPERS ==========

    public function getStatusBadgeAttribute()
    {
        return match($this->status_detail) {
            'Pending' => '<span class="badge bg-warning">Pending</span>',
            'Disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            default => '<span class="badge bg-secondary">' . $this->status_detail . '</span>',
        };
    }

    public function getJenisKegiatanBadgeAttribute()
    {
        return match($this->jenis_kegiatan) {
            'Sertifikasi' => '<span class="badge bg-primary">Sertifikasi</span>',
            'Lomba' => '<span class="badge bg-success">Lomba</span>',
            'Magang' => '<span class="badge bg-info">Magang</span>',
            'Kursus' => '<span class="badge bg-warning">Kursus</span>',
            'Pelatihan' => '<span class="badge bg-secondary">Pelatihan</span>',
            'Pengalaman Kerja' => '<span class="badge bg-dark">Pengalaman Kerja</span>',
            'Organisasi' => '<span class="badge bg-purple">Organisasi</span>',
            'Lainnya' => '<span class="badge bg-light text-dark">Lainnya</span>',
            default => '<span class="badge bg-secondary">' . $this->jenis_kegiatan . '</span>',
        };
    }

    public static function getJenisKegiatanOptions()
    {
        return [
            'Sertifikasi' => 'Sertifikasi Profesional',
            'Lomba' => 'Lomba/Kompetisi',
            'Magang' => 'Magang/PKL/Internship',
            'Kursus' => 'Kursus Online/Offline',
            'Pelatihan' => 'Pelatihan/Workshop',
            'Pengalaman Kerja' => 'Pengalaman Kerja',
            'Organisasi' => 'Kegiatan Organisasi',
            'Lainnya' => 'Lainnya',
        ];
    }
}
