@extends('layouts.app')

@section('title', 'Generate Tagihan Massal')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Generate Tagihan Massal</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan</a></li>
                <li class="breadcrumb-item active">Generate Massal</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-magic me-2"></i>Generate Tagihan Otomatis</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Informasi:</strong> Fitur ini akan membuat tagihan otomatis untuk semua mahasiswa aktif berdasarkan tarif yang dipilih. Mahasiswa yang sudah memiliki tagihan dengan tarif dan tahun akademik yang sama akan dilewati.
                    </div>

                    <form action="{{ route('tagihan.generate-massal') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                                <select name="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                                    <option value="">Pilih Tahun Akademik</option>
                                    @foreach($tahunAkademik as $ta)
                                        <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
                                    @endforeach
                                </select>
                                @error('tahun_akademik_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tarif <span class="text-danger">*</span></label>
                                <select name="tarif_id" class="form-select @error('tarif_id') is-invalid @enderror" required>
                                    <option value="">Pilih Tarif</option>
                                    @foreach($tarif as $t)
                                        <option value="{{ $t->id }}" {{ old('tarif_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->nama_tarif }} ({{ $t->jenis }}) - Rp {{ number_format($t->nominal, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tarif_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Program Studi (Opsional)</label>
                                <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror">
                                    <option value="">Semua Program Studi</option>
                                    @foreach($programStudi as $prodi)
                                        <option value="{{ $prodi->id }}" {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                                    @endforeach
                                </select>
                                @error('program_studi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Angkatan (Opsional)</label>
                                <input type="text" name="angkatan" class="form-control @error('angkatan') is-invalid @enderror" value="{{ old('angkatan') }}" placeholder="Contoh: 2024">
                                @error('angkatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_jatuh_tempo" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" value="{{ old('tanggal_jatuh_tempo') }}" required>
                            @error('tanggal_jatuh_tempo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Yakin generate tagihan massal?')">
                                <i class="bi bi-magic me-1"></i>Generate Tagihan
                            </button>
                            <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
