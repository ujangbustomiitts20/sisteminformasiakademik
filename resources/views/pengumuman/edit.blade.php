@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="page-title">
    <h4>Edit Pengumuman</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pengumuman.index') }}">Pengumuman</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2"></i>Form Edit Pengumuman
            </div>
            <div class="card-body">
                <form action="{{ route('pengumuman.update', $pengumuman) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" 
                            value="{{ old('judul', $pengumuman->judul) }}" required>
                        @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="target" class="form-label">Target Pengumuman</label>
                        <select name="target" id="target" class="form-select @error('target') is-invalid @enderror">
                            <option value="">-- Semua --</option>
                            <option value="mahasiswa" {{ old('target', $pengumuman->target) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ old('target', $pengumuman->target) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        </select>
                        @error('target')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="konten" class="form-label">Isi Pengumuman <span class="text-danger">*</span></label>
                        <textarea name="konten" id="konten" class="form-control @error('konten') is-invalid @enderror" rows="10" required>{{ old('konten', $pengumuman->konten) }}</textarea>
                        @error('konten')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $pengumuman->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktifkan pengumuman
                            </label>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update
                        </button>
                        <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
