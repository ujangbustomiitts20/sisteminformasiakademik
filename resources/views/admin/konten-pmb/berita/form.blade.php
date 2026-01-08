@extends('layouts.app')

@section('title', isset($berita) ? 'Edit Berita' : 'Tambah Berita')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-newspaper me-2"></i>{{ isset($berita) ? 'Edit Berita' : 'Tambah Berita' }}</h5>
            <a href="{{ route('pmb.konten-pmb.berita.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ isset($berita) ? route('pmb.konten-pmb.berita.update', $berita->hashid) : route('pmb.konten-pmb.berita.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($berita))
                @method('PUT')
                @endif
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $berita->judul ?? '') }}" required>
                            @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ringkasan</label>
                            <textarea name="ringkasan" class="form-control @error('ringkasan') is-invalid @enderror" rows="3">{{ old('ringkasan', $berita->ringkasan ?? '') }}</textarea>
                            <small class="text-muted">Maksimal 200 karakter</small>
                            @error('ringkasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Konten <span class="text-danger">*</span></label>
                            <textarea name="konten" id="konten" class="form-control @error('konten') is-invalid @enderror" rows="15">{{ old('konten', $berita->konten ?? '') }}</textarea>
                            @error('konten')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Pengaturan</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                        <option value="berita" {{ old('kategori', $berita->kategori ?? '') == 'berita' ? 'selected' : '' }}>Berita</option>
                                        <option value="pengumuman" {{ old('kategori', $berita->kategori ?? '') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                        <option value="tips" {{ old('kategori', $berita->kategori ?? '') == 'tips' ? 'selected' : '' }}>Tips & Trik</option>
                                    </select>
                                    @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="is_published" class="form-select">
                                        <option value="1" {{ old('is_published', $berita->is_published ?? 1) == 1 ? 'selected' : '' }}>Publish</option>
                                        <option value="0" {{ old('is_published', $berita->is_published ?? 1) == 0 ? 'selected' : '' }}>Draft</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Publish</label>
                                    <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', isset($berita) && $berita->published_at ? $berita->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Penulis</label>
                                    <input type="text" name="penulis" class="form-control" value="{{ old('penulis', $berita->penulis ?? auth()->user()->name ?? 'Admin') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Gambar</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Gambar Utama</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/*">
                                    <small class="text-muted">Resolusi: 800x400 pixel</small>
                                    @if(isset($berita) && $berita->gambar)
                                    <div class="mt-2">
                                        <img src="{{ $berita->gambar_url }}" alt="" class="img-fluid rounded">
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">SEO</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Tags</label>
                                    <input type="text" name="tags" class="form-control" value="{{ old('tags', $berita->tags ?? '') }}" placeholder="pmb, pendaftaran, mahasiswa">
                                    <small class="text-muted">Pisahkan dengan koma</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('#konten'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
    })
    .catch(error => console.error(error));
</script>
@endpush
