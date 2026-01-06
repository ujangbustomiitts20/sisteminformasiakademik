@extends('layouts.app')

@section('title', 'Buat SKP Baru')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Buat SKP Baru</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dosen.skp.index') }}">SKP</a></li>
                <li class="breadcrumb-item active">Buat Baru</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Form SKP Baru</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('dosen.skp.store') }}" method="POST">
                    @csrf
                    
                    <!-- Info Pegawai (readonly) -->
                    <div class="alert alert-light mb-4">
                        <h6 class="alert-heading mb-2"><i class="bi bi-person me-2"></i>Data Pegawai</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Nama</small>
                                <p class="mb-1 fw-semibold">{{ $dosen->nama_lengkap ?? $dosen->nama }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">NIDN</small>
                                <p class="mb-1 fw-semibold">{{ $dosen->nidn ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Jabatan</small>
                                <p class="mb-1 fw-semibold">{{ $dosen->jabatan_fungsional ?? $dosen->jabatan_akademik ?? 'Dosen' }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Unit Kerja</small>
                                <p class="mb-0 fw-semibold">{{ $dosen->programStudi->nama ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                <select name="tahun" class="form-select @error('tahun') is-invalid @enderror" required>
                                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}" {{ old('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Periode</label>
                                <select name="periode" class="form-select @error('periode') is-invalid @enderror">
                                    <option value="">-- Pilih Periode --</option>
                                    <option value="Semester 1" {{ old('periode') == 'Semester 1' ? 'selected' : '' }}>Semester 1 (Januari - Juni)</option>
                                    <option value="Semester 2" {{ old('periode') == 'Semester 2' ? 'selected' : '' }}>Semester 2 (Juli - Desember)</option>
                                    <option value="Tahunan" {{ old('periode') == 'Tahunan' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                                @error('periode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dosen.skp.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Buat SKP & Tambah Target
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-info">
            <div class="card-header bg-info text-white py-3">
                <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Panduan</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0 ps-3">
                    <li class="mb-2">Pilih <strong>Tahun</strong> dan <strong>Periode</strong> SKP</li>
                    <li class="mb-2">Setelah SKP dibuat, tambahkan <strong>Target Kinerja</strong></li>
                    <li class="mb-2">Setelah target lengkap, <strong>Ajukan</strong> ke atasan</li>
                    <li class="mb-2">Tunggu <strong>Persetujuan</strong> dari atasan</li>
                    <li class="mb-2">Di akhir periode, input <strong>Realisasi</strong> capaian</li>
                    <li>Atasan akan melakukan <strong>Penilaian</strong></li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
