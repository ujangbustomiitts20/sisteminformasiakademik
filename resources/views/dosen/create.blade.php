@extends('layouts.app')

@section('title', 'Tambah Dosen')

@section('content')
<div class="page-title">
    <h4>Tambah Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus me-2"></i>Form Tambah Dosen
            </div>
            <div class="card-body">
                <form action="{{ route('dosen.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nidn" class="form-label">NIDN <span class="text-danger">*</span></label>
                                <input type="text" name="nidn" id="nidn" class="form-control @error('nidn') is-invalid @enderror" value="{{ old('nidn') }}" required>
                                @error('nidn')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Password default = NIDN</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program_studi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                                <select name="program_studi_id" id="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    @foreach($programStudi as $ps)
                                    <option value="{{ $ps->id }}" {{ old('program_studi_id') == $ps->id ? 'selected' : '' }}>
                                        {{ $ps->nama }} ({{ $ps->fakultas->nama ?? '-' }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('program_studi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telepon" class="form-label">No. Telepon</label>
                                <input type="text" name="telepon" id="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon') }}">
                                @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}">
                                @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}">
                                @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
                                <select name="jabatan_fungsional" id="jabatan_fungsional" class="form-select @error('jabatan_fungsional') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="Tenaga Pengajar" {{ old('jabatan_fungsional') == 'Tenaga Pengajar' ? 'selected' : '' }}>Tenaga Pengajar</option>
                                    <option value="Asisten Ahli" {{ old('jabatan_fungsional') == 'Asisten Ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                                    <option value="Lektor" {{ old('jabatan_fungsional') == 'Lektor' ? 'selected' : '' }}>Lektor</option>
                                    <option value="Lektor Kepala" {{ old('jabatan_fungsional') == 'Lektor Kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                                    <option value="Guru Besar" {{ old('jabatan_fungsional') == 'Guru Besar' ? 'selected' : '' }}>Guru Besar</option>
                                </select>
                                @error('jabatan_fungsional')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="golongan" class="form-label">Golongan</label>
                                <input type="text" name="golongan" id="golongan" class="form-control @error('golongan') is-invalid @enderror" value="{{ old('golongan') }}" placeholder="III/a, IV/b, dll">
                                @error('golongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                        @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('dosen.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                <ul class="small text-muted mb-0">
                    <li>NIDN akan digunakan sebagai password default untuk login</li>
                    <li>Email harus unik dan akan digunakan untuk login</li>
                    <li>Field bertanda <span class="text-danger">*</span> wajib diisi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
