@extends('layouts.app')

@section('title', 'Edit Mahasiswa')

@section('content')
<div class="page-title">
    <h4>Edit Mahasiswa</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mahasiswa.index') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="bi bi-person me-2"></i>Data Pribadi</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">NIM <span class="text-danger">*</span></label>
                        <input type="text" name="nim" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim', $mahasiswa->nim) }}" required>
                        @error('nim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $mahasiswa->nama) }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $mahasiswa->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="L" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $mahasiswa->tanggal_lahir?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $mahasiswa->telepon) }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $mahasiswa->alamat) }}</textarea>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="bi bi-mortarboard me-2"></i>Data Akademik</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                            @foreach($programStudi as $ps)
                            <option value="{{ $ps->id }}" {{ old('program_studi_id', $mahasiswa->program_studi_id) == $ps->id ? 'selected' : '' }}>
                                {{ $ps->nama }} ({{ $ps->fakultas->nama ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        @error('program_studi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Angkatan <span class="text-danger">*</span></label>
                            <input type="number" name="angkatan" class="form-control @error('angkatan') is-invalid @enderror" value="{{ old('angkatan', $mahasiswa->angkatan) }}" required>
                            @error('angkatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Semester Aktif <span class="text-danger">*</span></label>
                            <input type="number" name="semester_aktif" class="form-control" value="{{ old('semester_aktif', $mahasiswa->semester_aktif) }}" min="1" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Aktif" {{ old('status', $mahasiswa->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Cuti" {{ old('status', $mahasiswa->status) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Lulus" {{ old('status', $mahasiswa->status) == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="DO" {{ old('status', $mahasiswa->status) == 'DO' ? 'selected' : '' }}>DO</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dosen Wali</label>
                        <select name="dosen_wali_id" class="form-select">
                            <option value="">-- Pilih Dosen Wali --</option>
                            @foreach($dosen as $d)
                            <option value="{{ $d->id }}" {{ old('dosen_wali_id', $mahasiswa->dosen_wali_id) == $d->id ? 'selected' : '' }}>
                                {{ $d->nama }} ({{ $d->nidn }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x me-1"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
