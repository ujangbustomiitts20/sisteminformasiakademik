@extends('layouts.app')

@section('title', 'Tambah Periode Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tambah Periode Kegiatan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.kegiatan-lapangan.periode.index') }}">Periode</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.kegiatan-lapangan.periode.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.kegiatan-lapangan.periode.store') }}" method="POST">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
                        <select name="jenis_kegiatan_id" class="form-select @error('jenis_kegiatan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($jenisKegiatan as $jenis)
                            <option value="{{ $jenis->id }}" {{ old('jenis_kegiatan_id') == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('jenis_kegiatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                        <select name="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Akademik --</option>
                            @foreach($tahunAkademik as $ta)
                            <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                {{ $ta->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('tahun_akademik_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label">Nama Periode <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                            value="{{ old('nama') }}" placeholder="Contoh: PKL Semester Ganjil 2024/2025" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai Daftar <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai_daftar" class="form-control @error('tanggal_mulai_daftar') is-invalid @enderror" 
                            value="{{ old('tanggal_mulai_daftar') }}" required>
                        @error('tanggal_mulai_daftar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai Daftar <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_selesai_daftar" class="form-control @error('tanggal_selesai_daftar') is-invalid @enderror" 
                            value="{{ old('tanggal_selesai_daftar') }}" required>
                        @error('tanggal_selesai_daftar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai Kegiatan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai_kegiatan" class="form-control @error('tanggal_mulai_kegiatan') is-invalid @enderror" 
                            value="{{ old('tanggal_mulai_kegiatan') }}" required>
                        @error('tanggal_mulai_kegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai Kegiatan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_selesai_kegiatan" class="form-control @error('tanggal_selesai_kegiatan') is-invalid @enderror" 
                            value="{{ old('tanggal_selesai_kegiatan') }}" required>
                        @error('tanggal_selesai_kegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Kuota Mahasiswa</label>
                        <input type="number" name="kuota" class="form-control @error('kuota') is-invalid @enderror" 
                            value="{{ old('kuota') }}" min="1" placeholder="Kosongkan jika tanpa batas">
                        @error('kuota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="dibuka" {{ old('status') == 'dibuka' ? 'selected' : '' }}>Dibuka</option>
                            <option value="ditutup" {{ old('status') == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                            rows="3" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.kegiatan-lapangan.periode.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
