@extends('layouts.app')

@section('title', 'Edit Dosen')

@section('content')
<div class="page-title">
    <h4>Edit Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil-square me-2"></i>Form Edit Dosen
    </div>
    <div class="card-body">
        <form action="{{ route('dosen.update', $dosen) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informasi Dasar</h6>
                    
                    <div class="mb-3">
                        <label for="nidn" class="form-label">NIDN <span class="text-danger">*</span></label>
                        <input type="text" name="nidn" id="nidn" class="form-control @error('nidn') is-invalid @enderror" 
                            value="{{ old('nidn', $dosen->nidn) }}" required>
                        @error('nidn')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" 
                            value="{{ old('nama', $dosen->nama) }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                            value="{{ old('email', $dosen->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="telepon" class="form-label">Telepon</label>
                        <input type="text" name="telepon" id="telepon" class="form-control @error('telepon') is-invalid @enderror" 
                            value="{{ old('telepon', $dosen->telepon) }}">
                        @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin', $dosen->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $dosen->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Aktif" {{ old('status', $dosen->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Cuti" {{ old('status', $dosen->status) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Nonaktif" {{ old('status', $dosen->status) == 'Nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informasi Akademik</h6>
                    
                    <div class="mb-3">
                        <label for="program_studi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" id="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($programStudi as $ps)
                            <option value="{{ $ps->id }}" {{ old('program_studi_id', $dosen->program_studi_id) == $ps->id ? 'selected' : '' }}>{{ $ps->nama }} ({{ $ps->fakultas->nama ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('program_studi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
                        <input type="text" name="jabatan_fungsional" id="jabatan_fungsional" class="form-control @error('jabatan_fungsional') is-invalid @enderror" 
                            value="{{ old('jabatan_fungsional', $dosen->jabatan_fungsional) }}">
                        @error('jabatan_fungsional')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="golongan" class="form-label">Golongan</label>
                        <input type="text" name="golongan" id="golongan" class="form-control @error('golongan') is-invalid @enderror" 
                            value="{{ old('golongan', $dosen->golongan) }}">
                        @error('golongan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $dosen->alamat) }}</textarea>
                        @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('dosen.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
