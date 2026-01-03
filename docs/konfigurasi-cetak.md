# Konfigurasi Cetak - SIAKAD

## Deskripsi
Sistem konfigurasi cetak memungkinkan admin untuk mengkustomisasi tampilan dokumen PDF yang dihasilkan sistem. Setiap jenis dokumen memiliki konfigurasi tersendiri yang dapat diatur sesuai kebutuhan institusi.

## Jenis Dokumen yang Didukung

| Kode | Nama Dokumen | Deskripsi |
|------|--------------|-----------|
| `krs` | Kartu Rencana Studi (KRS) | Daftar mata kuliah yang diambil mahasiswa |
| `khs` | Kartu Hasil Studi (KHS) | Hasil nilai per semester |
| `transkrip` | Transkrip Nilai | Rekap seluruh nilai mahasiswa |
| `invoice` | Invoice Tagihan | Tagihan pembayaran mahasiswa |
| `kwitansi` | Kwitansi Pembayaran | Bukti pembayaran |
| `surat_cuti` | Surat Cuti Akademik | Surat keterangan cuti |
| `surat_aktif` | Surat Keterangan Aktif | Surat keterangan mahasiswa aktif |
| `kartu_ujian` | Kartu Peserta Ujian | Kartu untuk mengikuti ujian |
| `yudisium` | Surat Keterangan Yudisium | Surat keterangan kelulusan |
| `laporan` | Laporan Umum | Template untuk berbagai laporan |
| `daftar_hadir` | Daftar Hadir | Daftar hadir perkuliahan |

## Fitur Konfigurasi

### 1. Pengaturan Kertas
- **Ukuran Kertas**: A4, Letter, Legal, F4/Folio
- **Orientasi**: Portrait (tegak) atau Landscape (mendatar)
- **Margin**: Atas, Kanan, Bawah, Kiri (dalam mm)

### 2. Pengaturan Tampilan
- **Kop Surat**: Tampilkan/sembunyikan header institusi
- **Logo**: Tampilkan/sembunyikan logo institusi
- **Footer**: Tampilkan/sembunyikan footer sistem

### 3. Pengaturan Tanda Tangan
- **Tampilkan TTD**: Aktifkan/nonaktifkan bagian tanda tangan
- **Jabatan**: Jabatan penandatangan (contoh: Kepala Bagian Akademik)
- **Nama**: Nama penandatangan (opsional - dapat menggunakan data dinamis)
- **NIP**: NIP penandatangan (opsional)

### 4. Catatan Kaki
Tambahkan catatan tambahan di bagian bawah dokumen.

## Cara Penggunaan

### Akses Menu
1. Login sebagai Admin
2. Navigasi ke **Tools > Konfigurasi Cetak**

### Edit Konfigurasi
1. Klik tombol **Edit** pada dokumen yang ingin dikonfigurasi
2. Sesuaikan pengaturan sesuai kebutuhan
3. Klik **Simpan Perubahan**

### Preview Dokumen
- **Preview PDF**: Lihat hasil konfigurasi dalam format PDF dengan data dummy
- **Preview HTML**: Lihat preview dalam browser

### Reset ke Default
- Klik tombol reset (ikon panah melingkar) untuk mengembalikan ke pengaturan default
- Gunakan **Reset Semua ke Default** untuk mereset semua konfigurasi sekaligus

## Penggunaan di Code

### CetakService
```php
use App\Services\CetakService;

// Generate dan stream PDF
return CetakService::stream(
    'krs',                    // kode konfigurasi
    'cetak.krs',              // view path
    compact('mahasiswa', 'krs', 'tahunAkademik'),
    'KRS_' . $mahasiswa->nim . '.pdf'
);

// Generate dan download PDF
return CetakService::download(
    'invoice',
    'cetak.invoice',
    compact('tagihan', 'mahasiswa'),
    'Invoice_' . $tagihan->no_tagihan . '.pdf'
);
```

### Menggunakan Template Baru
1. Buat view di `resources/views/cetak/nama-dokumen.blade.php`
2. Extend dari master template:
```blade
@extends('cetak.layouts.master')

@section('title', 'Judul Dokumen')
@section('page-margin', $config->getMarginCss())

@section('content')
    @if($config->tampilkan_kop)
        @include('cetak.components.kop-surat')
    @endif
    
    {{-- Konten dokumen --}}
    
    @if($config->tampilkan_ttd)
        @include('cetak.components.signature', [
            'jabatan' => $config->jabatan_ttd,
            'nama' => $config->nama_ttd,
            'nip' => $config->nip_ttd,
        ])
    @endif
@endsection
```

### Komponen yang Tersedia
- `cetak.components.kop-surat` - Kop surat resmi dengan logo
- `cetak.components.header-simple` - Header sederhana
- `cetak.components.info-mahasiswa` - Tabel info mahasiswa
- `cetak.components.signature` - Tanda tangan tunggal
- `cetak.components.signature-double` - Tanda tangan ganda (2 kolom)
- `cetak.components.footer` - Footer sederhana

## Database

### Migration
```bash
php artisan migrate
```

### Seeder
```bash
php artisan db:seed --class=KonfigurasiCetakSeeder
```

## Helper Functions

```php
// Mendapatkan konfigurasi berdasarkan kode
$config = konfigurasi_cetak('krs');

// Format Rupiah
echo format_rupiah(1000000); // "Rp 1.000.000"

// Format Tanggal Indonesia
echo format_tanggal(now()); // "25 Desember 2025"
```

## Troubleshooting

### PDF tidak ter-generate
1. Pastikan package DomPDF terinstall: `composer require barryvdh/laravel-dompdf`
2. Jalankan `php artisan config:clear`

### Konfigurasi tidak muncul
1. Jalankan seeder: `php artisan db:seed --class=KonfigurasiCetakSeeder`
2. Periksa tabel `konfigurasi_cetak` di database

### Font tidak tampil
1. DomPDF memerlukan font DejaVu Sans yang sudah termasuk secara default
2. Untuk font custom, perlu dikonfigurasi di `config/dompdf.php`
