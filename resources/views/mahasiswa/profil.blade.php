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

<div class="row g-4">
    <!-- Kartu Profil -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <!-- Foto Profil -->
                <div class="mb-4">
                    @if($mahasiswa->foto)
                    <img src="{{ Storage::url($mahasiswa->foto) }}" alt="Foto Profil" 
                         class="rounded-circle shadow" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center shadow"
                         style="width: 150px; height: 150px;">
                        <span class="text-white" style="font-size: 4rem;">
                            {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
                        </span>
                    </div>
                    @endif
                </div>
                
                <h4 class="mb-1">{{ $mahasiswa->nama }}</h4>
                <p class="text-muted mb-3">{{ $mahasiswa->nim }}</p>
                
                <span class="badge bg-{{ $mahasiswa->status == 'Aktif' ? 'success' : ($mahasiswa->status == 'Cuti' ? 'warning' : 'secondary') }} fs-6 mb-3">
                    {{ $mahasiswa->status }}
                </span>
                
                <hr>
                
                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="mb-0 text-primary">{{ number_format($ipk, 2) }}</h5>
                        <small class="text-muted">IPK</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0 text-success">{{ $totalSks }}</h5>
                        <small class="text-muted">SKS</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0 text-info">{{ $mahasiswa->semester_aktif ?? 1 }}</h5>
                        <small class="text-muted">Semester</small>
                    </div>
                </div>
                
                <hr>
                
                <!-- Quick Actions -->
                <div class="d-grid gap-2">
                    <a href="{{ route('profile') }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Profil
                    </a>
                    <a href="{{ route('mahasiswa.download-kartu') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-download me-2"></i>Download Kartu Mahasiswa
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Statistik Keuangan -->
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-wallet2 me-2"></i>Ringkasan Keuangan
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Total Tagihan</small>
                        <strong>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Total Dibayar</small>
                        <strong class="text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Sisa Tagihan</small>
                        <strong class="text-{{ ($totalTagihan - $totalDibayar) > 0 ? 'danger' : 'success' }}">
                            Rp {{ number_format(max(0, $totalTagihan - $totalDibayar), 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
                
                @php
                    $persentase = $totalTagihan > 0 ? min(100, ($totalDibayar / $totalTagihan) * 100) : 0;
                @endphp
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persentase }}%"></div>
                </div>
                <small class="text-muted">{{ number_format($persentase, 0) }}% terbayar</small>
            </div>
        </div>
    </div>
    
    <!-- Detail Informasi -->
    <div class="col-lg-8">
        <!-- Data Pribadi -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Data Pribadi
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nama Lengkap</label>
                        <p class="mb-0 fw-semibold">{{ $mahasiswa->nama }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">NIM</label>
                        <p class="mb-0 fw-semibold">{{ $mahasiswa->nim }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jenis Kelamin</label>
                        <p class="mb-0">{{ $mahasiswa->jenis_kelamin ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tempat, Tanggal Lahir</label>
                        <p class="mb-0">
                            {{ $mahasiswa->tempat_lahir ?? '-' }}{{ $mahasiswa->tanggal_lahir ? ', ' . $mahasiswa->tanggal_lahir->format('d F Y') : '' }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Agama</label>
                        <p class="mb-0">{{ $mahasiswa->agama ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">No. Telepon</label>
                        <p class="mb-0">{{ $mahasiswa->no_hp ?? '-' }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">Alamat</label>
                        <p class="mb-0">{{ $mahasiswa->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Akademik -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-mortarboard me-2"></i>Data Akademik
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Program Studi</label>
                        <p class="mb-0 fw-semibold">{{ $mahasiswa->programStudi->nama ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Fakultas</label>
                        <p class="mb-0">{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Angkatan</label>
                        <p class="mb-0">{{ $mahasiswa->angkatan ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Semester Aktif</label>
                        <p class="mb-0">Semester {{ $mahasiswa->semester_aktif ?? 1 }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Dosen Wali</label>
                        <p class="mb-0">{{ $mahasiswa->dosenWali->nama ?? 'Belum ditentukan' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Status</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ $mahasiswa->status == 'Aktif' ? 'success' : ($mahasiswa->status == 'Cuti' ? 'warning' : 'secondary') }}">
                                {{ $mahasiswa->status }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Orang Tua -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-people me-2"></i>Data Orang Tua / Wali
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nama Ayah</label>
                        <p class="mb-0">{{ $mahasiswa->nama_ayah ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Pekerjaan Ayah</label>
                        <p class="mb-0">{{ $mahasiswa->pekerjaan_ayah ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nama Ibu</label>
                        <p class="mb-0">{{ $mahasiswa->nama_ibu ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Pekerjaan Ibu</label>
                        <p class="mb-0">{{ $mahasiswa->pekerjaan_ibu ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">No. Telepon Orang Tua</label>
                        <p class="mb-0">{{ $mahasiswa->no_hp_ortu ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Penghasilan Orang Tua</label>
                        <p class="mb-0">{{ $mahasiswa->penghasilan_ortu ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Akun -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-shield-lock me-2"></i>Informasi Akun
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Email</label>
                        <p class="mb-0">{{ $mahasiswa->user->email ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Terdaftar Sejak</label>
                        <p class="mb-0">{{ $mahasiswa->created_at?->format('d F Y') ?? '-' }}</p>
                    </div>
                </div>
                <a href="{{ route('profile') }}" class="btn btn-outline-primary">
                    <i class="bi bi-key me-2"></i>Ubah Password
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
