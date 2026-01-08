@extends('layouts.app')

@section('title', isset($galeri) ? 'Edit Galeri' : 'Tambah Galeri')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-images me-2"></i>{{ isset($galeri) ? 'Edit Galeri' : 'Tambah Galeri' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($galeri) ? route('pmb.konten-pmb.galeri.update', $galeri->hashid) : route('pmb.konten-pmb.galeri.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($galeri))
                @method('PUT')
                @endif
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $galeri->judul ?? '') }}" required>
                        @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                            <option value="">Pilih Kategori</option>
                            <option value="kampus" {{ old('kategori', $galeri->kategori ?? '') == 'kampus' ? 'selected' : '' }}>Kampus</option>
                            <option value="kegiatan" {{ old('kategori', $galeri->kategori ?? '') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                            <option value="fasilitas" {{ old('kategori', $galeri->kategori ?? '') == 'fasilitas' ? 'selected' : '' }}>Fasilitas</option>
                            <option value="wisuda" {{ old('kategori', $galeri->kategori ?? '') == 'wisuda' ? 'selected' : '' }}>Wisuda</option>
                            <option value="mahasiswa" {{ old('kategori', $galeri->kategori ?? '') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="seminar" {{ old('kategori', $galeri->kategori ?? '') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                            <option value="lainnya" {{ old('kategori', $galeri->kategori ?? '') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $galeri->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Gambar {{ isset($galeri) ? '' : '<span class="text-danger">*</span>' }}</label>
                        <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*" {{ isset($galeri) ? '' : 'required' }}>
                        <small class="text-muted">Format: JPG, JPEG, PNG, WebP. Max: 2MB</small>
                        @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if(isset($galeri) && $galeri->gambar)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $galeri->gambar) }}" alt="{{ $galeri->judul }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan', $galeri->urutan ?? 0) }}" min="0">
                        @error('urutan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active', $galeri->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $galeri->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
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
                    <a href="{{ route('pmb.konten-pmb.galeri.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
