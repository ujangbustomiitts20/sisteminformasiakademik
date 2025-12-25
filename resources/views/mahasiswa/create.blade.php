@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')

@section('content')
<div class="page-title">
    <h4>Tambah Mahasiswa</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mahasiswa.index') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('mahasiswa.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="bi bi-person me-2"></i>Data Pribadi</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">NIM <span class="text-danger">*</span></label>
                        <input type="text" name="nim" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim') }}" required>
                        @error('nim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Email ini akan digunakan untuk login</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="bi bi-mortarboard me-2"></i>Data Akademik</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($programStudi as $ps)
                            <option value="{{ $ps->id }}" {{ old('program_studi_id') == $ps->id ? 'selected' : '' }}>
                                {{ $ps->nama }} ({{ $ps->fakultas->nama ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        @error('program_studi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Angkatan <span class="text-danger">*</span></label>
                        <input type="number" name="angkatan" class="form-control @error('angkatan') is-invalid @enderror" value="{{ old('angkatan', date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}" required>
                        @error('angkatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dosen Wali</label>
                        <select name="dosen_wali_id" class="form-select">
                            <option value="">-- Pilih Dosen Wali --</option>
                            @foreach($dosen as $d)
                            <option value="{{ $d->id }}" {{ old('dosen_wali_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->nama }} ({{ $d->nidn }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Info:</strong> Password default adalah NIM mahasiswa. Mahasiswa dapat mengubah password setelah login.
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x me-1"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
