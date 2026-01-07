<?php

namespace App\Models;

use App\Traits\HashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PejabatPenandatangan extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'pejabat_penandatangan';

    protected $fillable = [
        'kode',
        'nama_jabatan_id',
        'jabatan',
        'nama',
        'nip',
        'pangkat_golongan',
        'gelar_depan',
        'gelar_belakang',
        'dosen_id',
        'pegawai_id',
        'tanda_tangan',
        'stempel',
        'kategori',
        'dokumen_terkait',
        'berlaku_mulai',
        'berlaku_sampai',
        'aktif',
        'urutan',
        'catatan',
    ];

    protected $casts = [
        'dokumen_terkait' => 'array',
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
        'aktif' => 'boolean',
    ];

    /**
     * Kategori pejabat
     */
    public const KATEGORI = [
        'pimpinan' => 'Pimpinan Institusi',
        'akademik' => 'Akademik',
        'keuangan' => 'Keuangan',
        'sdm' => 'SDM/Kepegawaian',
        'kemahasiswaan' => 'Kemahasiswaan',
        'umum' => 'Umum',
    ];

    /**
     * Daftar dokumen yang memerlukan tanda tangan
     */
    public const DOKUMEN = [
        // Pimpinan
        'ijazah' => 'Ijazah',
        'skpi' => 'SKPI (Surat Keterangan Pendamping Ijazah)',
        'sk_pengangkatan' => 'SK Pengangkatan Pegawai',
        'sk_kenaikan_pangkat' => 'SK Kenaikan Pangkat',
        'sk_pensiun' => 'SK Pensiun',
        'mou' => 'MoU/Perjanjian Kerjasama',
        'ktm' => 'Kartu Tanda Mahasiswa',
        'undangan_wisuda' => 'Undangan Wisuda',
        
        // Akademik
        'transkrip' => 'Transkrip Nilai',
        'khs' => 'Kartu Hasil Studi (KHS)',
        'krs' => 'Kartu Rencana Studi (KRS)',
        'skl' => 'Surat Keterangan Lulus',
        'surat_yudisium' => 'Surat Yudisium',
        'sk_pembimbing' => 'SK Dosen Pembimbing',
        'sk_penguji' => 'SK Dosen Penguji',
        'sk_wali' => 'SK Dosen Wali',
        'berita_acara_sidang' => 'Berita Acara Sidang',
        'surat_cuti_akademik' => 'Surat Cuti Akademik',
        'surat_aktif_kuliah' => 'Surat Keterangan Aktif Kuliah',
        'surat_izin_penelitian' => 'Surat Izin Penelitian',
        'surat_pengantar_magang' => 'Surat Pengantar Magang/PKL',
        'surat_rekomendasi' => 'Surat Rekomendasi',
        
        // Keuangan
        'slip_gaji' => 'Slip Gaji',
        'kwitansi' => 'Kwitansi Pembayaran',
        'surat_tagihan' => 'Surat Tagihan',
        'surat_bebas_keuangan' => 'Surat Bebas Keuangan',
        'sk_beasiswa' => 'SK Penerima Beasiswa',
        
        // SDM
        'surat_cuti_pegawai' => 'Surat Cuti Pegawai',
        'surat_tugas' => 'Surat Tugas',
        'sppd' => 'Surat Perintah Perjalanan Dinas (SPPD)',
        'sk_mutasi' => 'SK Mutasi',
        
        // Umum
        'surat_bebas_perpustakaan' => 'Surat Bebas Perpustakaan',
        'surat_bebas_lab' => 'Surat Bebas Laboratorium',
        'berita_acara_rapat' => 'Berita Acara Rapat',
    ];

    /**
     * Relasi ke Dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    /**
     * Relasi ke Pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Relasi ke NamaJabatan
     */
    public function namaJabatan()
    {
        return $this->belongsTo(NamaJabatan::class, 'nama_jabatan_id');
    }

    /**
     * Scope untuk pejabat aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope berdasarkan kategori
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope berdasarkan kode
     */
    public function scopeKode($query, $kode)
    {
        return $query->where('kode', $kode);
    }

    /**
     * Get pejabat by kode
     */
    public static function getByKode($kode)
    {
        return self::where('kode', $kode)->aktif()->first();
    }

    /**
     * Get nama lengkap dengan gelar
     */
    public function getNamaLengkapAttribute()
    {
        $nama = $this->nama;
        if ($this->gelar_depan) {
            $nama = $this->gelar_depan . ' ' . $nama;
        }
        if ($this->gelar_belakang) {
            $nama = $nama . ', ' . $this->gelar_belakang;
        }
        return $nama;
    }

    /**
     * Get URL tanda tangan
     */
    public function getTandaTanganUrlAttribute()
    {
        if ($this->tanda_tangan) {
            return asset('storage/' . $this->tanda_tangan);
        }
        return null;
    }

    /**
     * Get URL stempel
     */
    public function getStempelUrlAttribute()
    {
        if ($this->stempel) {
            return asset('storage/' . $this->stempel);
        }
        return null;
    }

    /**
     * Check apakah pejabat masih berlaku
     */
    public function isBerlaku()
    {
        $now = now()->toDateString();
        
        if ($this->berlaku_mulai && $this->berlaku_mulai > $now) {
            return false;
        }
        
        if ($this->berlaku_sampai && $this->berlaku_sampai < $now) {
            return false;
        }
        
        return $this->aktif;
    }

    /**
     * Get pejabat untuk dokumen tertentu
     */
    public static function getForDokumen($dokumenKode)
    {
        return self::aktif()
            ->where(function ($query) use ($dokumenKode) {
                $query->whereJsonContains('dokumen_terkait', $dokumenKode)
                    ->orWhereNull('dokumen_terkait');
            })
            ->orderBy('urutan')
            ->get();
    }
}
