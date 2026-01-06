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
                                    <label class="form-label">Kode <span class="text-danger">*</span></label>
                                    <input type="text" name="kode" class="form-control" required value="{{ old('kode', $pejabatPenandatangan->kode) }}">
                                    <small class="text-muted">Kode unik untuk referensi di sistem</small>
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
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                                    <select name="nama_jabatan_id" class="form-select" id="namaJabatanEdit">
                                        <option value="">-- Pilih dari Daftar Jabatan --</option>
                                        @foreach($namaJabatans as $jab)
                                            <option value="{{ $jab->id }}" data-kategori="{{ $jab->kategori }}" {{ old('nama_jabatan_id', $pejabatPenandatangan->nama_jabatan_id) == $jab->id ? 'selected' : '' }}>{{ $jab->nama }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Pilih dari daftar jabatan atau isi manual di bawah</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Atau Isi Jabatan Manual</label>
                                    <input type="text" name="jabatan" class="form-control" id="jabatanManualEdit" value="{{ old('jabatan', $pejabatPenandatangan->jabatan) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Urutan</label>
                                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $pejabatPenandatangan->urutan) }}" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        <h6 class="mb-3"><i class="bi bi-person me-2"></i>Data Pejabat</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilih dari Pegawai</label>
                                    <select name="pegawai_id" class="form-select" id="pegawaiEdit">
                                        <option value="">-- Pilih Pegawai --</option>
                                        @foreach($pegawais as $pegawai)
                                            <option value="{{ $pegawai->id }}" 
                                                data-nama="{{ $pegawai->nama }}" 
                                                data-nip="{{ $pegawai->nip }}" 
                                                data-pangkat="{{ $pegawai->pangkat }} ({{ $pegawai->golongan }})"
                                                {{ old('pegawai_id', $pejabatPenandatangan->pegawai_id) == $pegawai->id ? 'selected' : '' }}>
                                                {{ $pegawai->nama }} - {{ $pegawai->nip ?? 'No NIP' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Atau Pilih dari Dosen</label>
                                    <select name="dosen_id" class="form-select" id="dosenEdit">
                                        <option value="">-- Pilih Dosen --</option>
                                        @foreach($dosens as $dosen)
                                            <option value="{{ $dosen->id }}" 
                                                data-nama="{{ $dosen->nama }}" 
                                                data-nip="{{ $dosen->nip }}" 
                                                data-gelar-depan="{{ $dosen->gelar_depan }}" 
                                                data-gelar-belakang="{{ $dosen->gelar_belakang }}"
                                                {{ old('dosen_id', $pejabatPenandatangan->dosen_id) == $dosen->id ? 'selected' : '' }}>
                                                {{ $dosen->nama }} - {{ $dosen->nip ?? 'No NIP' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Gelar Depan</label>
                                    <input type="text" name="gelar_depan" class="form-control" id="gelarDepanEdit" value="{{ old('gelar_depan', $pejabatPenandatangan->gelar_depan) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control" id="namaEdit" value="{{ old('nama', $pejabatPenandatangan->nama) }}" placeholder="Isi jika tidak pilih pegawai/dosen">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Gelar Belakang</label>
                                    <input type="text" name="gelar_belakang" class="form-control" id="gelarBelakangEdit" value="{{ old('gelar_belakang', $pejabatPenandatangan->gelar_belakang) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">NIP/NIDN</label>
                                    <input type="text" name="nip" class="form-control" id="nipEdit" value="{{ old('nip', $pejabatPenandatangan->nip) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pangkat/Golongan</label>
                                    <input type="text" name="pangkat_golongan" class="form-control" id="pangkatEdit" value="{{ old('pangkat_golongan', $pejabatPenandatangan->pangkat_golongan) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Berlaku Mulai</label>
                                    <input type="date" name="berlaku_mulai" class="form-control" value="{{ old('berlaku_mulai', $pejabatPenandatangan->berlaku_mulai?->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
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
    // Auto-fill from Pegawai
    const pegawaiSelect = document.getElementById('pegawaiEdit');
    const dosenSelect = document.getElementById('dosenEdit');
    const namaInput = document.getElementById('namaEdit');
    const nipInput = document.getElementById('nipEdit');
    const gelarDepanInput = document.getElementById('gelarDepanEdit');
    const gelarBelakangInput = document.getElementById('gelarBelakangEdit');
    const pangkatInput = document.getElementById('pangkatEdit');

    pegawaiSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            namaInput.value = selected.dataset.nama || '';
            nipInput.value = selected.dataset.nip || '';
            pangkatInput.value = selected.dataset.pangkat || '';
            dosenSelect.value = ''; // Clear dosen selection
        }
    });

    dosenSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            namaInput.value = selected.dataset.nama || '';
            nipInput.value = selected.dataset.nip || '';
            gelarDepanInput.value = selected.dataset.gelarDepan || '';
            gelarBelakangInput.value = selected.dataset.gelarBelakang || '';
            pegawaiSelect.value = ''; // Clear pegawai selection
        }
    });

    // Auto-fill jabatan from NamaJabatan
    const namaJabatanSelect = document.getElementById('namaJabatanEdit');
    const jabatanManualInput = document.getElementById('jabatanManualEdit');
    const kategoriSelect = document.getElementById('kategoriEdit');

    namaJabatanSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            jabatanManualInput.value = selected.text;
            // Set kategori based on jabatan
            if (selected.dataset.kategori && kategoriSelect) {
                kategoriSelect.value = selected.dataset.kategori;
            }
        }
    });
});
</script>
@endpush
@endsection
