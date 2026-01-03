@extends('layouts.app')

@section('title', 'Verifikasi Dokumen')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Verifikasi Dokumen</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pmb.calon-mahasiswa.index') }}">Calon Mahasiswa</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pmb.calon-mahasiswa.show', $calonMahasiswa->hashid) }}">{{ $calonMahasiswa->nama_lengkap }}</a></li>
                <li class="breadcrumb-item active">Verifikasi Dokumen</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <!-- Info Pendaftar -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($calonMahasiswa->foto)
                        <img src="{{ Storage::url($calonMahasiswa->foto) }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 2rem;">
                            {{ strtoupper(substr($calonMahasiswa->nama_lengkap, 0, 1)) }}
                        </div>
                    @endif
                    <h5 class="mb-1">{{ $calonMahasiswa->nama_lengkap }}</h5>
                    <p class="text-muted mb-2">{{ $calonMahasiswa->no_pendaftaran }}</p>
                    <span class="badge bg-{{ $calonMahasiswa->status_badge }}">{{ $calonMahasiswa->status_label }}</span>
                </div>
                <div class="card-footer bg-white">
                    @php
                        $dokumenWajibCount = $settingDokumen->where('is_wajib', true)->count();
                        $dokumenUploadedCount = $calonMahasiswa->dokumen->count();
                        $dokumenValidCount = $calonMahasiswa->dokumen->where('status_verifikasi', 'valid')->count();
                        $dokumenPendingCount = $calonMahasiswa->dokumen->where('status_verifikasi', 'pending')->count();
                        $dokumenTidakValidCount = $calonMahasiswa->dokumen->where('status_verifikasi', 'tidak_valid')->count();
                    @endphp
                    <div class="d-flex justify-content-between mb-1">
                        <span>Dokumen Wajib</span>
                        <strong>{{ $dokumenWajibCount }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Telah Diunggah</span>
                        <strong>{{ $dokumenUploadedCount }}</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="bi bi-check-circle text-success me-1"></i>Valid</span>
                        <strong class="text-success">{{ $dokumenValidCount }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="bi bi-clock text-warning me-1"></i>Pending</span>
                        <strong class="text-warning">{{ $dokumenPendingCount }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-x-circle text-danger me-1"></i>Tidak Valid</span>
                        <strong class="text-danger">{{ $dokumenTidakValidCount }}</strong>
                    </div>
                </div>
            </div>

            <!-- Upload Dokumen Baru -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-upload me-2"></i>Upload Dokumen
                </div>
                <div class="card-body">
                    <form action="{{ route('pmb.calon-mahasiswa.upload-dokumen', $calonMahasiswa->hashid) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
                            <select name="jenis_dokumen" id="jenis_dokumen" class="form-select form-select-sm" required>
                                <option value="">-- Pilih --</option>
                                @php
                                    $dokumenWajib = $settingDokumen->where('is_wajib', true);
                                    $dokumenOpsional = $settingDokumen->where('is_wajib', false);
                                @endphp
                                @if($dokumenWajib->count() > 0)
                                <optgroup label="📌 Wajib">
                                    @foreach($dokumenWajib as $setting)
                                        @php $sudahAda = $calonMahasiswa->dokumen->where('jenis_dokumen', $setting->kode)->first(); @endphp
                                        <option value="{{ $setting->kode }}" {{ $sudahAda ? 'disabled' : '' }}>
                                            {{ $setting->nama_dokumen }} @if($sudahAda) ✓ @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                                @endif
                                @if($dokumenOpsional->count() > 0)
                                <optgroup label="📎 Opsional">
                                    @foreach($dokumenOpsional as $setting)
                                        @php $sudahAda = $calonMahasiswa->dokumen->where('jenis_dokumen', $setting->kode)->first(); @endphp
                                        <option value="{{ $setting->kode }}" {{ $sudahAda ? 'disabled' : '' }}>
                                            {{ $setting->nama_dokumen }} @if($sudahAda) ✓ @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">File</label>
                            <input type="file" name="file" id="file" class="form-control form-control-sm" required>
                            <small class="text-muted">PDF, JPG, PNG. Maks 5MB</small>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-upload me-1"></i> Upload
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Daftar Dokumen Wajib -->
            @php $dokumenWajibSettings = $settingDokumen->where('is_wajib', true); @endphp
            @if($dokumenWajibSettings->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-asterisk me-2"></i>Dokumen Wajib
                </div>
                <div class="card-body">
                    @foreach($dokumenWajibSettings as $setting)
                        @php
                            $dokumen = $calonMahasiswa->dokumen->where('jenis_dokumen', $setting->kode)->first();
                        @endphp
                        <div class="card mb-3 {{ $dokumen ? ($dokumen->status_verifikasi == 'valid' ? 'border-success' : ($dokumen->status_verifikasi == 'tidak_valid' ? 'border-danger' : 'border-warning')) : 'border-secondary bg-light' }}">
                            <div class="card-header d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <strong>{{ $setting->nama_dokumen }}</strong>
                                </div>
                                @if($dokumen)
                                    <span class="badge bg-{{ $dokumen->status_badge }}">{{ $dokumen->status_label }}</span>
                                @else
                                    <span class="badge bg-secondary">Belum Upload</span>
                                @endif
                            </div>
                            @if($dokumen)
                            <div class="card-body py-2">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <a href="{{ Storage::url($dokumen->path_file) }}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                            @php $ext = strtolower(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION)); @endphp
                                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ Storage::url($dokumen->path_file) }}" class="rounded me-2" style="height: 50px; width: 50px; object-fit: cover;">
                                            @else
                                                <i class="bi bi-file-earmark-pdf fs-1 text-danger me-2"></i>
                                            @endif
                                            <small class="text-truncate" style="max-width: 100px;">{{ $dokumen->nama_file }}</small>
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('pmb.calon-mahasiswa.update-verifikasi-dokumen', $calonMahasiswa->hashid) }}" method="POST" class="d-flex gap-2 align-items-center">
                                            @csrf
                                            <input type="hidden" name="dokumen[0][id]" value="{{ $dokumen->id }}">
                                            <select name="dokumen[0][status]" class="form-select form-select-sm" style="width: 140px;">
                                                <option value="valid" {{ $dokumen->status_verifikasi == 'valid' ? 'selected' : '' }}>✓ Valid</option>
                                                <option value="tidak_valid" {{ $dokumen->status_verifikasi == 'tidak_valid' ? 'selected' : '' }}>✗ Tidak Valid</option>
                                                <option value="pending" {{ $dokumen->status_verifikasi == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                            </select>
                                            <input type="text" name="dokumen[0][catatan]" class="form-control form-control-sm" placeholder="Catatan" value="{{ $dokumen->catatan_verifikasi }}">
                                            <button type="submit" class="btn btn-sm btn-primary" title="Simpan">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <small class="text-muted d-block">{{ $dokumen->created_at->format('d/m/Y H:i') }}</small>
                                        <form action="{{ route('pmb.calon-mahasiswa.delete-dokumen', ['calon' => $calonMahasiswa->hashid, 'dokumen' => $dokumen->hashid]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if($dokumen->catatan_verifikasi)
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted"><i class="bi bi-chat-left-text me-1"></i>{{ $dokumen->catatan_verifikasi }}</small>
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="card-body py-3 text-center text-muted">
                                <i class="bi bi-cloud-upload fs-4 d-block mb-1"></i>
                                <small>Dokumen belum diunggah</small>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Daftar Dokumen Opsional -->
            @php $dokumenOpsionalSettings = $settingDokumen->where('is_wajib', false); @endphp
            @if($dokumenOpsionalSettings->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-paperclip me-2"></i>Dokumen Opsional
                </div>
                <div class="card-body">
                    @foreach($dokumenOpsionalSettings as $setting)
                        @php
                            $dokumen = $calonMahasiswa->dokumen->where('jenis_dokumen', $setting->kode)->first();
                        @endphp
                        @if($dokumen)
                        <div class="card mb-3 {{ $dokumen->status_verifikasi == 'valid' ? 'border-success' : ($dokumen->status_verifikasi == 'tidak_valid' ? 'border-danger' : 'border-warning') }}">
                            <div class="card-header d-flex justify-content-between align-items-center py-2">
                                <strong>{{ $setting->nama_dokumen }}</strong>
                                <span class="badge bg-{{ $dokumen->status_badge }}">{{ $dokumen->status_label }}</span>
                            </div>
                            <div class="card-body py-2">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <a href="{{ Storage::url($dokumen->path_file) }}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                            @php $ext = strtolower(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION)); @endphp
                                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ Storage::url($dokumen->path_file) }}" class="rounded me-2" style="height: 50px; width: 50px; object-fit: cover;">
                                            @else
                                                <i class="bi bi-file-earmark-pdf fs-1 text-danger me-2"></i>
                                            @endif
                                            <small class="text-truncate" style="max-width: 100px;">{{ $dokumen->nama_file }}</small>
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('pmb.calon-mahasiswa.update-verifikasi-dokumen', $calonMahasiswa->hashid) }}" method="POST" class="d-flex gap-2 align-items-center">
                                            @csrf
                                            <input type="hidden" name="dokumen[0][id]" value="{{ $dokumen->id }}">
                                            <select name="dokumen[0][status]" class="form-select form-select-sm" style="width: 140px;">
                                                <option value="valid" {{ $dokumen->status_verifikasi == 'valid' ? 'selected' : '' }}>✓ Valid</option>
                                                <option value="tidak_valid" {{ $dokumen->status_verifikasi == 'tidak_valid' ? 'selected' : '' }}>✗ Tidak Valid</option>
                                                <option value="pending" {{ $dokumen->status_verifikasi == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                            </select>
                                            <input type="text" name="dokumen[0][catatan]" class="form-control form-control-sm" placeholder="Catatan" value="{{ $dokumen->catatan_verifikasi }}">
                                            <button type="submit" class="btn btn-sm btn-primary" title="Simpan">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <small class="text-muted d-block">{{ $dokumen->created_at->format('d/m/Y H:i') }}</small>
                                        <form action="{{ route('pmb.calon-mahasiswa.delete-dokumen', ['calon' => $calonMahasiswa->hashid, 'dokumen' => $dokumen->hashid]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if($dokumen->catatan_verifikasi)
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted"><i class="bi bi-chat-left-text me-1"></i>{{ $dokumen->catatan_verifikasi }}</small>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach

                    @if($calonMahasiswa->dokumen->whereIn('jenis_dokumen', $dokumenOpsionalSettings->pluck('kode'))->count() == 0)
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <small>Belum ada dokumen opsional yang diunggah</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($settingDokumen->isEmpty())
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-exclamation-circle fs-1 mb-2 d-block"></i>
                        <p class="mb-0">Belum ada setting dokumen yang diperlukan.<br>Silakan tambahkan di menu Setting Dokumen PMB.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('pmb.calon-mahasiswa.show', $calonMahasiswa->hashid) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        @if($calonMahasiswa->is_dokumen_lengkap && in_array($calonMahasiswa->status_pendaftaran, ['terdaftar', 'verifikasi_dokumen']))
        <form action="{{ route('pmb.calon-mahasiswa.update-status', $calonMahasiswa->hashid) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="status" value="lulus_administrasi">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-patch-check me-1"></i> Set Lulus Administrasi
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
