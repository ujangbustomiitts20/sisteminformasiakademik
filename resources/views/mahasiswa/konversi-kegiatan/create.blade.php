@extends('layouts.app')

@section('title', 'Ajukan Konversi Kegiatan')

@section('content')
<div class="page-title">
    <h4>Ajukan Konversi Kegiatan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('konversi-kegiatan.index') }}">Konversi Kegiatan</a></li>
            <li class="breadcrumb-item active">Ajukan Baru</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Form Pengajuan Konversi Kegiatan</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <strong>Informasi:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Pengajuan akan dibuat sebagai <strong>Draft</strong></li>
                        <li>Anda dapat menambahkan beberapa kegiatan dalam satu pengajuan</li>
                        <li>Setelah lengkap, klik <strong>Ajukan</strong> untuk memproses</li>
                    </ul>
                </div>

                <form action="{{ route('konversi-kegiatan.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Tahun Akademik</label>
                        <input type="text" class="form-control" value="{{ $tahunAkademik->tahun }} {{ $tahunAkademik->semester }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mahasiswa</label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->nim }} - {{ $mahasiswa->nama }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Program Studi</label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->programStudi->nama ?? '-' }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Catatan/Alasan Pengajuan</label>
                        <textarea name="catatan_mahasiswa" class="form-control @error('catatan_mahasiswa') is-invalid @enderror" rows="3" placeholder="Tuliskan alasan atau catatan untuk pengajuan ini (opsional)">{{ old('catatan_mahasiswa') }}</textarea>
                        @error('catatan_mahasiswa')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('konversi-kegiatan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Buat Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
