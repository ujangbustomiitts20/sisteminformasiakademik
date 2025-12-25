@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="page-title">
    <h4>Detail User</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.index') }}">User</a></li>
            <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'dosen' ? 'primary' : 'success') }} text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                @if($user->role == 'admin')
                <span class="badge bg-danger fs-6">Admin</span>
                @elseif($user->role == 'dosen')
                <span class="badge bg-primary fs-6">Dosen</span>
                @else
                <span class="badge bg-success fs-6">Mahasiswa</span>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>Aksi
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('user.edit', $user) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </a>
                    <form action="{{ route('user.reset-password', $user) }}" method="POST" onsubmit="return confirm('Reset password user ini?')">
                        @csrf
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bi bi-key me-2"></i>Reset Password
                        </button>
                    </form>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('user.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-2"></i>Hapus User
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi User
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td width="200" class="text-muted">ID</td>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Role</td>
                        <td>{{ ucfirst($user->role) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terdaftar</td>
                        <td>{{ $user->created_at->format('d M Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terakhir Update</td>
                        <td>{{ $user->updated_at->format('d M Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($user->role == 'dosen')
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Data Dosen
            </div>
            <div class="card-body">
                @if($user->dosen)
                <table class="table">
                    <tr>
                        <td width="200" class="text-muted">NIDN</td>
                        <td>{{ $user->dosen->nidn }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>{{ $user->dosen->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $user->dosen->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan Fungsional</td>
                        <td>{{ $user->dosen->jabatan_fungsional ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($user->dosen->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @elseif($user->dosen->status == 'Cuti')
                            <span class="badge bg-warning">Cuti</span>
                            @else
                            <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                    </tr>
                </table>
                <a href="{{ route('dosen.show', $user->dosen) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye me-1"></i>Lihat Detail Dosen
                </a>
                @else
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    User ini belum memiliki data dosen. 
                    <a href="{{ route('dosen.create') }}">Tambah data dosen</a>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($user->role == 'mahasiswa')
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-mortarboard me-2"></i>Data Mahasiswa
            </div>
            <div class="card-body">
                @if($user->mahasiswa)
                <table class="table">
                    <tr>
                        <td width="200" class="text-muted">NIM</td>
                        <td>{{ $user->mahasiswa->nim }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>{{ $user->mahasiswa->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $user->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $user->mahasiswa->angkatan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester Aktif</td>
                        <td>{{ $user->mahasiswa->semester_aktif }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($user->mahasiswa->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @elseif($user->mahasiswa->status == 'Cuti')
                            <span class="badge bg-warning">Cuti</span>
                            @elseif($user->mahasiswa->status == 'Lulus')
                            <span class="badge bg-info">Lulus</span>
                            @else
                            <span class="badge bg-danger">DO</span>
                            @endif
                        </td>
                    </tr>
                </table>
                <a href="{{ route('mahasiswa.show', $user->mahasiswa) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye me-1"></i>Lihat Detail Mahasiswa
                </a>
                @else
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    User ini belum memiliki data mahasiswa. 
                    <a href="{{ route('mahasiswa.create') }}">Tambah data mahasiswa</a>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('user.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar User
    </a>
</div>
@endsection
