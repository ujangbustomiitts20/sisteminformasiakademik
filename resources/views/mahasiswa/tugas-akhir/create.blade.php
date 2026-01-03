@extends('layouts.app')

@section('title', 'Ajukan Judul TA')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Pengajuan Judul Tugas Akhir</h1>
        <a href="{{ route('mahasiswa.tugas-akhir.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('mahasiswa.tugas-akhir.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Judul Tugas Akhir <span class="text-danger">*</span></label>
                    <textarea name="judul" class="form-control @error('judul') is-invalid @enderror" rows="2" required placeholder="Masukkan judul tugas akhir yang ingin diajukan...">{{ old('judul') }}</textarea>
                    @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Bidang Ilmu <span class="text-danger">*</span></label>
                    <input type="text" name="bidang_ilmu" class="form-control @error('bidang_ilmu') is-invalid @enderror" value="{{ old('bidang_ilmu') }}" required placeholder="Contoh: Machine Learning, Web Development, dll">
                    @error('bidang_ilmu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" required placeholder="Jelaskan secara singkat tentang topik tugas akhir Anda...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Latar Belakang</label>
                    <textarea name="latar_belakang" class="form-control @error('latar_belakang') is-invalid @enderror" rows="4" placeholder="Jelaskan latar belakang permasalahan yang ingin diselesaikan...">{{ old('latar_belakang') }}</textarea>
                    @error('latar_belakang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Rumusan Masalah</label>
                    <textarea name="rumusan_masalah" class="form-control @error('rumusan_masalah') is-invalid @enderror" rows="3" placeholder="1. Bagaimana...&#10;2. Bagaimana...">{{ old('rumusan_masalah') }}</textarea>
                    @error('rumusan_masalah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tujuan Penelitian</label>
                    <textarea name="tujuan_penelitian" class="form-control @error('tujuan_penelitian') is-invalid @enderror" rows="3" placeholder="1. Mengembangkan...&#10;2. Mengimplementasikan...">{{ old('tujuan_penelitian') }}</textarea>
                    @error('tujuan_penelitian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Dokumen Proposal Awal (PDF, opsional)</label>
                    <input type="file" name="dokumen_proposal" class="form-control @error('dokumen_proposal') is-invalid @enderror" accept=".pdf">
                    <small class="text-muted">Jika sudah memiliki draft proposal, silakan upload. Max 10MB.</small>
                    @error('dokumen_proposal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <hr>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('mahasiswa.tugas-akhir.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan sebagai Draft
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
