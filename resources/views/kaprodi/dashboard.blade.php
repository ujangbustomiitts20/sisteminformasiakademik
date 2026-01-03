@extends('layouts.app')

@section('title', 'Dashboard Ketua Prodi')

@section('content')
<div class="page-title">
    <h4>Dashboard Ketua Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard Kaprodi</li>
        </ol>
    </nav>
</div>

<!-- Info Prodi -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="stat-icon bg-primary text-white rounded-circle p-3 me-3">
                <i class="bi bi-diagram-3 fs-3"></i>
            </div>
            <div>
                <h5 class="mb-0">{{ $prodi->nama }}</h5>
                <small class="text-muted">{{ $prodi->fakultas->nama ?? '-' }}</small>
            </div>
            @if($tahunAktif)
            <div class="ms-auto text-end">
                <small class="text-muted">Tahun Akademik Aktif</small>
                <h6 class="mb-0">{{ $tahunAktif->tahun }} - {{ $tahunAktif->semester }}</h6>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card bg-primary h-100">
            <div class="card-body position-relative">
                <i class="bi bi-people stat-icon"></i>
                <h3 class="mb-1">{{ number_format($stats['total_mahasiswa']) }}</h3>
                <small>Mahasiswa Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card bg-success h-100">
            <div class="card-body position-relative">
                <i class="bi bi-person-workspace stat-icon"></i>
                <h3 class="mb-1">{{ number_format($stats['total_dosen']) }}</h3>
                <small>Dosen</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card bg-warning h-100">
            <div class="card-body position-relative">
                <i class="bi bi-journal-text stat-icon"></i>
                <h3 class="mb-1">{{ number_format($stats['krs_pending']) }}</h3>
                <small>KRS Menunggu</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card bg-info h-100">
            <div class="card-body position-relative">
                <i class="bi bi-journal-bookmark stat-icon"></i>
                <h3 class="mb-1">{{ number_format($stats['tugas_akhir_berjalan']) }}</h3>
                <small>TA Berjalan</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2">
                    <i class="bi bi-arrow-left-right fs-2"></i>
                </div>
                <h4 class="mb-0">{{ $stats['konversi_pending'] }}</h4>
                <small class="text-muted">Konversi Nilai Pending</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-danger mb-2">
                    <i class="bi bi-calendar-x fs-2"></i>
                </div>
                <h4 class="mb-0">{{ $stats['cuti_pending'] }}</h4>
                <small class="text-muted">Cuti Pending</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-briefcase fs-2"></i>
                </div>
                <h4 class="mb-0">{{ $stats['pkl_berjalan'] }}</h4>
                <small class="text-muted">PKL/Magang Berlangsung</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <a href="{{ route('kaprodi.krs.index') }}" class="text-decoration-none">
                    <div class="text-primary mb-2">
                        <i class="bi bi-check2-square fs-2"></i>
                    </div>
                    <span class="text-muted">Approval KRS</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('kaprodi.krs.index') }}?status=pending" class="btn btn-outline-primary">
                        <i class="bi bi-check-circle me-2"></i>Approve KRS
                        @if($stats['krs_pending'] > 0)
                        <span class="badge bg-danger ms-2">{{ $stats['krs_pending'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('kaprodi.tugas-akhir.index') }}?status=diajukan" class="btn btn-outline-info">
                        <i class="bi bi-journal-bookmark me-2"></i>Review Judul TA
                    </a>
                    <a href="{{ route('kaprodi.konversi-nilai.index') }}?status=diajukan" class="btn btn-outline-warning">
                        <i class="bi bi-arrow-left-right me-2"></i>Review Konversi Nilai
                    </a>
                    <a href="{{ route('kaprodi.cuti.index') }}?status=pending" class="btn btn-outline-secondary">
                        <i class="bi bi-calendar-x me-2"></i>Approval Cuti
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mahasiswa Bermasalah (IPK rendah) -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Mahasiswa Perlu Perhatian (IPK < 2.0)</h6>
                <a href="{{ route('kaprodi.mahasiswa.index') }}?ipk_max=2.0" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($mahasiswaBermasalah->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Angkatan</th>
                                <th>IPK</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mahasiswaBermasalah as $mhs)
                            <tr>
                                <td>{{ $mhs->nim }}</td>
                                <td>
                                    <a href="{{ route('kaprodi.mahasiswa.show', $mhs) }}">{{ $mhs->nama }}</a>
                                </td>
                                <td>{{ $mhs->angkatan }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ number_format($mhs->ipk, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $mhs->status == 'aktif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($mhs->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-emoji-smile fs-1"></i>
                    <p class="mt-2">Tidak ada mahasiswa dengan IPK < 2.0</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
