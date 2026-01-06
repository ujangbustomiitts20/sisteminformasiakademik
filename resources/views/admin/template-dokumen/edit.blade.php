@extends('layouts.app')

@section('title', 'Edit Template - ' . $templateDokumen->nama)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.template-dokumen.index') }}">Template Dokumen</a></li>
            <li class="breadcrumb-item active">Edit {{ $templateDokumen->nama }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Template</h1>
            <p class="text-muted mb-0">{{ $templateDokumen->nama }}</p>
        </div>
        <div>
            <a href="{{ route('admin.template-dokumen.show', $templateDokumen->hashid) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('admin.template-dokumen.preview', $templateDokumen->hashid) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-1"></i> Preview
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.template-dokumen.update', $templateDokumen->hashid) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Basic Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Dasar</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Kode Template <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kode') is-invalid @enderror" name="kode" value="{{ old('kode', $templateDokumen->kode) }}" required>
                                <small class="text-muted">Gunakan snake_case</small>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Nama Template <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $templateDokumen->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('kategori') is-invalid @enderror" name="kategori" required>
                                    @foreach($kategoris as $key => $label)
                                        <option value="{{ $key }}" {{ old('kategori', $templateDokumen->kategori) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Deskripsi</label>
                                <input type="text" class="form-control" name="deskripsi" value="{{ old('deskripsi', $templateDokumen->deskripsi) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template Content -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Konten Template</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">Judul Dokumen</label>
                            <input type="text" class="form-control" name="template_judul" value="{{ old('template_judul', $templateDokumen->template_judul) }}" placeholder="SURAT KETERANGAN AKTIF KULIAH">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Format Nomor Surat</label>
                            <input type="text" class="form-control font-monospace" name="template_nomor" value="{{ old('template_nomor', $templateDokumen->template_nomor) }}" placeholder="{no_surat}/UN.XX/AK/{bulan_romawi}/{tahun}">
                            <small class="text-muted">Placeholder: {no_surat}, {bulan_romawi}, {tahun}, {tanggal_sekarang}</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Isi Dokumen</label>
                            <textarea class="form-control" name="template_isi" rows="10" placeholder="Yang bertanda tangan di bawah ini...">{{ old('template_isi', $templateDokumen->template_isi) }}</textarea>
                            <small class="text-muted">Gunakan placeholder seperti {nama}, {nim}, {program_studi} dll. Lihat daftar placeholder di sidebar.</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Penutup</label>
                            <textarea class="form-control" name="template_penutup" rows="3">{{ old('template_penutup', $templateDokumen->template_penutup) }}</textarea>
                        </div>
                        
                        <div>
                            <label class="form-label">Catatan Bawah</label>
                            <textarea class="form-control" name="catatan_bawah" rows="2" placeholder="Catatan kaki dokumen (opsional)">{{ old('catatan_bawah', $templateDokumen->catatan_bawah) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Pejabat Settings -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Pejabat Penandatangan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pejabat 1</label>
                                <select class="form-select" name="pejabat_1_id">
                                    <option value="">-- Tidak Ada --</option>
                                    @foreach($pejabats as $pejabat)
                                        <option value="{{ $pejabat->id }}" {{ old('pejabat_1_id', $templateDokumen->pejabat_1_id) == $pejabat->id ? 'selected' : '' }}>
                                            {{ $pejabat->nama }} - {{ $pejabat->jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Label Pejabat 1</label>
                                <input type="text" class="form-control" name="pejabat_1_label" value="{{ old('pejabat_1_label', $templateDokumen->pejabat_1_label) }}" placeholder="Contoh: Kepala Bagian Akademik">
                                <small class="text-muted">Label custom untuk ditampilkan pada dokumen</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pejabat 2 (Opsional)</label>
                                <select class="form-select" name="pejabat_2_id">
                                    <option value="">-- Tidak Ada --</option>
                                    @foreach($pejabats as $pejabat)
                                        <option value="{{ $pejabat->id }}" {{ old('pejabat_2_id', $templateDokumen->pejabat_2_id) == $pejabat->id ? 'selected' : '' }}>
                                            {{ $pejabat->nama }} - {{ $pejabat->jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Label Pejabat 2</label>
                                <input type="text" class="form-control" name="pejabat_2_label" value="{{ old('pejabat_2_label', $templateDokumen->pejabat_2_label) }}" placeholder="Contoh: Dekan">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Display Options -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Opsi Tampilan</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="tampilkan_kop" id="tampilkan_kop" {{ old('tampilkan_kop', $templateDokumen->tampilkan_kop) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tampilkan_kop">Tampilkan Kop Surat</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="tampilkan_logo" id="tampilkan_logo" {{ old('tampilkan_logo', $templateDokumen->tampilkan_logo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tampilkan_logo">Tampilkan Logo</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="tampilkan_ttd_digital" id="tampilkan_ttd_digital" {{ old('tampilkan_ttd_digital', $templateDokumen->tampilkan_ttd_digital) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tampilkan_ttd_digital">Tampilkan Tanda Tangan Digital</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="tampilkan_stempel" id="tampilkan_stempel" {{ old('tampilkan_stempel', $templateDokumen->tampilkan_stempel) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tampilkan_stempel">Tampilkan Stempel</label>
                        </div>
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="aktif" id="aktif" {{ old('aktif', $templateDokumen->aktif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="aktif">Template Aktif</label>
                        </div>
                    </div>
                </div>

                <!-- Placeholder Reference -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-code-slash me-2"></i>Referensi Placeholder</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-2">DARI FIELD TEMPLATE</label>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($templateDokumen->fields as $field)
                                    <code class="bg-light px-2 py-1 rounded small user-select-all">{!! '{'.$field->kode_field.'}' !!}</code>
                                @empty
                                    <span class="text-muted small">Belum ada field. <a href="{{ route('admin.template-dokumen.fields', $templateDokumen->hashid) }}">Tambah field</a></span>
                                @endforelse
                            </div>
                        </div>
                        <div>
                            <label class="form-label text-muted small mb-2">SISTEM (OTOMATIS)</label>
                            <div class="d-flex flex-wrap gap-1">
                                <code class="bg-light px-2 py-1 rounded small user-select-all">{tanggal_sekarang}</code>
                                <code class="bg-light px-2 py-1 rounded small user-select-all">{hari_ini}</code>
                                <code class="bg-light px-2 py-1 rounded small user-select-all">{bulan_romawi}</code>
                                <code class="bg-light px-2 py-1 rounded small user-select-all">{tahun}</code>
                                <code class="bg-light px-2 py-1 rounded small user-select-all">{nama_institusi}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.template-dokumen.fields', $templateDokumen->hashid) }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-list-check me-1"></i> Kelola Field
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
