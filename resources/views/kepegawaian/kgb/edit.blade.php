@extends('layouts.app')

@section('title', 'Edit Kenaikan Gaji Berkala')

@section('content')
<div class="page-title">
    <h4>Edit Kenaikan Gaji Berkala (KGB)</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.kgb.index') }}">KGB</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil me-2"></i>Form Edit KGB
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.kgb.update', $kgb) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Info Pegawai (readonly) -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Pegawai</label>
                    <div class="form-control bg-light">
                        @if($kgb->dosen)
                        <i class="bi bi-person me-1"></i>{{ $kgb->dosen->nama_lengkap }} 
                        <span class="badge bg-info">Dosen</span>
                        @elseif($kgb->pegawai)
                        <i class="bi bi-person me-1"></i>{{ $kgb->pegawai->nama }} 
                        <span class="badge bg-secondary">Tendik</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr>
            <h5 class="mb-3"><i class="bi bi-calendar-date me-2"></i>Informasi TMT</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">TMT KGB <span class="text-danger">*</span></label>
                    <input type="date" name="tmt_kgb" class="form-control @error('tmt_kgb') is-invalid @enderror" value="{{ old('tmt_kgb', $kgb->tmt_kgb?->format('Y-m-d')) }}" required>
                    <div class="form-text">TMT KGB Berikutnya akan otomatis dihitung 2 tahun dari TMT KGB</div>
                    @error('tmt_kgb')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr>
            <h5 class="mb-3"><i class="bi bi-star me-2"></i>Golongan & Masa Kerja</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Golongan/Ruang <span class="text-danger">*</span></label>
                    <input type="text" name="golongan_ruang" class="form-control @error('golongan_ruang') is-invalid @enderror" value="{{ old('golongan_ruang', $kgb->golongan_ruang) }}" placeholder="Contoh: III/a" required>
                    @error('golongan_ruang')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Masa Kerja Golongan</label>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" name="masa_kerja_golongan_tahun" class="form-control" value="{{ old('masa_kerja_golongan_tahun', $kgb->masa_kerja_golongan_tahun) }}" min="0" required>
                                <span class="input-group-text">Thn</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" name="masa_kerja_golongan_bulan" class="form-control" value="{{ old('masa_kerja_golongan_bulan', $kgb->masa_kerja_golongan_bulan) }}" min="0" max="11" required>
                                <span class="input-group-text">Bln</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Masa Kerja Total</label>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" name="masa_kerja_total_tahun" class="form-control" value="{{ old('masa_kerja_total_tahun', $kgb->masa_kerja_total_tahun) }}" min="0" required>
                                <span class="input-group-text">Thn</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" name="masa_kerja_total_bulan" class="form-control" value="{{ old('masa_kerja_total_bulan', $kgb->masa_kerja_total_bulan) }}" min="0" max="11" required>
                                <span class="input-group-text">Bln</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <h5 class="mb-3"><i class="bi bi-cash-stack me-2"></i>Informasi Gaji</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Gaji Pokok Lama</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="gaji_pokok_lama" class="form-control @error('gaji_pokok_lama') is-invalid @enderror" value="{{ old('gaji_pokok_lama', $kgb->gaji_pokok_lama) }}" min="0">
                    </div>
                    @error('gaji_pokok_lama')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gaji Pokok Baru <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="gaji_pokok_baru" class="form-control @error('gaji_pokok_baru') is-invalid @enderror" value="{{ old('gaji_pokok_baru', $kgb->gaji_pokok_baru) }}" min="0" required>
                    </div>
                    @error('gaji_pokok_baru')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr>
            <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Surat Keputusan</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">No. SK</label>
                    <input type="text" name="no_sk" class="form-control @error('no_sk') is-invalid @enderror" value="{{ old('no_sk', $kgb->no_sk) }}">
                    @error('no_sk')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal SK</label>
                    <input type="date" name="tanggal_sk" class="form-control @error('tanggal_sk') is-invalid @enderror" value="{{ old('tanggal_sk', $kgb->tanggal_sk?->format('Y-m-d')) }}">
                    @error('tanggal_sk')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Dokumen SK (PDF)</label>
                @if($kgb->dokumen_sk)
                <div class="mb-2">
                    <a href="{{ Storage::url($kgb->dokumen_sk) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Lihat Dokumen Saat Ini
                    </a>
                </div>
                @endif
                <input type="file" name="dokumen_sk" class="form-control @error('dokumen_sk') is-invalid @enderror" accept=".pdf">
                <div class="form-text">Format: PDF, Maks: 5MB. Kosongkan jika tidak ingin mengubah.</div>
                @error('dokumen_sk')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="2">{{ old('catatan', $kgb->catatan) }}</textarea>
                @error('catatan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('kepegawaian.kgb.show', $kgb) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
