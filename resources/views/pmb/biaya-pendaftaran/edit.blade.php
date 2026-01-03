@extends('layouts.app')

@section('title', 'Edit Biaya Pendaftaran')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Biaya Pendaftaran</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pmb.biaya-pendaftaran.index') }}">Biaya Pendaftaran</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pmb.biaya-pendaftaran.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil-square me-2"></i>Form Edit Biaya Pendaftaran
                </div>
                <div class="card-body">
                    <form action="{{ route('pmb.biaya-pendaftaran.update', $biaya->hashid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Gelombang PMB <span class="text-danger">*</span></label>
                                    <select name="gelombang_pmb_id" class="form-select @error('gelombang_pmb_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Gelombang --</option>
                                        @foreach($gelombangs as $gel)
                                            <option value="{{ $gel->id }}" {{ old('gelombang_pmb_id', $biaya->gelombang_pmb_id) == $gel->id ? 'selected' : '' }}>
                                                {{ $gel->periodePmb->nama ?? '' }} - {{ $gel->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('gelombang_pmb_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jalur Seleksi <span class="text-danger">*</span></label>
                                    <select name="jalur_seleksi_id" class="form-select @error('jalur_seleksi_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jalur --</option>
                                        @foreach($jalurs as $jalur)
                                            <option value="{{ $jalur->id }}" {{ old('jalur_seleksi_id', $biaya->jalur_seleksi_id) == $jalur->id ? 'selected' : '' }}>
                                                {{ $jalur->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jalur_seleksi_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Program Studi</label>
                            <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror">
                                <option value="">-- Semua Prodi (Umum) --</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ old('program_studi_id', $biaya->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Kosongkan untuk biaya umum semua prodi</small>
                            @error('program_studi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Biaya Formulir <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="biaya_formulir" class="form-control @error('biaya_formulir') is-invalid @enderror" required min="0" value="{{ old('biaya_formulir', $biaya->biaya_formulir) }}">
                                    </div>
                                    @error('biaya_formulir')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Biaya Ujian <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="biaya_ujian" class="form-control @error('biaya_ujian') is-invalid @enderror" required min="0" value="{{ old('biaya_ujian', $biaya->biaya_ujian) }}">
                                    </div>
                                    @error('biaya_ujian')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-secondary">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Total Biaya Saat Ini:</strong>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="fs-5 fw-bold text-primary">{{ format_rupiah($biaya->total_biaya) }}</span>
                                </div>
                            </div>
                            <small class="text-muted">Total akan dihitung otomatis saat disimpan</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('pmb.biaya-pendaftaran.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
