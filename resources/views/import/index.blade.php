@extends('layouts.app')

@section('title', 'Import Data')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Import Data</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Import Data</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Import Mahasiswa -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Import Data Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Import data mahasiswa dari file CSV. Format kolom yang dibutuhkan:</p>
                    <ul class="small text-muted">
                        <li><code>nim</code> - NIM Mahasiswa (wajib)</li>
                        <li><code>nama</code> - Nama Lengkap (wajib)</li>
                        <li><code>email</code> - Email (wajib)</li>
                        <li><code>jenis_kelamin</code> - Laki-laki / Perempuan (wajib)</li>
                        <li><code>tempat_lahir</code> - Tempat Lahir (wajib)</li>
                        <li><code>tanggal_lahir</code> - Format YYYY-MM-DD (wajib)</li>
                        <li><code>kode_prodi</code> - Kode Program Studi (wajib)</li>
                        <li><code>angkatan</code> - Tahun Angkatan (opsional)</li>
                        <li><code>alamat</code> - Alamat (opsional)</li>
                        <li><code>telepon</code> - No. Telepon (opsional)</li>
                    </ul>
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        Password default untuk mahasiswa adalah NIM
                    </div>
                    
                    <form action="{{ route('import.mahasiswa') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file_mahasiswa" class="form-label">Pilih File CSV</label>
                            <input type="file" name="file" id="file_mahasiswa" class="form-control" accept=".csv,.txt" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i>Import
                            </button>
                            <a href="{{ route('import.template.mahasiswa') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-download me-1"></i>Download Template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import Dosen -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Import Data Dosen</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Import data dosen dari file CSV. Format kolom yang dibutuhkan:</p>
                    <ul class="small text-muted">
                        <li><code>nidn</code> - NIDN Dosen (wajib)</li>
                        <li><code>nama</code> - Nama Lengkap (wajib)</li>
                        <li><code>email</code> - Email (wajib)</li>
                        <li><code>jenis_kelamin</code> - Laki-laki / Perempuan (wajib)</li>
                        <li><code>tempat_lahir</code> - Tempat Lahir (wajib)</li>
                        <li><code>tanggal_lahir</code> - Format YYYY-MM-DD (wajib)</li>
                        <li><code>kode_fakultas</code> - Kode Fakultas (opsional)</li>
                        <li><code>alamat</code> - Alamat (opsional)</li>
                        <li><code>telepon</code> - No. Telepon (opsional)</li>
                    </ul>
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        Password default untuk dosen adalah NIDN
                    </div>
                    
                    <form action="{{ route('import.dosen') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file_dosen" class="form-label">Pilih File CSV</label>
                            <input type="file" name="file" id="file_dosen" class="form-control" accept=".csv,.txt" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-upload me-1"></i>Import
                            </button>
                            <a href="{{ route('import.template.dosen') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-download me-1"></i>Download Template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips Import Data</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h6><i class="bi bi-1-circle text-primary me-2"></i>Persiapkan File CSV</h6>
                    <p class="small text-muted">Pastikan file CSV menggunakan format yang sesuai dengan template. Gunakan delimiter koma (,) dan encoding UTF-8.</p>
                </div>
                <div class="col-md-4">
                    <h6><i class="bi bi-2-circle text-primary me-2"></i>Periksa Master Data</h6>
                    <p class="small text-muted">Pastikan kode Program Studi dan Fakultas sudah terdaftar di sistem sebelum import.</p>
                </div>
                <div class="col-md-4">
                    <h6><i class="bi bi-3-circle text-primary me-2"></i>Hindari Duplikat</h6>
                    <p class="small text-muted">NIM dan NIDN harus unik. Data yang duplikat akan otomatis dilewati.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
