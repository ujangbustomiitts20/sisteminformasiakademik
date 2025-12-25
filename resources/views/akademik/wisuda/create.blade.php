@extends('layouts.app')

@section('title', 'Tambah Periode Wisuda')

@section('content')
<div class="page-title">
    <h4>Tambah Periode Wisuda</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('wisuda.index') }}">Wisuda</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Form Periode Wisuda</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('wisuda.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                                <select name="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tahun Akademik --</option>
                                    @foreach($tahunAkademik as $ta)
                                    <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun }} {{ $ta->semester }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('tahun_akademik_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Periode <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                       value="{{ old('nama') }}" placeholder="Contoh: Wisuda Periode I 2024/2025" required>
                                @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Wisuda <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_wisuda" class="form-control @error('tanggal_wisuda') is-invalid @enderror" 
                                       value="{{ old('tanggal_wisuda') }}" required>
                                @error('tanggal_wisuda')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" 
                                       value="{{ old('lokasi') }}" placeholder="Lokasi pelaksanaan wisuda">
                                @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Buka Pendaftaran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_buka_pendaftaran" class="form-control @error('tanggal_buka_pendaftaran') is-invalid @enderror" 
                                       value="{{ old('tanggal_buka_pendaftaran') }}" required>
                                @error('tanggal_buka_pendaftaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Tutup Pendaftaran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_tutup_pendaftaran" class="form-control @error('tanggal_tutup_pendaftaran') is-invalid @enderror" 
                                       value="{{ old('tanggal_tutup_pendaftaran') }}" required>
                                @error('tanggal_tutup_pendaftaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Yudisium</label>
                                <input type="date" name="tanggal_yudisium" class="form-control @error('tanggal_yudisium') is-invalid @enderror" 
                                       value="{{ old('tanggal_yudisium') }}">
                                @error('tanggal_yudisium')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kuota Peserta</label>
                                <input type="number" name="kuota" class="form-control @error('kuota') is-invalid @enderror" 
                                       value="{{ old('kuota') }}" min="1" placeholder="Kosongkan jika tidak ada batasan">
                                @error('kuota')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Biaya Wisuda <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya_wisuda" class="form-control @error('biaya_wisuda') is-invalid @enderror" 
                                   value="{{ old('biaya_wisuda', 0) }}" min="0" required>
                        </div>
                        @error('biaya_wisuda')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Persyaratan</label>
                        <textarea name="persyaratan" class="form-control @error('persyaratan') is-invalid @enderror" 
                                  rows="4" placeholder="Daftar persyaratan wisuda...">{{ old('persyaratan') }}</textarea>
                        @error('persyaratan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('wisuda.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <h6>Alur Wisuda:</h6>
                <ol class="text-muted small">
                    <li>Buat periode wisuda (Draft)</li>
                    <li>Buka pendaftaran (Status: Dibuka)</li>
                    <li>Mahasiswa mendaftar wisuda</li>
                    <li>Verifikasi berkas pendaftaran</li>
                    <li>Proses yudisium</li>
                    <li>Tutup pendaftaran</li>
                    <li>Pelaksanaan wisuda</li>
                    <li>Tandai selesai</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
