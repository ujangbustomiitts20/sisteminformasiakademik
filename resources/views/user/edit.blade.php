@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="page-title">
    <h4>Edit User</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.index') }}">User</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2"></i>Form Edit User
            </div>
            <div class="card-body">
                <form action="{{ route('user.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role (Legacy)</label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                            <option value="">-- Tidak Menggunakan Legacy Role --</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="dosen" {{ old('role', $user->role) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="mahasiswa" {{ old('role', $user->role) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="kaprodi" {{ old('role', $user->role) == 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                            <option value="dekan" {{ old('role', $user->role) == 'dekan' ? 'selected' : '' }}>Dekan</option>
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
                                           class="form-check-input dynamic-role-checkbox" id="dynamic_role_{{ $role->id }}"
                                           {{ in_array($role->id, old('dynamic_roles', $userRoles ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dynamic_role_{{ $role->id }}">
                                        <span class="badge bg-{{ $role->warna }} me-1">{{ $role->nama }}</span>
                                        @if($role->is_system)
                                            <span class="badge bg-secondary">Sistem</span>
                                        @endif
                                        <br><small class="text-muted">{{ $role->deskripsi }}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('dynamic_roles')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role Utama (Primary)</label>
                        <select name="primary_role" class="form-select @error('primary_role') is-invalid @enderror">
                            <option value="">-- Pilih Role Utama --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('primary_role', $primaryRoleId ?? '') == $role->id ? 'selected' : '' }}>
                                    {{ $role->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('primary_role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Role utama menentukan tampilan default dan dashboard.</small>
                    </div>
                    @endif

                    <hr>
                    <p class="text-muted"><small>Kosongkan password jika tidak ingin mengubah</small></p>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update
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
                <i class="bi bi-info-circle me-2"></i>Info User
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="150">ID</td>
                        <td>: {{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td>Terdaftar</td>
                        <td>: {{ $user->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Terakhir Update</td>
                        <td>: {{ $user->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>

                @if($user->role == 'dosen' && $user->dosen)
                <div class="alert alert-primary">
                    <strong>Terhubung dengan data Dosen:</strong><br>
                    NIDN: {{ $user->dosen->nidn }}<br>
                    Nama: {{ $user->dosen->nama }}
                </div>
                @endif

                @if($user->role == 'mahasiswa' && $user->mahasiswa)
                <div class="alert alert-success">
                    <strong>Terhubung dengan data Mahasiswa:</strong><br>
                    NIM: {{ $user->mahasiswa->nim }}<br>
                    Nama: {{ $user->mahasiswa->nama }}
                </div>
                @endif

                @if($user->roles->count() > 0)
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <i class="bi bi-shield-check me-2"></i>Role Dinamis Aktif
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($user->roles as $role)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="badge bg-{{ $role->warna }}">{{ $role->nama }}</span>
                                        @if($role->pivot->is_primary)
                                            <span class="badge bg-primary ms-1">Primary</span>
                                        @endif
                                    </span>
                                    <small class="text-muted">{{ $role->permissions_count ?? $role->permissions->count() }} permissions</small>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
