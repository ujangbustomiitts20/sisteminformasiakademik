@extends('layouts.app')

@section('title', 'Tambah Pertanyaan EDOM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Tambah Pertanyaan EDOM</h1>
            <p class="text-muted mb-0">Buat pertanyaan evaluasi dosen baru</p>
        </div>
        <a href="{{ route('admin.edom.pertanyaan') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.edom.pertanyaan.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kode <span class="text-danger">*</span></label>
                                <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror" 
                                       value="{{ old('kode') }}" placeholder="P01" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategoris as $kode => $nama)
                                        <option value="{{ $kode }}" {{ old('kategori') == $kode ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Urutan <span class="text-danger">*</span></label>
                                <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" 
                                       value="{{ old('urutan', 1) }}" min="1" required>
                                @error('urutan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                            <textarea name="pertanyaan" class="form-control @error('pertanyaan') is-invalid @enderror" 
                                      rows="3" placeholder="Masukkan pertanyaan evaluasi" required>{{ old('pertanyaan') }}</textarea>
                            @error('pertanyaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktifkan pertanyaan ini</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.edom.pertanyaan') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Kategori Kompetensi</h6>
                    <ul class="small mb-0">
                        <li><strong>Pedagogik:</strong> Kemampuan mengelola pembelajaran</li>
                        <li><strong>Profesional:</strong> Penguasaan materi dan keilmuan</li>
                        <li><strong>Kepribadian:</strong> Sikap dan kepribadian dosen</li>
                        <li><strong>Sosial:</strong> Kemampuan berkomunikasi dan berinteraksi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
