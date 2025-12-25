@extends('layouts.app')

@section('title', 'Tambah Penerima Beasiswa')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Tambah Penerima Beasiswa</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('beasiswa.penerima.index') }}">Penerima Beasiswa</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Form Penerima Beasiswa</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('beasiswa.penerima.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Beasiswa <span class="text-danger">*</span></label>
                            <select name="beasiswa_id" class="form-select @error('beasiswa_id') is-invalid @enderror" required>
                                <option value="">Pilih Beasiswa</option>
                                @foreach($beasiswa as $b)
                                    <option value="{{ $b->id }}" {{ old('beasiswa_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama }} 
                                        ({{ $b->tipe_potongan === 'Persen' ? $b->nilai_potongan . '%' : 'Rp ' . number_format($b->nilai_potongan, 0, ',', '.') }})
                                        @if($b->kuota) - Sisa: {{ $b->sisa_kuota }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('beasiswa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                            <select name="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror" required>
                                <option value="">Pilih Mahasiswa</option>
                                @foreach($mahasiswa as $mhs)
                                    <option value="{{ $mhs->id }}" {{ old('mahasiswa_id') == $mhs->id ? 'selected' : '' }}>
                                        {{ $mhs->nim }} - {{ $mhs->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mahasiswa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
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

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', now()->format('Y-m-d')) }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}">
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan jika tidak ada batas waktu</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                            <a href="{{ route('beasiswa.penerima.index') }}" class="btn btn-secondary">
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
