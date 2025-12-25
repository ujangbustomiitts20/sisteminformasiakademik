@extends('layouts.app')

@section('title', 'Tambah Pembayaran')

@section('content')
<div class="page-title">
    <h4>Tambah Pembayaran</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembayaran.index') }}">Pembayaran</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-plus-lg me-2"></i>Form Tambah Pembayaran
    </div>
    <div class="card-body">
        <form action="{{ route('pembayaran.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mahasiswa_id" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                        <select name="mahasiswa_id" id="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Mahasiswa --</option>
                            @foreach($mahasiswa as $m)
                            <option value="{{ $m->id }}" {{ old('mahasiswa_id') == $m->id ? 'selected' : '' }}>{{ $m->nim }} - {{ $m->nama }}</option>
                            @endforeach
                        </select>
                        @error('mahasiswa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tahun_akademik_id" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                        <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Akademik --</option>
                            @foreach($tahunAkademik as $ta)
                            <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        @error('tahun_akademik_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                        <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="spp" {{ old('jenis') == 'spp' ? 'selected' : '' }}>SPP</option>
                            <option value="her" {{ old('jenis') == 'her' ? 'selected' : '' }}>Her-Registrasi</option>
                            <option value="wisuda" {{ old('jenis') == 'wisuda' ? 'selected' : '' }}>Wisuda</option>
                            <option value="lainnya" {{ old('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah Pembayaran <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="jumlah" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                                value="{{ old('jumlah') }}" required>
                            @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="belum_bayar" {{ old('status', 'belum_bayar') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal_bayar" class="form-label">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                            value="{{ old('tanggal_bayar') }}">
                        @error('tanggal_bayar')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
