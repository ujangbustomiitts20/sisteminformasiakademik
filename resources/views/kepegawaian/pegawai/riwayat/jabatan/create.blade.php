@extends('layouts.app')

@section('title', 'Tambah Riwayat Jabatan')

@section('content')
<div class="page-title">
    <h4>Tambah Riwayat Jabatan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai) }}">{{ $pegawai->nama }}</a></li>
            <li class="breadcrumb-item active">Tambah Jabatan</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-briefcase me-2"></i>Form Riwayat Jabatan
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.pegawai.riwayat.jabatan.store', $pegawai) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nama_jabatan" class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_jabatan" id="nama_jabatan" class="form-control @error('nama_jabatan') is-invalid @enderror" value="{{ old('nama_jabatan') }}" required>
                                @error('nama_jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jenis_jabatan" class="form-label">Jenis Jabatan <span class="text-danger">*</span></label>
                                <select name="jenis_jabatan" id="jenis_jabatan" class="form-select @error('jenis_jabatan') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Struktural', 'Fungsional', 'Akademik'] as $j)
                                    <option value="{{ $j }}" {{ old('jenis_jabatan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="unit_kerja" class="form-label">Unit Kerja</label>
                        <input type="text" name="unit_kerja" id="unit_kerja" class="form-control @error('unit_kerja') is-invalid @enderror" value="{{ old('unit_kerja') }}">
                        @error('unit_kerja')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tmt_jabatan" class="form-label">TMT Jabatan <span class="text-danger">*</span></label>
                                <input type="date" name="tmt_jabatan" id="tmt_jabatan" class="form-control @error('tmt_jabatan') is-invalid @enderror" value="{{ old('tmt_jabatan') }}" required>
                                @error('tmt_jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tmt_selesai" class="form-label">TMT Selesai</label>
                                <input type="date" name="tmt_selesai" id="tmt_selesai" class="form-control @error('tmt_selesai') is-invalid @enderror" value="{{ old('tmt_selesai') }}">
                                <small class="text-muted">Kosongkan jika masih menjabat</small>
                                @error('tmt_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="mb-3">Data SK</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_sk" class="form-label">No. SK</label>
                                <input type="text" name="no_sk" id="no_sk" class="form-control @error('no_sk') is-invalid @enderror" value="{{ old('no_sk') }}">
                                @error('no_sk')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_sk" class="form-label">Tanggal SK</label>
                                <input type="date" name="tanggal_sk" id="tanggal_sk" class="form-control @error('tanggal_sk') is-invalid @enderror" value="{{ old('tanggal_sk') }}">
                                @error('tanggal_sk')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pejabat_sk" class="form-label">Pejabat yang Menandatangani SK</label>
                        <input type="text" name="pejabat_sk" id="pejabat_sk" class="form-control @error('pejabat_sk') is-invalid @enderror" value="{{ old('pejabat_sk') }}">
                        @error('pejabat_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_sk" class="form-label">File SK</label>
                        <input type="file" name="file_sk" id="file_sk" class="form-control @error('file_sk') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maks: 2MB</small>
                        @error('file_sk')
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
