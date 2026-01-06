@extends('layouts.app')

@section('title', 'Edit Sertifikasi')

@section('content')
<div class="page-title">
    <h4>Edit Sertifikasi Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.sertifikasi.index') }}">Sertifikasi</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('kepegawaian.sertifikasi.update', $sertifikasi) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Dosen</label>
                    <input type="text" class="form-control" value="{{ $sertifikasi->dosen->nama_lengkap ?? '-' }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Sertifikasi <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                        <option value="serdos" {{ old('jenis', $sertifikasi->jenis) == 'serdos' ? 'selected' : '' }}>Sertifikasi Dosen (Serdos)</option>
                        <option value="kompetensi" {{ old('jenis', $sertifikasi->jenis) == 'kompetensi' ? 'selected' : '' }}>Sertifikasi Kompetensi</option>
                        <option value="profesi" {{ old('jenis', $sertifikasi->jenis) == 'profesi' ? 'selected' : '' }}>Sertifikasi Profesi</option>
                        <option value="lainnya" {{ old('jenis', $sertifikasi->jenis) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Sertifikasi <span class="text-danger">*</span></label>
                    <input type="text" name="nama_sertifikasi" class="form-control @error('nama_sertifikasi') is-invalid @enderror" value="{{ old('nama_sertifikasi', $sertifikasi->nama_sertifikasi) }}" required>
                    @error('nama_sertifikasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Sertifikat <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_sertifikat" class="form-control @error('nomor_sertifikat') is-invalid @enderror" value="{{ old('nomor_sertifikat', $sertifikasi->nomor_sertifikat) }}" required>
                    @error('nomor_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penerbit <span class="text-danger">*</span></label>
                    <input type="text" name="penerbit" class="form-control @error('penerbit') is-invalid @enderror" value="{{ old('penerbit', $sertifikasi->penerbit) }}" required>
                    @error('penerbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Bidang Studi</label>
                    <input type="text" name="bidang_studi" class="form-control @error('bidang_studi') is-invalid @enderror" value="{{ old('bidang_studi', $sertifikasi->bidang_studi) }}">
                    @error('bidang_studi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Terbit <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_terbit" class="form-control @error('tanggal_terbit') is-invalid @enderror" value="{{ old('tanggal_terbit', $sertifikasi->tanggal_terbit->format('Y-m-d')) }}" required>
                    @error('tanggal_terbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Expired</label>
                    <input type="date" name="tanggal_expired" class="form-control @error('tanggal_expired') is-invalid @enderror" value="{{ old('tanggal_expired', $sertifikasi->tanggal_expired ? $sertifikasi->tanggal_expired->format('Y-m-d') : '') }}">
                    <small class="text-muted">Kosongkan jika tidak expired</small>
                    @error('tanggal_expired')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">File Sertifikat (PDF)</label>
                    <input type="file" name="file_sertifikat" class="form-control @error('file_sertifikat') is-invalid @enderror" accept=".pdf">
                    @if($sertifikasi->file_sertifikat)
                        <small class="text-muted">File saat ini: <a href="{{ Storage::url($sertifikasi->file_sertifikat) }}" target="_blank">Lihat</a></small>
                    @endif
                    @error('file_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('kepegawaian.sertifikasi.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Update Sertifikasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
