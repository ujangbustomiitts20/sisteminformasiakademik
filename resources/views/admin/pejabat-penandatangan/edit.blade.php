@extends('layouts.app')

@section('title', 'Edit Pejabat Penandatangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Pejabat Penandatangan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.pejabat-penandatangan.index') }}">Pejabat Penandatangan</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.pejabat-penandatangan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.pejabat-penandatangan.update', $pejabatPenandatangan->hashid) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Pejabat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                                    <select name="nama_jabatan_id" class="form-select" required id="namaJabatanEdit">
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach($namaJabatans as $jab)
                                            <option value="{{ $jab->id }}" data-kategori="{{ $jab->kategori }}" data-kode="{{ $jab->kode }}" {{ old('nama_jabatan_id', $pejabatPenandatangan->nama_jabatan_id) == $jab->id ? 'selected' : '' }}>{{ $jab->nama }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Kode akan otomatis diambil dari jabatan</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori" class="form-select" required id="kategoriEdit">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($kategoris as $key => $label)
                                            <option value="{{ $key }}" {{ old('kategori', $pejabatPenandatangan->kategori) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        <h6 class="mb-3"><i class="bi bi-person me-2"></i>Pilih Pejabat <span class="text-danger">*</span></h6>
                        <p class="text-muted small">Pilih salah satu: Pegawai atau Dosen</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pegawai</label>
                                    <select name="pegawai_id" class="form-select" id="pegawaiEdit">
                                        <option value="">-- Pilih Pegawai --</option>
                                        @foreach($pegawais as $pegawai)
                                            <option value="{{ $pegawai->id }}" {{ old('pegawai_id', $pejabatPenandatangan->pegawai_id) == $pegawai->id ? 'selected' : '' }}>
                                                {{ $pegawai->nama }} - {{ $pegawai->nip ?? 'No NIP' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Atau Dosen</label>
                                    <select name="dosen_id" class="form-select" id="dosenEdit">
                                        <option value="">-- Pilih Dosen --</option>
                                        @foreach($dosens as $dosen)
                                            <option value="{{ $dosen->id }}" {{ old('dosen_id', $pejabatPenandatangan->dosen_id) == $dosen->id ? 'selected' : '' }}>
                                                {{ $dosen->nama }} - {{ $dosen->nip ?? $dosen->nidn ?? 'No NIP' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        <h6 class="mb-3"><i class="bi bi-calendar me-2"></i>Masa Berlaku & Urutan</h6>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Urutan</label>
                                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $pejabatPenandatangan->urutan ?? 0) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Berlaku Mulai</label>
                                    <input type="date" name="berlaku_mulai" class="form-control" value="{{ old('berlaku_mulai', $pejabatPenandatangan->berlaku_mulai?->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Berlaku Sampai</label>
                                    <input type="date" name="berlaku_sampai" class="form-control" value="{{ old('berlaku_sampai', $pejabatPenandatangan->berlaku_sampai?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $pejabatPenandatangan->catatan) }}</textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktifCheck" {{ old('aktif', $pejabatPenandatangan->aktif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="aktifCheck">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dokumen yang Ditandatangani</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Pilih dokumen yang dapat ditandatangani oleh pejabat ini. Jika tidak ada yang dipilih, pejabat dapat menandatangani semua dokumen.</p>
                        <div class="row">
                            @php
                                $selectedDocs = old('dokumen_terkait', $pejabatPenandatangan->dokumen_terkait ?? []);
                            @endphp
                            @foreach($dokumens as $key => $label)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dokumen_terkait[]" value="{{ $key }}" id="dok_{{ $key }}" {{ in_array($key, $selectedDocs) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="dok_{{ $key }}">{{ $label }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tanda Tangan Digital</h5>
                    </div>
                    <div class="card-body">
                        @if($pejabatPenandatangan->tanda_tangan)
                            <div class="text-center mb-3">
                                <img src="{{ $pejabatPenandatangan->tanda_tangan_url }}" alt="Tanda Tangan" class="img-fluid border rounded" style="max-height: 120px; background: #f8f9fa;">
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">{{ $pejabatPenandatangan->tanda_tangan ? 'Ganti' : 'Upload' }} Tanda Tangan</label>
                            <input type="file" name="tanda_tangan" class="form-control" accept="image/png,image/jpeg">
                            <small class="text-muted">Format: PNG/JPG, Max: 1MB</small>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Stempel</h5>
                    </div>
                    <div class="card-body">
                        @if($pejabatPenandatangan->stempel)
                            <div class="text-center mb-3">
                                <img src="{{ $pejabatPenandatangan->stempel_url }}" alt="Stempel" class="img-fluid border rounded" style="max-height: 120px; background: #f8f9fa;">
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">{{ $pejabatPenandatangan->stempel ? 'Ganti' : 'Upload' }} Stempel</label>
                            <input type="file" name="stempel" class="form-control" accept="image/png,image/jpeg">
                            <small class="text-muted">Format: PNG/JPG, Max: 1MB</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('admin.pejabat-penandatangan.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pegawaiSelect = document.getElementById('pegawaiEdit');
    const dosenSelect = document.getElementById('dosenEdit');
    const namaJabatanSelect = document.getElementById('namaJabatanEdit');
    const kategoriSelect = document.getElementById('kategoriEdit');

    // Mutual exclusion: clear dosen when pegawai selected
    pegawaiSelect?.addEventListener('change', function() {
        if (this.value) {
            dosenSelect.value = '';
        }
    });

    // Mutual exclusion: clear pegawai when dosen selected
    dosenSelect?.addEventListener('change', function() {
        if (this.value) {
            pegawaiSelect.value = '';
        }
    });

    // Auto-set kategori from NamaJabatan
    namaJabatanSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value && selected.dataset.kategori && kategoriSelect) {
            kategoriSelect.value = selected.dataset.kategori;
        }
    });
});
</script>
@endpush
@endsection
