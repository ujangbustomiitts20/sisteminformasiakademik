@extends('layouts.app')

@section('title', 'Edit Tenaga Kependidikan')

@section('content')
<div class="page-title">
    <h4>Edit Tenaga Kependidikan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pegawai.index') }}">Tenaga Kependidikan</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<form action="{{ route('kepegawaian.pegawai.update', $pegawai) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-8">
            <!-- Data Pribadi -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>Data Pribadi
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP</label>
                                <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $pegawai->nip) }}">
                                @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" name="nik" id="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $pegawai->nik) }}" maxlength="16">
                                @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $pegawai->nama) }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}">
                                @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir?->format('Y-m-d')) }}">
                                @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="agama" class="form-label">Agama</label>
                                <select name="agama" id="agama" class="form-select @error('agama') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama', $pegawai->agama) == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                                @error('agama')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status_pernikahan" class="form-label">Status Pernikahan</label>
                                <select name="status_pernikahan" id="status_pernikahan" class="form-select @error('status_pernikahan') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati'] as $status)
                                    <option value="{{ $status }}" {{ old('status_pernikahan', $pegawai->status_pernikahan) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                @error('status_pernikahan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Kontak -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-telephone me-2"></i>Informasi Kontak
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $pegawai->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No. HP</label>
                                <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $pegawai->no_hp) }}">
                                @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" name="telepon" id="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon', $pegawai->telepon) }}">
                                @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2">{{ old('alamat', $pegawai->alamat) }}</textarea>
                        @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Data Kepegawaian -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-briefcase me-2"></i>Data Kepegawaian
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="unit_kerja_id" class="form-label">Unit Kerja</label>
                        <select name="unit_kerja_id" id="unit_kerja_id" class="form-select @error('unit_kerja_id') is-invalid @enderror">
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach($unitKerja as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_kerja_id', $pegawai->unit_kerja_id) == $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
                            @endforeach
                        </select>
                        @error('unit_kerja_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama_jabatan_id" class="form-label">Jabatan</label>
                        <select name="nama_jabatan_id" id="nama_jabatan_id" class="form-select @error('nama_jabatan_id') is-invalid @enderror">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($namaJabatans as $jab)
                            <option value="{{ $jab->id }}" {{ old('nama_jabatan_id', $pegawai->nama_jabatan_id) == $jab->id ? 'selected' : '' }}>{{ $jab->nama }}</option>
                            @endforeach
                        </select>
                        @error('nama_jabatan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="jenis_pegawai" class="form-label">Jenis Pegawai <span class="text-danger">*</span></label>
                        <select name="jenis_pegawai" id="jenis_pegawai" class="form-select @error('jenis_pegawai') is-invalid @enderror" required>
                            @foreach(['Honorer', 'Kontrak', 'PNS', 'PPPK', 'Tetap Yayasan'] as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                            @endforeach
                        </select>
                        @error('jenis_pegawai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tmt_pegawai" class="form-label">TMT Pegawai</label>
                        <input type="date" name="tmt_pegawai" id="tmt_pegawai" class="form-control @error('tmt_pegawai') is-invalid @enderror" value="{{ old('tmt_pegawai', $pegawai->tmt_pegawai?->format('Y-m-d')) }}">
                        @error('tmt_pegawai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Tanggal Mulai Tugas</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach(['Aktif', 'Cuti', 'Non-Aktif', 'Pensiun'] as $status)
                            <option value="{{ $status }}" {{ old('status', $pegawai->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Foto -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-camera me-2"></i>Foto
                </div>
                <div class="card-body text-center">
                    @if($pegawai->foto)
                    <img src="{{ Storage::url($pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="img-thumbnail mb-3" style="max-width: 150px;">
                    @else
                    <div class="bg-secondary text-white rounded d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px; font-size: 3rem;">
                        {{ strtoupper(substr($pegawai->nama, 0, 1)) }}
                    </div>
                    @endif
                    <div class="mb-3">
                        <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                        @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('kepegawaian.pegawai.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
