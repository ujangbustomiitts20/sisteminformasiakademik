@extends('layouts.app')

@section('title', isset($fasilitas) ? 'Edit Fasilitas' : 'Tambah Fasilitas')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-building me-2"></i>{{ isset($fasilitas) ? 'Edit Fasilitas' : 'Tambah Fasilitas' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($fasilitas) ? route('pmb.konten-pmb.fasilitas.update', $fasilitas->id) : route('pmb.konten-pmb.fasilitas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($fasilitas))
                @method('PUT')
                @endif
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $fasilitas->nama ?? '') }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Icon (Bootstrap Icons)</label>
                        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $fasilitas->icon ?? '') }}" placeholder="building, book, wifi">
                        <small class="text-muted">Lihat: <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></small>
                        @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $fasilitas->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Gambar</label>
                        <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WebP. Max: 2MB</small>
                        @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if(isset($fasilitas) && $fasilitas->gambar)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $fasilitas->gambar) }}" alt="{{ $fasilitas->nama }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan', $fasilitas->urutan ?? 0) }}" min="0">
                        @error('urutan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active', $fasilitas->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $fasilitas->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    @if(isset($fasilitas->icon) || old('icon'))
                    <div class="col-12">
                        <label class="form-label">Preview Icon</label>
                        <div class="p-3 bg-light rounded text-center">
                            <i id="iconPreview" class="bi bi-{{ old('icon', $fasilitas->icon ?? 'building') }}" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                    <a href="{{ route('pmb.konten-pmb.fasilitas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('input[name="icon"]')?.addEventListener('input', function() {
    const preview = document.getElementById('iconPreview');
    if (preview) {
        preview.className = 'bi bi-' + this.value;
    }
});
</script>
@endpush
