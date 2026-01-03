@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="page-title">
    <h4>Laporan & Statistik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Laporan</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Laporan Mahasiswa</h5>
                <p class="card-text text-muted">Data mahasiswa per program studi, angkatan, dan status</p>
                <a href="{{ route('laporan.mahasiswa') }}" class="btn btn-primary">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-clipboard-data text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Laporan Nilai</h5>
                <p class="card-text text-muted">Rekap nilai mahasiswa per mata kuliah dan semester</p>
                <a href="{{ route('laporan.nilai') }}" class="btn btn-success">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-cash-stack text-warning" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Laporan Keuangan</h5>
                <p class="card-text text-muted">Rekap pembayaran dan tunggakan mahasiswa</p>
                <a href="{{ route('laporan.keuangan') }}" class="btn btn-warning">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-clipboard-check text-info" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Laporan Absensi</h5>
                <p class="card-text text-muted">Rekap kehadiran mahasiswa per mata kuliah</p>
                <a href="{{ route('laporan.absensi') }}" class="btn btn-info">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-bar-chart text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Statistik Akademik</h5>
                <p class="card-text text-muted">Grafik dan statistik data akademik</p>
                <a href="{{ route('laporan.statistik') }}" class="btn btn-danger">
                    <i class="bi bi-eye me-1"></i>Lihat Statistik
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-mortarboard text-purple" style="font-size: 3rem; color: #6f42c1;"></i>
                </div>
                <h5 class="card-title">Laporan Wisuda & Lulusan</h5>
                <p class="card-text text-muted">Data lulusan per periode wisuda dan predikat</p>
                <a href="{{ route('laporan.wisuda') }}" class="btn btn-purple" style="background: #6f42c1; color: white;">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-graph-up-arrow text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Distribusi IPK</h5>
                <p class="card-text text-muted">Analisis distribusi IPK mahasiswa per prodi</p>
                <a href="{{ route('laporan.ipk') }}" class="btn btn-success">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-journal-text text-info" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Laporan KRS</h5>
                <p class="card-text text-muted">Rekap pengambilan KRS per tahun akademik</p>
                <a href="{{ route('laporan.krs') }}" class="btn btn-info">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-badge text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Laporan Dosen</h5>
                <p class="card-text text-muted">Data dosen dan beban mengajar</p>
                <a href="{{ route('laporan.dosen') }}" class="btn btn-primary">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-award text-warning" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Statistik Kelulusan</h5>
                <p class="card-text text-muted">Trend kelulusan dan statistik per tahun</p>
                <a href="{{ route('laporan.kelulusan') }}" class="btn btn-warning">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
