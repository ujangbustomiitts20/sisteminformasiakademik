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
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="dosen" {{ old('role', $user->role) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="mahasiswa" {{ old('role', $user->role) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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
            </div>
        </div>
    </div>
</div>
@endsection
