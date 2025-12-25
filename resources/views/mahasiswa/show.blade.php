@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Detail Mahasiswa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mahasiswa.index') }}">Mahasiswa</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('mahasiswa.edit', $mahasiswa) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>
</div>

<div class="row">
    <!-- Profil -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="user-avatar bg-primary mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff;">
                    {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $mahasiswa->nama }}</h5>
                <p class="text-muted mb-2">{{ $mahasiswa->nim }}</p>
                <span class="badge bg-{{ $mahasiswa->status == 'Aktif' ? 'success' : ($mahasiswa->status == 'Cuti' ? 'warning' : ($mahasiswa->status == 'Lulus' ? 'info' : 'danger')) }} fs-6">
                    {{ $mahasiswa->status }}
                </span>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="card mb-4">
            <div class="card-header">Statistik Akademik</div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-primary mb-0">{{ $ipk }}</h4>
                        <small class="text-muted">IPK</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success mb-0">{{ $totalSks }}</h4>
                        <small class="text-muted">SKS Lulus</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-info mb-0">{{ $mahasiswa->semester_aktif }}</h4>
                        <small class="text-muted">Semester</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Info Detail -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">Informasi Pribadi</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="200" class="text-muted">Email</td>
                        <td>{{ $mahasiswa->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Kelamin</td>
                        <td>{{ $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tempat, Tanggal Lahir</td>
                        <td>{{ $mahasiswa->tempat_lahir ?? '-' }}, {{ $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. Telepon</td>
                        <td>{{ $mahasiswa->telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat</td>
                        <td>{{ $mahasiswa->alamat ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">Informasi Akademik</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="200" class="text-muted">Program Studi</td>
                        <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fakultas</td>
                        <td>{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $mahasiswa->angkatan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dosen Wali</td>
                        <td>{{ $mahasiswa->dosenWali->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
