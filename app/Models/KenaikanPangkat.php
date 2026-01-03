<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KenaikanPangkat extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'kenaikan_pangkat';

    protected $fillable = [
        'dosen_id',
        'pegawai_id',
        'periode',
        'tahun',
        'pangkat_lama',
        'golongan_lama',
        'tmt_pangkat_lama',
        'pangkat_baru',
        'golongan_baru',
        'tmt_pangkat_baru',
        'jenis',
        'no_sk',
        'tanggal_sk',
        'pejabat_penandatangan',
        'masa_kerja_tahun',
        'masa_kerja_bulan',
        'pendidikan_terakhir',
        'angka_kredit',
        'penilaian_kinerja',
        'dokumen_sk',
        'dokumen_pak',
        'dokumen_skp',
        'status',
        'catatan',
        'diusulkan_oleh',
        'diverifikasi_oleh',
        'disetujui_oleh',
    ];

    protected $casts = [
        'tmt_pangkat_lama' => 'date',
        'tmt_pangkat_baru' => 'date',
        'tanggal_sk' => 'date',
        'tahun' => 'integer',
        'angka_kredit' => 'decimal:2',
    ];

    const PERIODE = [
        'april' => 'April',
        'oktober' => 'Oktober',
    ];

    const JENIS = [
        'reguler' => 'Reguler',
        'pilihan' => 'Pilihan',
        'struktural' => 'Struktural',
        'fungsional' => 'Fungsional',
        'penyesuaian_ijazah' => 'Penyesuaian Ijazah',
        'pengabdian' => 'Pengabdian',
        'anumerta' => 'Anumerta',
        'tugas_belajar' => 'Tugas Belajar',
    ];

    const STATUS = [
        'draft' => 'Draft',
        'diusulkan' => 'Diusulkan',
        'verifikasi' => 'Verifikasi',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
    ];

    const PANGKAT = [
        'I/a' => 'Juru Muda',
        'I/b' => 'Juru Muda Tingkat I',
        'I/c' => 'Juru',
        'I/d' => 'Juru Tingkat I',
        'II/a' => 'Pengatur Muda',
        'II/b' => 'Pengatur Muda Tingkat I',
        'II/c' => 'Pengatur',
        'II/d' => 'Pengatur Tingkat I',
        'III/a' => 'Penata Muda',
        'III/b' => 'Penata Muda Tingkat I',
        'III/c' => 'Penata',
        'III/d' => 'Penata Tingkat I',
        'IV/a' => 'Pembina',
        'IV/b' => 'Pembina Tingkat I',
        'IV/c' => 'Pembina Utama Muda',
        'IV/d' => 'Pembina Utama Madya',
        'IV/e' => 'Pembina Utama',
    ];

    // Relationships
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function diusulkanOleh()
    {
        return $this->belongsTo(User::class, 'diusulkan_oleh');
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Accessors
    public function getPeriodeLabelAttribute()
    {
        return self::PERIODE[$this->periode] ?? $this->periode;
    }

    public function getJenisLabelAttribute()
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'diusulkan' => '<span class="badge bg-info">Diusulkan</span>',
            'verifikasi' => '<span class="badge bg-warning text-dark">Verifikasi</span>',
            'disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }

    public function getNamaPegawaiAttribute()
    {
        if ($this->dosen_id) {
            return $this->dosen->nama ?? '-';
        }
        return $this->pegawai->nama ?? '-';
    }

    public function getMasaKerjaAttribute()
    {
        return $this->masa_kerja_tahun . ' tahun ' . $this->masa_kerja_bulan . ' bulan';
    }

    public function getPangkatLamaLabelAttribute()
    {
        return self::PANGKAT[$this->golongan_lama] ?? $this->pangkat_lama;
    }

    public function getPangkatBaruLabelAttribute()
    {
        return self::PANGKAT[$this->golongan_baru] ?? $this->pangkat_baru;
    }

    public function getPeriodeTahunAttribute()
    {
        return $this->periode_label . ' ' . $this->tahun;
    }

    // Scopes
    public function scopeByPeriode($query, $periode, $tahun)
    {
        return $query->where('periode', $periode)->where('tahun', $tahun);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    public function scopePeriodeAktif($query)
    {
        $bulanIni = now()->month;
        if ($bulanIni >= 1 && $bulanIni <= 4) {
            return $query->where('periode', 'april')->where('tahun', now()->year);
        } else {
            return $query->where('periode', 'oktober')->where('tahun', now()->year);
        }
    }

    // Helper untuk mendapatkan golongan berikutnya
    public static function getNextGolongan($golongan)
    {
        $urutan = array_keys(self::PANGKAT);
        $index = array_search($golongan, $urutan);
        
        if ($index !== false && isset($urutan[$index + 1])) {
            return $urutan[$index + 1];
        }
        
        return null;
    }
}
