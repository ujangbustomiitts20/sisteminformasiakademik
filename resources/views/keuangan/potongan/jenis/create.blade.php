@extends('layouts.app')

@section('title', 'Tambah Jenis Potongan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Tambah Jenis Potongan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('jenis-potongan.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Jenis Potongan <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach(\App\Models\JenisPotongan::KATEGORI as $key => $value)
                                        <option value="{{ $key }}" {{ old('kategori') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tipe Nilai <span class="text-danger">*</span></label>
                                <select name="tipe_nilai" id="tipe_nilai" class="form-select @error('tipe_nilai') is-invalid @enderror" required>
                                    @foreach(\App\Models\JenisPotongan::TIPE_NILAI as $key => $value)
                                        <option value="{{ $key }}" {{ old('tipe_nilai', 'nominal') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('tipe_nilai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nilai Default <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="nilai-prefix">Rp</span>
                                    <input type="number" name="nilai_default" class="form-control @error('nilai_default') is-invalid @enderror" value="{{ old('nilai_default', 0) }}" min="0" step="0.01" required>
                                    @error('nilai_default')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nilai Maksimal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nilai_max" class="form-control @error('nilai_max') is-invalid @enderror" value="{{ old('nilai_max') }}" min="0" step="0.01">
                                    @error('nilai_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Kosongkan jika tidak ada batas</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Prioritas</label>
                                <input type="number" name="prioritas" class="form-control @error('prioritas') is-invalid @enderror" value="{{ old('prioritas', 0) }}" min="0">
                                <small class="text-muted">Semakin kecil semakin tinggi prioritasnya</small>
                                @error('prioritas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Opsi</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="is_stackable" class="form-check-input" id="is_stackable" value="1" {{ old('is_stackable') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_stackable">
                                        Dapat digabung dengan potongan lain (Stackable)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('jenis-potongan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tipe_nilai').addEventListener('change', function() {
    const prefix = document.getElementById('nilai-prefix');
    if (this.value === 'persen') {
        prefix.textContent = '%';
    } else {
        prefix.textContent = 'Rp';
    }
});

// Trigger on load
document.getElementById('tipe_nilai').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
