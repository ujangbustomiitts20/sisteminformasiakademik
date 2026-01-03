@extends('layouts.app')

@section('title', isset($faq) ? 'Edit FAQ' : 'Tambah FAQ')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>{{ isset($faq) ? 'Edit FAQ' : 'Tambah FAQ' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($faq) ? route('pmb.konten-pmb.faq.update', $faq->id) : route('pmb.konten-pmb.faq.store') }}" method="POST">
                @csrf
                @if(isset($faq))
                @method('PUT')
                @endif
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                            <option value="">Pilih Kategori</option>
                            <option value="pendaftaran" {{ old('kategori', $faq->kategori ?? '') == 'pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                            <option value="biaya" {{ old('kategori', $faq->kategori ?? '') == 'biaya' ? 'selected' : '' }}>Biaya</option>
                            <option value="beasiswa" {{ old('kategori', $faq->kategori ?? '') == 'beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                            <option value="jadwal" {{ old('kategori', $faq->kategori ?? '') == 'jadwal' ? 'selected' : '' }}>Jadwal</option>
                            <option value="program_studi" {{ old('kategori', $faq->kategori ?? '') == 'program_studi' ? 'selected' : '' }}>Program Studi</option>
                            <option value="umum" {{ old('kategori', $faq->kategori ?? '') == 'umum' ? 'selected' : '' }}>Umum</option>
                        </select>
                        @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan', $faq->urutan ?? 0) }}" min="0">
                        @error('urutan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active', $faq->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $faq->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                        <input type="text" name="pertanyaan" class="form-control @error('pertanyaan') is-invalid @enderror" value="{{ old('pertanyaan', $faq->pertanyaan ?? '') }}" required>
                        @error('pertanyaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Jawaban <span class="text-danger">*</span></label>
                        <textarea name="jawaban" rows="5" class="form-control @error('jawaban') is-invalid @enderror" required>{{ old('jawaban', $faq->jawaban ?? '') }}</textarea>
                        @error('jawaban')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                    <a href="{{ route('pmb.konten-pmb.faq.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
