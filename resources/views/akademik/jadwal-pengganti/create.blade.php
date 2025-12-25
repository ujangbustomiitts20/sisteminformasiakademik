@extends('layouts.app')

@section('title', 'Tambah Jadwal Pengganti')

@section('content')
<div class="page-title">
    <h4>Tambah Jadwal Pengganti</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jadwal-pengganti.index') }}">Jadwal Pengganti</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Form Jadwal Pengganti</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form action="{{ route('jadwal-pengganti.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Jadwal Kuliah <span class="text-danger">*</span></label>
                        <select name="jadwal_kuliah_id" class="form-select @error('jadwal_kuliah_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jadwal Kuliah --</option>
                            @foreach($jadwalKuliah as $jk)
                            <option value="{{ $jk->id }}" {{ old('jadwal_kuliah_id') == $jk->id ? 'selected' : '' }}
                                    data-hari="{{ $jk->hari }}" data-jam="{{ $jk->jam_mulai }} - {{ $jk->jam_selesai }}">
                                {{ $jk->mataKuliah->kode }} - {{ $jk->mataKuliah->nama }} 
                                ({{ $jk->hari }}, {{ substr($jk->jam_mulai, 0, 5) }}-{{ substr($jk->jam_selesai, 0, 5) }}) 
                                - {{ $jk->dosen->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('jadwal_kuliah_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Asli (yang diganti) <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_asli" class="form-control @error('tanggal_asli') is-invalid @enderror" 
                                       value="{{ old('tanggal_asli') }}" required>
                                @error('tanggal_asli')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pengganti <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pengganti" class="form-control @error('tanggal_pengganti') is-invalid @enderror" 
                                       value="{{ old('tanggal_pengganti') }}" min="{{ date('Y-m-d') }}" required>
                                @error('tanggal_pengganti')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" 
                                       value="{{ old('jam_mulai') }}" required>
                                @error('jam_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" 
                                       value="{{ old('jam_selesai') }}" required>
                                @error('jam_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ruangan <span class="text-danger">*</span></label>
                        <select name="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($ruangan as $r)
                            <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nama }} ({{ $r->gedung }}) - Kapasitas: {{ $r->kapasitas }}
                            </option>
                            @endforeach
                        </select>
                        @error('ruangan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan Penggantian <span class="text-danger">*</span></label>
                        <select name="alasan" class="form-select @error('alasan') is-invalid @enderror" required>
                            <option value="">-- Pilih Alasan --</option>
                            @foreach($alasanList as $key => $value)
                            <option value="{{ $key }}" {{ old('alasan') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('alasan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                  rows="3" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('jadwal-pengganti.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <p class="text-muted mb-2">Gunakan fitur ini untuk:</p>
                <ul class="text-muted small">
                    <li>Mengganti jadwal perkuliahan yang tertunda</li>
                    <li>Reschedule kelas pengganti hari libur</li>
                    <li>Mengatur ulang jadwal dosen berhalangan</li>
                </ul>
                <hr>
                <p class="text-muted small mb-0">
                    <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                    Pastikan tidak ada konflik ruangan pada waktu yang dipilih.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
