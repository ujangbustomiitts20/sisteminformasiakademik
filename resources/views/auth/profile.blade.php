@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="page-title">
    <h4>Profil Saya</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Profil</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    @php
                        $foto = null;
                        if (Auth::user()->isMahasiswa() && Auth::user()->mahasiswa?->foto) {
                            $foto = Auth::user()->mahasiswa->foto;
                        } elseif (Auth::user()->isDosen() && Auth::user()->dosen?->foto) {
                            $foto = Auth::user()->dosen->foto;
                        }
                    @endphp
                    @if($foto)
                    <img src="{{ asset('storage/' . $foto) }}" alt="Foto Profil" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    @endif
                </div>
                <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                <p class="text-muted mb-2">{{ Auth::user()->email }}</p>
                <span class="badge bg-{{ Auth::user()->role == 'admin' ? 'danger' : (Auth::user()->role == 'dosen' ? 'primary' : 'success') }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>
        </div>
        
        @if(Auth::user()->isMahasiswa() && Auth::user()->mahasiswa)
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Data Mahasiswa
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">NIM</td>
                        <td>: <code>{{ Auth::user()->mahasiswa->nim }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>: {{ Auth::user()->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>: {{ Auth::user()->mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>: <span class="badge bg-{{ Auth::user()->mahasiswa->status == 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst(Auth::user()->mahasiswa->status) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
        
        @if(Auth::user()->isDosen() && Auth::user()->dosen)
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Data Dosen
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">NIDN</td>
                        <td>: <code>{{ Auth::user()->dosen->nidn }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fakultas</td>
                        <td>: {{ Auth::user()->dosen->fakultas->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>: <span class="badge bg-{{ Auth::user()->dosen->status == 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst(Auth::user()->dosen->status) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-8">
        <!-- Update Profil -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-person-fill-gear me-2"></i>Update Profil
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @if(Auth::user()->isMahasiswa())
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', Auth::user()->mahasiswa->no_hp ?? '') }}" placeholder="08xxxxxxxxxx">
                            @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">Pilih...</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', Auth::user()->mahasiswa->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', Auth::user()->mahasiswa->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2">{{ old('alamat', Auth::user()->mahasiswa->alamat ?? '') }}</textarea>
                            @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Data Orang Tua -->
                        <div class="col-md-12 mt-3 mb-2">
                            <h6 class="text-primary"><i class="bi bi-people me-2"></i>Data Orang Tua</h6>
                            <hr>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="form-control @error('nama_ayah') is-invalid @enderror" value="{{ old('nama_ayah', Auth::user()->mahasiswa->nama_ayah ?? '') }}" placeholder="Nama lengkap ayah">
                            @error('nama_ayah')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="form-control @error('nama_ibu') is-invalid @enderror" value="{{ old('nama_ibu', Auth::user()->mahasiswa->nama_ibu ?? '') }}" placeholder="Nama lengkap ibu">
                            @error('nama_ibu')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" class="form-control @error('pekerjaan_ayah') is-invalid @enderror" value="{{ old('pekerjaan_ayah', Auth::user()->mahasiswa->pekerjaan_ayah ?? '') }}" placeholder="Pekerjaan ayah">
                            @error('pekerjaan_ayah')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" class="form-control @error('pekerjaan_ibu') is-invalid @enderror" value="{{ old('pekerjaan_ibu', Auth::user()->mahasiswa->pekerjaan_ibu ?? '') }}" placeholder="Pekerjaan ibu">
                            @error('pekerjaan_ibu')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP Orang Tua</label>
                            <input type="text" name="no_hp_ortu" class="form-control @error('no_hp_ortu') is-invalid @enderror" value="{{ old('no_hp_ortu', Auth::user()->mahasiswa->no_hp_ortu ?? '') }}" placeholder="08xxxxxxxxxx">
                            @error('no_hp_ortu')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penghasilan Orang Tua</label>
                            <select name="penghasilan_ortu" class="form-select @error('penghasilan_ortu') is-invalid @enderror">
                                <option value="">Pilih...</option>
                                <option value="< Rp 1.000.000" {{ old('penghasilan_ortu', Auth::user()->mahasiswa->penghasilan_ortu ?? '') == '< Rp 1.000.000' ? 'selected' : '' }}>< Rp 1.000.000</option>
                                <option value="Rp 1.000.000 - Rp 3.000.000" {{ old('penghasilan_ortu', Auth::user()->mahasiswa->penghasilan_ortu ?? '') == 'Rp 1.000.000 - Rp 3.000.000' ? 'selected' : '' }}>Rp 1.000.000 - Rp 3.000.000</option>
                                <option value="Rp 3.000.000 - Rp 5.000.000" {{ old('penghasilan_ortu', Auth::user()->mahasiswa->penghasilan_ortu ?? '') == 'Rp 3.000.000 - Rp 5.000.000' ? 'selected' : '' }}>Rp 3.000.000 - Rp 5.000.000</option>
                                <option value="> Rp 5.000.000" {{ old('penghasilan_ortu', Auth::user()->mahasiswa->penghasilan_ortu ?? '') == '> Rp 5.000.000' ? 'selected' : '' }}>> Rp 5.000.000</option>
                            </select>
                            @error('penghasilan_ortu')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat Orang Tua</label>
                            <textarea name="alamat_ortu" class="form-control @error('alamat_ortu') is-invalid @enderror" rows="2" placeholder="Alamat lengkap orang tua">{{ old('alamat_ortu', Auth::user()->mahasiswa->alamat_ortu ?? '') }}</textarea>
                            @error('alamat_ortu')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                            @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: JPG, PNG. Maksimal 2MB</small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Update Password -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-shield-lock me-2"></i>Ubah Password
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key me-2"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
