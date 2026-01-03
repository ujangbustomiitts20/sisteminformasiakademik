@extends('layouts.app')

@section('title', 'Edit Riwayat Pangkat')

@section('content')
<div class="page-title">
    <h4>Edit Riwayat Pangkat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai) }}">{{ $pegawai->nama }}</a></li>
            <li class="breadcrumb-item active">Edit Pangkat</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-award me-2"></i>Form Riwayat Pangkat
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.pegawai.riwayat.pangkat.update', [$pegawai, $pangkat]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="golongan" class="form-label">Golongan <span class="text-danger">*</span></label>
                                <select name="golongan" id="golongan" class="form-select @error('golongan') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach(['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'] as $g)
                                    <option value="{{ $g }}" {{ old('golongan', $pangkat->golongan) == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                                @error('golongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="pangkat" class="form-label">Pangkat <span class="text-danger">*</span></label>
                                <input type="text" name="pangkat" id="pangkat" class="form-control @error('pangkat') is-invalid @enderror" value="{{ old('pangkat', $pangkat->pangkat) }}" required>
                                @error('pangkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tmt_pangkat" class="form-label">TMT Pangkat <span class="text-danger">*</span></label>
                                <input type="date" name="tmt_pangkat" id="tmt_pangkat" class="form-control @error('tmt_pangkat') is-invalid @enderror" value="{{ old('tmt_pangkat', $pangkat->tmt_pangkat) }}" required>
                                @error('tmt_pangkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="masa_kerja_tahun" class="form-label">Masa Kerja (Tahun)</label>
                                <input type="number" name="masa_kerja_tahun" id="masa_kerja_tahun" class="form-control @error('masa_kerja_tahun') is-invalid @enderror" value="{{ old('masa_kerja_tahun', $pangkat->masa_kerja_tahun ?? 0) }}" min="0">
                                @error('masa_kerja_tahun')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="masa_kerja_bulan" class="form-label">Masa Kerja (Bulan)</label>
                                <input type="number" name="masa_kerja_bulan" id="masa_kerja_bulan" class="form-control @error('masa_kerja_bulan') is-invalid @enderror" value="{{ old('masa_kerja_bulan', $pangkat->masa_kerja_bulan ?? 0) }}" min="0" max="11">
                                @error('masa_kerja_bulan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="mb-3">Data SK</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_sk" class="form-label">No. SK</label>
                                <input type="text" name="no_sk" id="no_sk" class="form-control @error('no_sk') is-invalid @enderror" value="{{ old('no_sk', $pangkat->no_sk) }}">
                                @error('no_sk')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_sk" class="form-label">Tanggal SK</label>
                                <input type="date" name="tanggal_sk" id="tanggal_sk" class="form-control @error('tanggal_sk') is-invalid @enderror" value="{{ old('tanggal_sk', $pangkat->tanggal_sk) }}">
                                @error('tanggal_sk')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pejabat_sk" class="form-label">Pejabat yang Menandatangani SK</label>
                        <input type="text" name="pejabat_sk" id="pejabat_sk" class="form-control @error('pejabat_sk') is-invalid @enderror" value="{{ old('pejabat_sk', $pangkat->pejabat_sk) }}">
                        @error('pejabat_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_sk" class="form-label">File SK</label>
                        @if($pangkat->file_sk)
                        <div class="mb-2">
                            <a href="{{ asset('storage/'.$pangkat->file_sk) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Lihat File Saat Ini
                            </a>
                        </div>
                        @endif
                        <input type="file" name="file_sk" id="file_sk" class="form-control @error('file_sk') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maks: 2MB. Kosongkan jika tidak ingin mengubah file.</small>
                        @error('file_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
