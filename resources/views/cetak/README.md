# Template Konfigurasi Cetak SIAKAD

## Struktur File

```
resources/views/cetak/
├── layouts/
│   └── master.blade.php          # Master template untuk semua cetakan
├── components/
│   ├── kop-surat.blade.php       # Komponen kop surat resmi
│   ├── header-simple.blade.php   # Header simple untuk laporan
│   ├── info-mahasiswa.blade.php  # Info mahasiswa
│   ├── signature.blade.php       # Tanda tangan single
│   ├── signature-double.blade.php # Tanda tangan double
│   └── footer.blade.php          # Footer
├── krs.blade.php
├── khs.blade.php
└── transkrip.blade.php
```

## Cara Menggunakan

### 1. Extend Master Template

```blade
@extends('cetak.layouts.master')

@section('title', 'Judul Dokumen')

@section('content')
    {{-- Konten dokumen --}}
@endsection
```

### 2. Menggunakan Komponen

#### Kop Surat
```blade
@include('cetak.components.kop-surat')
```

#### Header Simple
```blade
@include('cetak.components.header-simple', [
    'title' => 'LAPORAN DATA MAHASISWA',
    'subtitle' => 'Tahun Akademik 2024/2025'
])
```

#### Info Mahasiswa
```blade
@include('cetak.components.info-mahasiswa', [
    'mahasiswa' => $mahasiswa,
    'tahunAkademik' => $tahunAkademik,
    'showAngkatan' => true,
    'showSemester' => true,
    'showDosenWali' => true
])
```

#### Tanda Tangan Single
```blade
@include('cetak.components.signature', [
    'jabatan' => 'Kepala Bagian Akademik',
    'nama' => 'Dr. Nama Lengkap',
    'nip' => '123456789',
    'kota' => 'Jakarta',
    'tanggal' => now()->format('d F Y')
])
```

#### Tanda Tangan Double
```blade
@include('cetak.components.signature-double', [
    'left' => [
        'title' => 'Mengetahui,',
        'jabatan' => 'Ketua Program Studi',
        'nama' => 'Dr. Nama Lengkap',
        'nip' => '123456789'
    ],
    'right' => [
        'jabatan' => 'Dosen Pembimbing Akademik',
        'nama' => 'Nama Dosen',
        'nidn' => '987654321'
    ],
    'kota' => 'Jakarta',
    'tanggal' => now()->format('d F Y')
])
```

### 3. CSS Classes yang Tersedia

#### Tables
```html
<table class="bordered">           <!-- Table dengan border -->
<table class="striped">            <!-- Table dengan stripe zebra -->
<table class="bordered striped">   <!-- Kombinasi -->
<table class="no-border">          <!-- Table tanpa border -->
<table class="info-table">         <!-- Table untuk info key-value -->
```

#### Badges
```html
<span class="badge badge-success">Lulus</span>
<span class="badge badge-primary">A</span>
<span class="badge badge-info">Info</span>
<span class="badge badge-warning">Pending</span>
<span class="badge badge-danger">Gagal</span>
<span class="badge badge-secondary">Draft</span>
```

#### Text Utilities
```html
<p class="text-center">Center</p>
<p class="text-left">Left</p>
<p class="text-right">Right</p>
<p class="text-bold">Bold</p>
<p class="text-italic">Italic</p>
<p class="text-small">Small</p>
<p class="text-muted">Muted</p>
```

#### Spacing
```html
<div class="mt-1">Margin top 5px</div>
<div class="mt-2">Margin top 10px</div>
<div class="mt-3">Margin top 15px</div>
<div class="mt-4">Margin top 20px</div>
<div class="mb-1">Margin bottom 5px</div>
<!-- etc. -->
```

#### Page Break
```html
<div class="page-break"></div>     <!-- Force page break -->
<div class="no-break">             <!-- Prevent page break inside -->
    Content here
</div>
```

### 4. Menggunakan CetakService

```php
use App\Services\CetakService;

// Stream PDF (tampilkan di browser)
return CetakService::stream('krs', 'cetak.krs', $data, 'filename.pdf');

// Download PDF
return CetakService::download('krs', 'cetak.krs', $data, 'filename.pdf');

// Get configuration
$config = CetakService::getConfig('krs');

// Check if configured
if (CetakService::isConfigured('krs')) {
    // ...
}
```

### 5. Konfigurasi yang Tersedia

| Kode | Deskripsi |
|------|-----------|
| `krs` | Kartu Rencana Studi |
| `khs` | Kartu Hasil Studi |
| `transkrip` | Transkrip Nilai |
| `invoice` | Invoice Tagihan |
| `kwitansi` | Kwitansi Pembayaran |
| `surat_cuti` | Surat Cuti Akademik |
| `surat_aktif` | Surat Keterangan Aktif |
| `kartu_ujian` | Kartu Peserta Ujian |
| `yudisium` | Surat Keterangan Yudisium |
| `laporan` | Laporan Umum |
| `daftar_hadir` | Daftar Hadir |

### 6. Mengakses Konfigurasi di View

Jika menggunakan `CetakService`, konfigurasi otomatis ditambahkan ke data dengan key `$config`.

```blade
@if($config->tampilkan_kop)
    @include('cetak.components.kop-surat')
@endif

@if($config->tampilkan_ttd)
    @include('cetak.components.signature', [
        'jabatan' => $config->jabatan_ttd,
        'nama' => $config->nama_ttd,
        'nip' => $config->nip_ttd
    ])
@endif
```

### 7. Custom Margin di View

```blade
@section('page-margin', '20mm 15mm 20mm 25mm')
```

### 8. Menambah Watermark

```blade
@section('watermark', 'DRAFT')
```

## Mengelola Konfigurasi

1. Akses menu **Admin > Konfigurasi Cetak**
2. Pilih dokumen yang ingin dikonfigurasi
3. Atur:
   - Ukuran kertas (A4, Letter, Legal, F4)
   - Orientasi (Portrait/Landscape)
   - Margin (Top, Right, Bottom, Left)
   - Tampilkan Kop Surat
   - Tampilkan Logo
   - Tampilkan Tanda Tangan
   - Jabatan/Nama/NIP Penandatangan
   - Footer dan Catatan Kaki

## Catatan

- Semua margin dalam satuan milimeter (mm)
- F4/Folio menggunakan ukuran custom 215 x 330 mm
- Logo diambil dari setting `logo_institusi`
- Data institusi diambil dari tabel `settings`
