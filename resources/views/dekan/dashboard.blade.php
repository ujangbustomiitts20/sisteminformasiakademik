@extends('layouts.app')

@section('title', 'Dashboard Dekan')

@section('content')
<div class="page-title">
    <h4>Dashboard Dekan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard Dekan</li>
        </ol>
    </nav>
</div>

<!-- Info Fakultas -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="stat-icon bg-primary text-white rounded-circle p-3 me-3">
                <i class="bi bi-building fs-3"></i>
            </div>
            <div>
                <h5 class="mb-0">{{ $fakultas->nama }}</h5>
                <small class="text-muted">Fakultas</small>
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
                <i class="bi bi-diagram-3 stat-icon"></i>
                <h3 class="mb-1">{{ number_format($stats['total_prodi']) }}</h3>
                <small>Program Studi</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card bg-success h-100">
            <div class="card-body position-relative">
                <i class="bi bi-people stat-icon"></i>
                <h3 class="mb-1">{{ number_format($stats['total_mahasiswa']) }}</h3>
                <small>Mahasiswa Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card bg-info h-100">
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
                <div class="text-danger mb-2">
                    <i class="bi bi-calendar-x fs-2"></i>
                </div>
                <h4 class="mb-0">{{ $stats['cuti_pending'] }}</h4>
                <small class="text-muted">Cuti Menunggu Approval</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-primary mb-2">
                    <i class="bi bi-mortarboard-fill fs-2"></i>
                </div>
                <h4 class="mb-0">{{ $stats['wisuda_pending'] }}</h4>
                <small class="text-muted">Wisuda Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-award fs-2"></i>
                </div>
                <h4 class="mb-0">{{ $stats['lulusan_tahun_ini'] }}</h4>
                <small class="text-muted">Lulusan Tahun Ini</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <a href="{{ route('dekan.cuti.index') }}" class="text-decoration-none">
                    <div class="text-warning mb-2">
                        <i class="bi bi-check2-circle fs-2"></i>
                    </div>
                    <span class="text-muted">Approval Cuti</span>
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
                    <a href="{{ route('dekan.cuti.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-check-circle me-2"></i>Approval Cuti
                        @if($stats['cuti_pending'] > 0)
                        <span class="badge bg-danger ms-2">{{ $stats['cuti_pending'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('dekan.tugas-akhir.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-journal-bookmark me-2"></i>Monitor Tugas Akhir
                    </a>
                    <a href="{{ route('dekan.wisuda.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-mortarboard me-2"></i>Monitor Wisuda
                    </a>
                    <a href="{{ route('dekan.statistik.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-graph-up me-2"></i>Statistik Akademik
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik per Prodi -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistik per Program Studi</h6>
                <a href="{{ route('dekan.program-studi.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Program Studi</th>
                                <th>Mahasiswa</th>
                                <th>Dosen</th>
                                <th>Rasio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statsPerProdi as $prodi)
                            <tr>
                                <td>
                                    <a href="{{ route('dekan.program-studi.show', $prodi) }}">{{ $prodi->nama }}</a>
                                </td>
                                <td>{{ $prodi->mahasiswa_count }}</td>
                                <td>{{ $prodi->dosen_count }}</td>
                                <td>
                                    @php
                                        $rasio = $prodi->dosen_count > 0 ? round($prodi->mahasiswa_count / $prodi->dosen_count) : 0;
                                    @endphp
                                    <span class="badge bg-{{ $rasio <= 25 ? 'success' : ($rasio <= 35 ? 'warning' : 'danger') }}">
                                        1:{{ $rasio }}
                                    </span>
                                </td>
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
</div>
@endsection
