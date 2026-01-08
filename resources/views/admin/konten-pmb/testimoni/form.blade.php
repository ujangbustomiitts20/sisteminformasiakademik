@extends('layouts.app')

@section('title', isset($testimoni) ? 'Edit Testimoni' : 'Tambah Testimoni')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-quote-right me-2"></i>{{ isset($testimoni) ? 'Edit Testimoni' : 'Tambah Testimoni' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($testimoni) ? route('pmb.konten-pmb.testimoni.update', $testimoni->hashid) : route('pmb.konten-pmb.testimoni.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($testimoni))
                @method('PUT')
                @endif
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $testimoni->nama ?? '') }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" value="{{ old('keterangan', $testimoni->keterangan ?? '') }}" required placeholder="Alumni 2023 - S1 Informatika">
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Testimoni <span class="text-danger">*</span></label>
                        <textarea name="testimoni" rows="5" class="form-control @error('testimoni') is-invalid @enderror" required>{{ old('testimoni', $testimoni->testimoni ?? '') }}</textarea>
                        @error('testimoni')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Foto</label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WebP. Max: 2MB. Rasio 1:1 recommended</small>
                        @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if(isset($testimoni) && $testimoni->foto)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $testimoni->foto) }}" alt="{{ $testimoni->nama }}" class="img-thumbnail rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan', $testimoni->urutan ?? 0) }}" min="0">
                        @error('urutan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active', $testimoni->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $testimoni->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                    <a href="{{ route('pmb.konten-pmb.testimoni.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
