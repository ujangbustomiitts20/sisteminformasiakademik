@extends('layouts.app')

@section('title', 'Tambah Pendaftar Wisuda')

@section('content')
<div class="page-title">
    <h4>Tambah Pendaftar Wisuda</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('wisuda.index') }}">Wisuda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('wisuda.show', $wisuda) }}">{{ $wisuda->nama }}</a></li>
            <li class="breadcrumb-item active">Tambah Pendaftar</li>
        </ol>
    </nav>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-4">
        <!-- Info Periode -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Info Periode Wisuda
            </div>
            <div class="card-body">
                <h5>{{ $wisuda->nama }}</h5>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Tanggal Wisuda</td>
                        <td><strong>{{ $wisuda->tanggal_wisuda->format('d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kuota</td>
                        <td>
                            @if($wisuda->kuota)
                            {{ $wisuda->jumlah_pendaftar }} / {{ $wisuda->kuota }}
                            @else
                            Tidak terbatas
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Biaya</td>
                        <td>Rp {{ number_format($wisuda->biaya_wisuda, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus me-2"></i>Form Pendaftaran
            </div>
            <div class="card-body">
                <form action="{{ route('wisuda.pendaftaran.store', $wisuda) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                        <select name="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Mahasiswa --</option>
                            @foreach($mahasiswa as $mhs)
                            <option value="{{ $mhs->id }}" {{ old('mahasiswa_id') == $mhs->id ? 'selected' : '' }}>
                                {{ $mhs->nim }} - {{ $mhs->nama }} ({{ $mhs->programStudi->nama ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        @error('mahasiswa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Hanya menampilkan mahasiswa aktif yang belum terdaftar di periode ini</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">IPK <span class="text-danger">*</span></label>
                                <input type="number" name="ipk" step="0.01" min="0" max="4" 
                                       class="form-control @error('ipk') is-invalid @enderror" 
                                       value="{{ old('ipk') }}" required>
                                @error('ipk')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total SKS <span class="text-danger">*</span></label>
                                <input type="number" name="total_sks" min="1" 
                                       class="form-control @error('total_sks') is-invalid @enderror" 
                                       value="{{ old('total_sks') }}" required>
                                @error('total_sks')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Judul Skripsi/Tugas Akhir</label>
                        <textarea name="judul_skripsi" rows="2" 
                                  class="form-control @error('judul_skripsi') is-invalid @enderror">{{ old('judul_skripsi') }}</textarea>
                        @error('judul_skripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Lulus Sidang</label>
                        <input type="date" name="tanggal_lulus_sidang" 
                               class="form-control @error('tanggal_lulus_sidang') is-invalid @enderror" 
                               value="{{ old('tanggal_lulus_sidang') }}">
                        @error('tanggal_lulus_sidang')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('wisuda.show', $wisuda) }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
