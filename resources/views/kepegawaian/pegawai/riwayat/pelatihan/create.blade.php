@extends('layouts.app')

@section('title', 'Tambah Riwayat Pelatihan')

@section('content')
<div class="page-title">
    <h4>Tambah Riwayat Pelatihan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai) }}">{{ $pegawai->nama }}</a></li>
            <li class="breadcrumb-item active">Tambah Pelatihan</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-bookmark me-2"></i>Form Riwayat Pelatihan
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.pegawai.riwayat.pelatihan.store', $pegawai) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nama_pelatihan" class="form-label">Nama Pelatihan/Diklat <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pelatihan" id="nama_pelatihan" class="form-control @error('nama_pelatihan') is-invalid @enderror" value="{{ old('nama_pelatihan') }}" required>
                        @error('nama_pelatihan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_pelatihan" class="form-label">Jenis <span class="text-danger">*</span></label>
                                <select name="jenis_pelatihan" id="jenis_pelatihan" class="form-select @error('jenis_pelatihan') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Diklat', 'Workshop', 'Seminar', 'Kursus', 'Sertifikasi', 'Lainnya'] as $j)
                                    <option value="{{ $j }}" {{ old('jenis_pelatihan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_pelatihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="penyelenggara" class="form-label">Penyelenggara</label>
                                <input type="text" name="penyelenggara" id="penyelenggara" class="form-control @error('penyelenggara') is-invalid @enderror" value="{{ old('penyelenggara') }}">
                                @error('penyelenggara')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tempat" class="form-label">Tempat</label>
                        <input type="text" name="tempat" id="tempat" class="form-control @error('tempat') is-invalid @enderror" value="{{ old('tempat') }}">
                        @error('tempat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}">
                                @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                                <input type="number" name="tahun" id="tahun" class="form-control @error('tahun') is-invalid @enderror" value="{{ old('tahun', date('Y')) }}" min="1990" max="{{ date('Y') }}" required>
                                @error('tahun')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlah_jam" class="form-label">Jumlah Jam Pelatihan</label>
                        <input type="number" name="jumlah_jam" id="jumlah_jam" class="form-control @error('jumlah_jam') is-invalid @enderror" value="{{ old('jumlah_jam') }}" min="0">
                        @error('jumlah_jam')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    <h6 class="mb-3">Data Sertifikat</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_sertifikat" class="form-label">No. Sertifikat</label>
                                <input type="text" name="no_sertifikat" id="no_sertifikat" class="form-control @error('no_sertifikat') is-invalid @enderror" value="{{ old('no_sertifikat') }}">
                                @error('no_sertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_sertifikat" class="form-label">Tanggal Sertifikat</label>
                                <input type="date" name="tanggal_sertifikat" id="tanggal_sertifikat" class="form-control @error('tanggal_sertifikat') is-invalid @enderror" value="{{ old('tanggal_sertifikat') }}">
                                @error('tanggal_sertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_sertifikat" class="form-label">File Sertifikat</label>
                        <input type="file" name="file_sertifikat" id="file_sertifikat" class="form-control @error('file_sertifikat') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maks: 2MB</small>
                        @error('file_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
