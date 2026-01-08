@extends('layouts.app')

@section('title', isset($keunggulan) ? 'Edit Keunggulan' : 'Tambah Keunggulan')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-star me-2"></i>{{ isset($keunggulan) ? 'Edit Keunggulan' : 'Tambah Keunggulan' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($keunggulan) ? route('pmb.konten-pmb.keunggulan.update', $keunggulan->hashid) : route('pmb.konten-pmb.keunggulan.store') }}" method="POST">
                @csrf
                @if(isset($keunggulan))
                @method('PUT')
                @endif
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $keunggulan->judul ?? '') }}" required>
                        @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Icon (Bootstrap Icons) <span class="text-danger">*</span></label>
                        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $keunggulan->icon ?? '') }}" required placeholder="mortarboard, award, people">
                        <small class="text-muted">Lihat: <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></small>
                        @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $keunggulan->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan', $keunggulan->urutan ?? 0) }}" min="0">
                        @error('urutan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active', $keunggulan->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $keunggulan->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Preview Icon</label>
                        <div class="p-3 bg-light rounded text-center">
                            <i id="iconPreview" class="bi bi-{{ old('icon', $keunggulan->icon ?? 'star') }}" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                    <a href="{{ route('pmb.konten-pmb.keunggulan.index') }}" class="btn btn-secondary">
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
document.querySelector('input[name="icon"]').addEventListener('input', function() {
    document.getElementById('iconPreview').className = 'bi bi-' + this.value;
});
</script>
@endpush
