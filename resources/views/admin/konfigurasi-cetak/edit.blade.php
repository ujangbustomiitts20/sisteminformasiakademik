@extends('layouts.app')

@section('title', 'Edit Konfigurasi Cetak')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Konfigurasi Cetak</h1>
            <p class="text-muted mb-0">{{ $konfigurasiCetak->nama }}</p>
        </div>
        <a href="{{ route('konfigurasi-cetak.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <form action="{{ route('konfigurasi-cetak.update', $konfigurasiCetak->hashid) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Informasi Dasar -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-info-circle me-2"></i>Informasi Dasar
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Kode <span class="text-muted">(tidak dapat diubah)</span></label>
                            <input type="text" class="form-control" value="{{ $konfigurasiCetak->kode }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                   value="{{ old('nama', $konfigurasiCetak->nama) }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Judul Dokumen</label>
                            <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" 
                                   value="{{ old('judul', $konfigurasiCetak->judul) }}"
                                   placeholder="Judul yang ditampilkan di dokumen">
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   {{ old('is_active', $konfigurasiCetak->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Pengaturan Kertas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-file-earmark me-2"></i>Pengaturan Kertas
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ukuran Kertas <span class="text-danger">*</span></label>
                                <select name="ukuran_kertas" class="form-select @error('ukuran_kertas') is-invalid @enderror">
                                    <option value="a4" {{ old('ukuran_kertas', $konfigurasiCetak->ukuran_kertas) == 'a4' ? 'selected' : '' }}>A4 (210 x 297 mm)</option>
                                    <option value="letter" {{ old('ukuran_kertas', $konfigurasiCetak->ukuran_kertas) == 'letter' ? 'selected' : '' }}>Letter (216 x 279 mm)</option>
                                    <option value="legal" {{ old('ukuran_kertas', $konfigurasiCetak->ukuran_kertas) == 'legal' ? 'selected' : '' }}>Legal (216 x 356 mm)</option>
                                    <option value="f4" {{ old('ukuran_kertas', $konfigurasiCetak->ukuran_kertas) == 'f4' ? 'selected' : '' }}>F4/Folio (215 x 330 mm)</option>
                                </select>
                                @error('ukuran_kertas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orientasi <span class="text-danger">*</span></label>
                                <select name="orientasi" class="form-select @error('orientasi') is-invalid @enderror">
                                    <option value="portrait" {{ old('orientasi', $konfigurasiCetak->orientasi) == 'portrait' ? 'selected' : '' }}>Portrait (Tegak)</option>
                                    <option value="landscape" {{ old('orientasi', $konfigurasiCetak->orientasi) == 'landscape' ? 'selected' : '' }}>Landscape (Mendatar)</option>
                                </select>
                                @error('orientasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <label class="form-label">Margin (mm)</label>
                        <div class="row">
                            <div class="col-6 col-md-3 mb-3">
                                <label class="form-label small text-muted">Atas</label>
                                <input type="number" name="margin_top" class="form-control @error('margin_top') is-invalid @enderror" 
                                       value="{{ old('margin_top', $konfigurasiCetak->margin_top) }}" min="0" max="100" step="1">
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="form-label small text-muted">Kanan</label>
                                <input type="number" name="margin_right" class="form-control @error('margin_right') is-invalid @enderror" 
                                       value="{{ old('margin_right', $konfigurasiCetak->margin_right) }}" min="0" max="100" step="1">
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="form-label small text-muted">Bawah</label>
                                <input type="number" name="margin_bottom" class="form-control @error('margin_bottom') is-invalid @enderror" 
                                       value="{{ old('margin_bottom', $konfigurasiCetak->margin_bottom) }}" min="0" max="100" step="1">
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <label class="form-label small text-muted">Kiri</label>
                                <input type="number" name="margin_left" class="form-control @error('margin_left') is-invalid @enderror" 
                                       value="{{ old('margin_left', $konfigurasiCetak->margin_left) }}" min="0" max="100" step="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengaturan Tampilan -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-layout-text-window me-2"></i>Pengaturan Tampilan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="tampilkan_kop" id="tampilkan_kop" 
                                       {{ old('tampilkan_kop', $konfigurasiCetak->tampilkan_kop) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tampilkan_kop">
                                    <strong>Tampilkan Kop Surat</strong>
                                </label>
                            </div>
                            <small class="text-muted">Header dengan nama institusi, alamat, kontak</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="tampilkan_logo" id="tampilkan_logo" 
                                       {{ old('tampilkan_logo', $konfigurasiCetak->tampilkan_logo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tampilkan_logo">
                                    <strong>Tampilkan Logo</strong>
                                </label>
                            </div>
                            <small class="text-muted">Logo institusi pada kop surat</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="tampilkan_footer" id="tampilkan_footer" 
                                       {{ old('tampilkan_footer', $konfigurasiCetak->tampilkan_footer) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tampilkan_footer">
                                    <strong>Tampilkan Footer</strong>
                                </label>
                            </div>
                            <small class="text-muted">Footer dengan informasi sistem</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Kaki</label>
                            <textarea name="catatan_kaki" class="form-control" rows="2" 
                                      placeholder="Catatan tambahan di bagian bawah dokumen">{{ old('catatan_kaki', $konfigurasiCetak->catatan_kaki) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Pengaturan Tanda Tangan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-pen me-2"></i>Pengaturan Tanda Tangan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="tampilkan_ttd" id="tampilkan_ttd" 
                                       {{ old('tampilkan_ttd', $konfigurasiCetak->tampilkan_ttd) ? 'checked' : '' }}
                                       onchange="toggleTtdFields()">
                                <label class="form-check-label" for="tampilkan_ttd">
                                    <strong>Tampilkan Bagian Tanda Tangan</strong>
                                </label>
                            </div>
                        </div>

                        <div id="ttd-fields">
                            <div class="mb-3">
                                <label class="form-label">Jabatan Penandatangan</label>
                                <input type="text" name="jabatan_ttd" class="form-control" 
                                       value="{{ old('jabatan_ttd', $konfigurasiCetak->jabatan_ttd) }}"
                                       placeholder="Contoh: Kepala Bagian Akademik">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Penandatangan</label>
                                <input type="text" name="nama_ttd" class="form-control" 
                                       value="{{ old('nama_ttd', $konfigurasiCetak->nama_ttd) }}"
                                       placeholder="Kosongkan untuk menggunakan data dinamis">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NIP Penandatangan</label>
                                <input type="text" name="nip_ttd" class="form-control" 
                                       value="{{ old('nip_ttd', $konfigurasiCetak->nip_ttd) }}"
                                       placeholder="Kosongkan untuk menggunakan data dinamis">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body d-flex justify-content-between">
                <a href="{{ route('konfigurasi-cetak.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Batal
                </a>
                <div>
                    <a href="{{ route('konfigurasi-cetak.preview-pdf', $konfigurasiCetak->hashid) }}" class="btn btn-outline-info me-2" target="_blank">
                        <i class="bi bi-file-pdf me-1"></i>Preview PDF
                    </a>
                    <a href="{{ route('konfigurasi-cetak.preview', $konfigurasiCetak->hashid) }}" class="btn btn-outline-primary me-2" target="_blank">
                        <i class="bi bi-eye me-1"></i>Preview HTML
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleTtdFields() {
    const checkbox = document.getElementById('tampilkan_ttd');
    const fields = document.getElementById('ttd-fields');
    fields.style.display = checkbox.checked ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    toggleTtdFields();
});
</script>
@endpush
@endsection
