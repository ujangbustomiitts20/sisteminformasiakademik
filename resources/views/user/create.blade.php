@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="page-title">
    <h4>Tambah User</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.index') }}">User</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus me-2"></i>Form Tambah User
            </div>
            <div class="card-body">
                <form action="{{ route('user.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role (Legacy)</label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                            <option value="">-- Tidak Menggunakan Legacy Role --</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="kaprodi" {{ old('role') == 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                            <option value="dekan" {{ old('role') == 'dekan' ? 'selected' : '' }}>Dekan</option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Opsional. Gunakan Role Dinamis untuk manajemen akses yang lebih fleksibel.</small>
                    </div>

                    @if(isset($roles) && $roles->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Role Dinamis</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($roles as $role)
                                <div class="form-check">
                                    <input type="checkbox" name="dynamic_roles[]" value="{{ $role->id }}" 
                                           class="form-check-input" id="dynamic_role_{{ $role->id }}"
                                           {{ in_array($role->id, old('dynamic_roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dynamic_role_{{ $role->id }}">
                                        <span class="badge bg-{{ $role->warna }} me-1">{{ $role->nama }}</span>
                                        @if($role->is_system)
                                            <span class="badge bg-secondary">Sistem</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role Utama (Primary)</label>
                        <select name="primary_role" class="form-select">
                            <option value="">-- Pilih Role Utama --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('primary_role') == $role->id ? 'selected' : '' }}>
                                    {{ $role->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Role utama menentukan tampilan default.</small>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('user.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-lightbulb me-2"></i>Tips:</h6>
                    <ul class="mb-0">
                        <li>User dengan role <strong>Admin</strong> dapat mengakses semua fitur.</li>
                        <li>User dengan role <strong>Dosen</strong> hanya dapat mengakses fitur khusus dosen.</li>
                        <li>User dengan role <strong>Mahasiswa</strong> hanya dapat mengakses fitur khusus mahasiswa.</li>
                    </ul>
                </div>
                <div class="alert alert-warning mb-0">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>Perhatian:</h6>
                    <p class="mb-0">Untuk menambah Dosen atau Mahasiswa dengan data lengkap (NIDN/NIM, Program Studi, dll), gunakan menu <strong>Dosen</strong> atau <strong>Mahasiswa</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
