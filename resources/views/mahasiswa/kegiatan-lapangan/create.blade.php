@extends('layouts.app')

@section('title', 'Daftar Kegiatan Lapangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pendaftaran PKL/Magang/KKN</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mahasiswa.kegiatan-lapangan.index') }}">Kegiatan Lapangan</a></li>
                    <li class="breadcrumb-item active">Daftar</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('mahasiswa.kegiatan-lapangan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
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

    <form action="{{ route('mahasiswa.kegiatan-lapangan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Pilih Periode & Jenis Kegiatan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Periode Kegiatan <span class="text-danger">*</span></label>
                            <select name="periode_id" class="form-select" required>
                                <option value="">-- Pilih Periode --</option>
                                @foreach($periodes as $periode)
                                <option value="{{ $periode->id }}" {{ old('periode_id') == $periode->id ? 'selected' : '' }}>
                                    {{ $periode->nama }} ({{ $periode->jenisKegiatan->nama ?? '-' }})
                                    - Daftar s/d {{ $periode->tanggal_selesai_daftar->format('d M Y') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Pilihan Mitra/Lokasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Pilihan 1 (Prioritas) <span class="text-danger">*</span></label>
                                <select name="mitra_pilihan_1" class="form-select" required>
                                    <option value="">-- Pilih Mitra --</option>
                                    @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}" {{ old('mitra_pilihan_1') == $mitra->id ? 'selected' : '' }}>
                                        {{ $mitra->nama }} - {{ $mitra->kota }}
                                        @if($mitra->kuota_mahasiswa)
                                        (Kuota: {{ $mitra->kuota_mahasiswa }})
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Pilihan 2</label>
                                <select name="mitra_pilihan_2" class="form-select">
                                    <option value="">-- Pilih Mitra --</option>
                                    @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}" {{ old('mitra_pilihan_2') == $mitra->id ? 'selected' : '' }}>
                                        {{ $mitra->nama }} - {{ $mitra->kota }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Pilihan 3</label>
                                <select name="mitra_pilihan_3" class="form-select">
                                    <option value="">-- Pilih Mitra --</option>
                                    @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}" {{ old('mitra_pilihan_3') == $mitra->id ? 'selected' : '' }}>
                                        {{ $mitra->nama }} - {{ $mitra->kota }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Rencana Kegiatan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Rencana Kegiatan / Motivasi <span class="text-danger">*</span></label>
                            <textarea name="rencana_kegiatan" class="form-control" rows="5" 
                                placeholder="Jelaskan rencana kegiatan Anda selama pelaksanaan PKL/Magang/KKN dan alasan memilih mitra tersebut..." required>{{ old('rencana_kegiatan') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Dokumen Pendukung</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Surat Pengantar</label>
                                <input type="file" name="surat_pengantar" class="form-control" accept=".pdf">
                                <small class="text-muted">Format: PDF. Maks 2MB</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dokumen Pendukung Lain</label>
                                <input type="file" name="dokumen_pendukung" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">CV, Sertifikat, dll. Maks 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Persyaratan</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <ul class="small mb-0">
                                <li>Minimal sudah menempuh semester 6</li>
                                <li>IPK minimal 2.75</li>
                                <li>Tidak memiliki nilai E</li>
                                <li>Tidak sedang cuti akademik</li>
                                <li>Telah menyelesaikan minimal 100 SKS</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Data Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">NIM</td>
                                <td>{{ auth()->user()->mahasiswa->nim ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama</td>
                                <td>{{ auth()->user()->mahasiswa->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Prodi</td>
                                <td>{{ auth()->user()->mahasiswa->programStudi->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Semester</td>
                                <td>{{ auth()->user()->mahasiswa->semester ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-1"></i>Ajukan Pendaftaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
