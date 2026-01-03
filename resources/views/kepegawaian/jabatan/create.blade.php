@extends('layouts.app')

@section('title', 'Tambah Riwayat Jabatan - ' . $dosen->nama)

@section('content')
<div class="page-title">
    <h4>Tambah Riwayat Jabatan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.index', $dosen) }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Tambah Jabatan</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-briefcase me-2"></i>Form Riwayat Jabatan Fungsional - {{ $dosen->nama }}
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.jabatan.store', $dosen) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional <span class="text-danger">*</span></label>
                        <select name="jabatan_fungsional" id="jabatan_fungsional" class="form-select @error('jabatan_fungsional') is-invalid @enderror" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="Tenaga Pengajar" {{ old('jabatan_fungsional') == 'Tenaga Pengajar' ? 'selected' : '' }}>Tenaga Pengajar</option>
                            <option value="Asisten Ahli" {{ old('jabatan_fungsional') == 'Asisten Ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                            <option value="Lektor" {{ old('jabatan_fungsional') == 'Lektor' ? 'selected' : '' }}>Lektor</option>
                            <option value="Lektor Kepala" {{ old('jabatan_fungsional') == 'Lektor Kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                            <option value="Guru Besar" {{ old('jabatan_fungsional') == 'Guru Besar' ? 'selected' : '' }}>Guru Besar</option>
                        </select>
                        @error('jabatan_fungsional')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="angka_kredit" class="form-label">Angka Kredit</label>
                        <input type="number" step="0.01" name="angka_kredit" id="angka_kredit" class="form-control @error('angka_kredit') is-invalid @enderror" value="{{ old('angka_kredit') }}" min="0">
                        @error('angka_kredit')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="no_sk" class="form-label">No. SK <span class="text-danger">*</span></label>
                        <input type="text" name="no_sk" id="no_sk" class="form-control @error('no_sk') is-invalid @enderror" value="{{ old('no_sk') }}" required>
                        @error('no_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_sk" class="form-label">Tanggal SK <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_sk" id="tanggal_sk" class="form-control @error('tanggal_sk') is-invalid @enderror" value="{{ old('tanggal_sk') }}" required>
                        @error('tanggal_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tmt_jabatan" class="form-label">TMT Jabatan <span class="text-danger">*</span></label>
                        <input type="date" name="tmt_jabatan" id="tmt_jabatan" class="form-control @error('tmt_jabatan') is-invalid @enderror" value="{{ old('tmt_jabatan') }}" required>
                        @error('tmt_jabatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pejabat_penetap" class="form-label">Pejabat Penetap</label>
                        <input type="text" name="pejabat_penetap" id="pejabat_penetap" class="form-control @error('pejabat_penetap') is-invalid @enderror" value="{{ old('pejabat_penetap') }}">
                        @error('pejabat_penetap')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="file_sk" class="form-label">File SK</label>
                        <input type="file" name="file_sk" id="file_sk" class="form-control @error('file_sk') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <a href="{{ route('kepegawaian.index', $dosen) }}#jabatan" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
