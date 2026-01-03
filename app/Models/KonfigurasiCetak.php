<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HashidsTrait;

class KonfigurasiCetak extends Model
{
    use HasFactory, HashidsTrait;

    protected $table = 'konfigurasi_cetak';

    protected $fillable = [
        'kode',
        'nama',
        'judul',
        'ukuran_kertas',
        'orientasi',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'tampilkan_kop',
        'tampilkan_logo',
        'tampilkan_ttd',
        'jabatan_ttd',
        'nama_ttd',
        'nip_ttd',
        'tampilkan_footer',
        'catatan_kaki',
        'pengaturan_tambahan',
        'is_active',
    ];

    protected $casts = [
        'tampilkan_kop' => 'boolean',
        'tampilkan_logo' => 'boolean',
        'tampilkan_ttd' => 'boolean',
        'tampilkan_footer' => 'boolean',
        'is_active' => 'boolean',
        'pengaturan_tambahan' => 'array',
    ];

    /**
     * Get konfigurasi by kode
     */
    public static function getByKode(string $kode): ?self
    {
        return static::where('kode', $kode)->where('is_active', true)->first();
    }

    /**
     * Get paper size for DomPDF
     */
    public function getPaperSize(): string
    {
        return match($this->ukuran_kertas) {
            'a4' => 'a4',
            'letter' => 'letter',
            'legal' => 'legal',
            'f4' => [215.9, 330.2], // F4 in mm converted to points
            default => 'a4',
        };
    }

    /**
     * Get margins as array
     */
    public function getMargins(): array
    {
        return [
            'top' => $this->margin_top . 'mm',
            'bottom' => $this->margin_bottom . 'mm',
            'left' => $this->margin_left . 'mm',
            'right' => $this->margin_right . 'mm',
        ];
    }

    /**
     * Get margin string for CSS
     */
    public function getMarginCss(): string
    {
        return "{$this->margin_top}mm {$this->margin_right}mm {$this->margin_bottom}mm {$this->margin_left}mm";
    }

    /**
     * Default configurations
     */
    public static function getDefaults(): array
    {
        return [
            [
                'kode' => 'krs',
                'nama' => 'Kartu Rencana Studi (KRS)',
                'judul' => 'KARTU RENCANA STUDI',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '15',
                'margin_bottom' => '20',
                'margin_left' => '15',
                'margin_right' => '15',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Kepala Bagian Akademik',
                'tampilkan_footer' => true,
            ],
            [
                'kode' => 'khs',
                'nama' => 'Kartu Hasil Studi (KHS)',
                'judul' => 'KARTU HASIL STUDI',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '15',
                'margin_bottom' => '20',
                'margin_left' => '15',
                'margin_right' => '15',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Kepala Bagian Akademik',
                'tampilkan_footer' => true,
            ],
            [
                'kode' => 'transkrip',
                'nama' => 'Transkrip Nilai',
                'judul' => 'TRANSKRIP AKADEMIK',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '15',
                'margin_bottom' => '20',
                'margin_left' => '20',
                'margin_right' => '15',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Dekan',
                'tampilkan_footer' => true,
            ],
            [
                'kode' => 'invoice',
                'nama' => 'Invoice Tagihan',
                'judul' => 'INVOICE',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '10',
                'margin_bottom' => '15',
                'margin_left' => '15',
                'margin_right' => '15',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Bagian Keuangan',
                'tampilkan_footer' => true,
            ],
            [
                'kode' => 'kwitansi',
                'nama' => 'Kwitansi Pembayaran',
                'judul' => 'KWITANSI',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '10',
                'margin_bottom' => '15',
                'margin_left' => '15',
                'margin_right' => '15',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Bendahara',
                'tampilkan_footer' => true,
            ],
            [
                'kode' => 'surat_cuti',
                'nama' => 'Surat Cuti Akademik',
                'judul' => 'SURAT KETERANGAN CUTI AKADEMIK',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '20',
                'margin_bottom' => '20',
                'margin_left' => '25',
                'margin_right' => '20',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Wakil Rektor Bidang Akademik',
                'tampilkan_footer' => false,
            ],
            [
                'kode' => 'surat_aktif',
                'nama' => 'Surat Keterangan Aktif',
                'judul' => 'SURAT KETERANGAN AKTIF KULIAH',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '20',
                'margin_bottom' => '20',
                'margin_left' => '25',
                'margin_right' => '20',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Kepala Bagian Akademik',
                'tampilkan_footer' => false,
            ],
            [
                'kode' => 'kartu_ujian',
                'nama' => 'Kartu Peserta Ujian',
                'judul' => 'KARTU PESERTA UJIAN',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '10',
                'margin_bottom' => '10',
                'margin_left' => '10',
                'margin_right' => '10',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Kepala Bagian Akademik',
                'tampilkan_footer' => false,
            ],
            [
                'kode' => 'yudisium',
                'nama' => 'Surat Keterangan Yudisium',
                'judul' => 'SURAT KETERANGAN LULUS',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '20',
                'margin_bottom' => '20',
                'margin_left' => '25',
                'margin_right' => '20',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Rektor',
                'tampilkan_footer' => false,
            ],
            [
                'kode' => 'laporan',
                'nama' => 'Laporan Umum',
                'judul' => 'LAPORAN',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'portrait',
                'margin_top' => '15',
                'margin_bottom' => '15',
                'margin_left' => '15',
                'margin_right' => '15',
                'tampilkan_kop' => false,
                'tampilkan_logo' => false,
                'tampilkan_ttd' => false,
                'tampilkan_footer' => true,
            ],
            [
                'kode' => 'daftar_hadir',
                'nama' => 'Daftar Hadir',
                'judul' => 'DAFTAR HADIR PERKULIAHAN',
                'ukuran_kertas' => 'a4',
                'orientasi' => 'landscape',
                'margin_top' => '10',
                'margin_bottom' => '10',
                'margin_left' => '10',
                'margin_right' => '10',
                'tampilkan_kop' => true,
                'tampilkan_logo' => true,
                'tampilkan_ttd' => true,
                'jabatan_ttd' => 'Dosen Pengampu',
                'tampilkan_footer' => false,
            ],
        ];
    }
}
