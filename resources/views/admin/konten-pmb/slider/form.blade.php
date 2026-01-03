@extends('layouts.app')

@section('title', isset($slider) ? 'Edit Slider' : 'Tambah Slider')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>{{ isset($slider) ? 'Edit Slider' : 'Tambah Slider' }}</h5>
                    <a href="{{ route('pmb.konten-pmb.slider.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ isset($slider) ? route('pmb.konten-pmb.slider.update', $slider->id) : route('pmb.konten-pmb.slider.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($slider))
                        @method('PUT')
                        @endif
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $slider->judul ?? '') }}" required>
                                @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi', $slider->deskripsi ?? '') }}</textarea>
                                @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Gambar {{ isset($slider) ? '' : '*' }}</label>
                                <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*" {{ isset($slider) ? '' : 'required' }}>
                                <small class="text-muted">Resolusi rekomendasi: 1920x600 pixel</small>
                                @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if(isset($slider) && $slider->gambar)
                                <div class="mt-2">
                                    <img src="{{ $slider->gambar_url }}" alt="" class="rounded" style="max-height: 100px;">
                                </div>
                                @endif
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Link</label>
                                <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $slider->link ?? '') }}" placeholder="https://...">
                                @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Teks Tombol</label>
                                <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $slider->button_text ?? 'Selengkapnya') }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $slider->urutan ?? 0) }}" min="0">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', $slider->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active', $slider->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
