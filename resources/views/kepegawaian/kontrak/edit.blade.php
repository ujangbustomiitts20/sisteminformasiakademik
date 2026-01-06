@extends('layouts.app')

@section('title', 'Edit Kontrak Kerja')

@section('content')
<div class="page-title">
    <h4>Edit Kontrak Kerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.kontrak.index') }}">Kontrak Kerja</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('kepegawaian.kontrak.update', $kontrak) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pegawai</label>
                    <input type="text" class="form-control" value="{{ $kontrak->nama_pegawai }}" readonly>
                    <small class="text-muted">{{ $kontrak->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Kontrak <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_kontrak" class="form-control @error('nomor_kontrak') is-invalid @enderror" value="{{ old('nomor_kontrak', $kontrak->nomor_kontrak) }}" required>
                    @error('nomor_kontrak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Kontrak <span class="text-danger">*</span></label>
                    <select name="jenis_kontrak" class="form-select @error('jenis_kontrak') is-invalid @enderror" required>
                        <option value="pkwt" {{ old('jenis_kontrak', $kontrak->jenis_kontrak) == 'pkwt' ? 'selected' : '' }}>PKWT (Waktu Tertentu)</option>
                        <option value="pkwtt" {{ old('jenis_kontrak', $kontrak->jenis_kontrak) == 'pkwtt' ? 'selected' : '' }}>PKWTT (Waktu Tidak Tertentu)</option>
                        <option value="honorer" {{ old('jenis_kontrak', $kontrak->jenis_kontrak) == 'honorer' ? 'selected' : '' }}>Honorer</option>
                        <option value="magang" {{ old('jenis_kontrak', $kontrak->jenis_kontrak) == 'magang' ? 'selected' : '' }}>Magang</option>
                    </select>
                    @error('jenis_kontrak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', $kontrak->tanggal_mulai->format('Y-m-d')) }}" required>
                    @error('tanggal_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Berakhir</label>
                    <input type="date" name="tanggal_berakhir" class="form-control @error('tanggal_berakhir') is-invalid @enderror" value="{{ old('tanggal_berakhir', $kontrak->tanggal_berakhir ? $kontrak->tanggal_berakhir->format('Y-m-d') : '') }}">
                    <small class="text-muted">Kosongkan jika tidak terbatas (PKWTT)</small>
                    @error('tanggal_berakhir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="gaji_pokok" class="form-control @error('gaji_pokok') is-invalid @enderror" value="{{ old('gaji_pokok', $kontrak->gaji_pokok) }}" required>
                        @error('gaji_pokok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="draft" {{ old('status', $kontrak->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="aktif" {{ old('status', $kontrak->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ old('status', $kontrak->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ old('status', $kontrak->status) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">File Kontrak (PDF)</label>
                    <input type="file" name="file_kontrak" class="form-control @error('file_kontrak') is-invalid @enderror" accept=".pdf">
                    @if($kontrak->file_kontrak)
                        <small class="text-muted">File saat ini: <a href="{{ Storage::url($kontrak->file_kontrak) }}" target="_blank">Lihat</a></small>
                    @endif
                    @error('file_kontrak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $kontrak->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('kepegawaian.kontrak.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Update Kontrak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
