@extends('layouts.app')

@section('title', 'Detail Sertifikasi')

@section('content')
<div class="page-title">
    <h4>Detail Sertifikasi Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.sertifikasi.index') }}">Sertifikasi</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informasi Sertifikasi</h5>
                <span class="badge bg-{{ $sertifikasi->status_color }} fs-6">{{ $sertifikasi->status_label }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nama Dosen</label>
                        <p class="mb-0 fw-bold">{{ $sertifikasi->dosen->nama_lengkap ?? '-' }}</p>
                        <small class="text-muted">{{ $sertifikasi->dosen->nidn ?? '' }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jenis Sertifikasi</label>
                        <p class="mb-0"><span class="badge bg-secondary">{{ $sertifikasi->jenis_label }}</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nama Sertifikasi</label>
                        <p class="mb-0 fw-bold">{{ $sertifikasi->nama_sertifikasi }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nomor Sertifikat</label>
                        <p class="mb-0">{{ $sertifikasi->nomor_sertifikat }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Penerbit</label>
                        <p class="mb-0">{{ $sertifikasi->penerbit }}</p>
                    </div>
                    @if($sertifikasi->bidang_studi)
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Bidang Studi</label>
                        <p class="mb-0">{{ $sertifikasi->bidang_studi }}</p>
                    </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Terbit</label>
                        <p class="mb-0">{{ $sertifikasi->tanggal_terbit->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Expired</label>
                        @if($sertifikasi->tanggal_expired)
                            <p class="mb-0">{{ $sertifikasi->tanggal_expired->format('d F Y') }}</p>
                            @if($sertifikasi->isExpiringSoon())
                                <span class="badge bg-warning">Akan expired dalam {{ $sertifikasi->tanggal_expired->diffInDays(now()) }} hari</span>
                            @endif
                        @else
                            <p class="mb-0 text-muted">Tidak expired</p>
                        @endif
                    </div>
                </div>

                @if($sertifikasi->file_sertifikat)
                <hr>
                <div class="mb-3">
                    <label class="form-label text-muted small">File Sertifikat</label>
                    <br>
                    <a href="{{ Storage::url($sertifikasi->file_sertifikat) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-file-pdf me-1"></i> Lihat Sertifikat
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <i class="bi bi-award text-primary" style="font-size: 4rem;"></i>
                <h5 class="mt-3">{{ $sertifikasi->jenis_label }}</h5>
                <p class="text-muted mb-0">{{ $sertifikasi->nama_sertifikasi }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('kepegawaian.sertifikasi.edit', $sertifikasi) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <a href="{{ route('kepegawaian.sertifikasi.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
