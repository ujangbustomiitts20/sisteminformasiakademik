@extends('layouts.app')

@section('title', 'Statistik Prodi')

@section('content')
<div class="page-title">
    <h4>Statistik Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Statistik</li>
        </ol>
    </nav>
</div>

<!-- Overview Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $statistik['total_mahasiswa'] ?? 0 }}</h3>
                        <small>Total Mahasiswa</small>
                    </div>
                    <i class="bi bi-people" style="font-size: 2.5rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $statistik['mahasiswa_aktif'] ?? 0 }}</h3>
                        <small>Mahasiswa Aktif</small>
                    </div>
                    <i class="bi bi-person-check" style="font-size: 2.5rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $statistik['total_dosen'] ?? 0 }}</h3>
                        <small>Total Dosen</small>
                    </div>
                    <i class="bi bi-person-badge" style="font-size: 2.5rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $statistik['total_mata_kuliah'] ?? 0 }}</h3>
                        <small>Mata Kuliah</small>
                    </div>
                    <i class="bi bi-book" style="font-size: 2.5rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Mahasiswa per Angkatan -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Mahasiswa per Angkatan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Angkatan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Aktif</th>
                                <th class="text-center">Rata IPK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mahasiswaPerAngkatan as $angkatan)
                            <tr>
                                <td><strong>{{ $angkatan->angkatan }}</strong></td>
                                <td class="text-center">{{ $angkatan->total }}</td>
                                <td class="text-center">{{ $angkatan->aktif ?? $angkatan->total }}</td>
                                <td class="text-center">{{ number_format($angkatan->rata_ipk ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Mahasiswa -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Status Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-success mb-0">{{ $statusMahasiswa['aktif'] ?? 0 }}</h4>
                            <small class="text-muted">Aktif</small>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-warning mb-0">{{ $statusMahasiswa['cuti'] ?? 0 }}</h4>
                            <small class="text-muted">Cuti</small>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-info mb-0">{{ $statusMahasiswa['lulus'] ?? 0 }}</h4>
                            <small class="text-muted">Lulus</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-3">
                            <h4 class="text-danger mb-0">{{ $statusMahasiswa['do'] ?? 0 }}</h4>
                            <small class="text-muted">DO</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-3">
                            <h4 class="text-secondary mb-0">{{ $statusMahasiswa['mengundurkan_diri'] ?? 0 }}</h4>
                            <small class="text-muted">Mundur</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-3">
                            <h4 class="text-dark mb-0">{{ $statusMahasiswa['tidak_aktif'] ?? 0 }}</h4>
                            <small class="text-muted">Non-Aktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Distribusi IPK -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Distribusi IPK Mahasiswa Aktif</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-start border-success border-4 p-2">
                            <h4 class="text-success mb-0">{{ $distribusiIpk['cumlaude'] ?? 0 }}</h4>
                            <small class="text-muted">Cumlaude (≥3.50)</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border-start border-primary border-4 p-2">
                            <h4 class="text-primary mb-0">{{ $distribusiIpk['sangat_memuaskan'] ?? 0 }}</h4>
                            <small class="text-muted">Sangat Memuaskan (3.00-3.49)</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border-start border-info border-4 p-2">
                            <h4 class="text-info mb-0">{{ $distribusiIpk['memuaskan'] ?? 0 }}</h4>
                            <small class="text-muted">Memuaskan (2.50-2.99)</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border-start border-danger border-4 p-2">
                            <h4 class="text-danger mb-0">{{ $distribusiIpk['rendah'] ?? 0 }}</h4>
                            <small class="text-muted">Perlu Perhatian (<2.50)</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h5 class="mb-0">Rata-rata IPK Prodi: <strong class="text-primary">{{ number_format($statistik['rata_ipk'] ?? 0, 2) }}</strong></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Ekspor Data</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('kaprodi.export.mahasiswa') }}" class="btn btn-outline-primary">
                        <i class="bi bi-download me-2"></i> Export Data Mahasiswa (CSV)
                    </a>
                    <a href="{{ route('kaprodi.nilai.export') }}" class="btn btn-outline-success">
                        <i class="bi bi-download me-2"></i> Export Data Nilai (CSV)
                    </a>
                </div>

                <hr>

                <h6 class="text-muted mb-3">Akses Cepat</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('kaprodi.mahasiswa.bermasalah') }}" class="btn btn-outline-danger w-100">
                            <i class="bi bi-exclamation-triangle me-1"></i> Mahasiswa Bermasalah
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('kaprodi.nilai.monitoring-ipk') }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-graph-up me-1"></i> Monitoring IPK
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('kaprodi.bimbingan.index') }}" class="btn btn-outline-warning w-100">
                            <i class="bi bi-chat-dots me-1"></i> Bimbingan Akademik
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('kaprodi.absensi.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-calendar-check me-1"></i> Monitoring Absensi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
