@extends('layouts.app')

@section('title', 'Statistik Akademik')

@section('content')
<div class="page-title">
    <h4>Statistik Akademik Fakultas</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Statistik</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Distribusi Status Mahasiswa -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Distribusi Status Mahasiswa</h6>
            </div>
            <div class="card-body">
                @php
                    $total = $distribusiStatus->sum('jumlah');
                    $statusColors = [
                        'aktif' => 'success',
                        'cuti' => 'warning',
                        'lulus' => 'info',
                        'do' => 'danger',
                        'keluar' => 'secondary',
                    ];
                @endphp
                
                @forelse($distribusiStatus as $status)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-capitalize">{{ $status->status }}</span>
                        <span>{{ $status->jumlah }} ({{ $total > 0 ? number_format(($status->jumlah / $total) * 100, 1) : 0 }}%)</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ $statusColors[$status->status] ?? 'secondary' }}" 
                             style="width: {{ $total > 0 ? ($status->jumlah / $total) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">Tidak ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Distribusi IPK -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Distribusi IPK Mahasiswa Aktif</h6>
            </div>
            <div class="card-body">
                @php
                    $totalIpk = array_sum($distribusiIpk);
                @endphp
                
                <div class="row text-center">
                    <div class="col">
                        <div class="card border-0 bg-success bg-opacity-10 mb-2">
                            <div class="card-body py-3">
                                <h3 class="text-success mb-0">{{ $distribusiIpk['cumlaude'] }}</h3>
                            </div>
                        </div>
                        <small>Cumlaude<br><span class="text-muted">(≥3.50)</span></small>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-info bg-opacity-10 mb-2">
                            <div class="card-body py-3">
                                <h3 class="text-info mb-0">{{ $distribusiIpk['sangat_memuaskan'] }}</h3>
                            </div>
                        </div>
                        <small>Sangat Memuaskan<br><span class="text-muted">(3.00-3.49)</span></small>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-primary bg-opacity-10 mb-2">
                            <div class="card-body py-3">
                                <h3 class="text-primary mb-0">{{ $distribusiIpk['memuaskan'] }}</h3>
                            </div>
                        </div>
                        <small>Memuaskan<br><span class="text-muted">(2.50-2.99)</span></small>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-warning bg-opacity-10 mb-2">
                            <div class="card-body py-3">
                                <h3 class="text-warning mb-0">{{ $distribusiIpk['cukup'] }}</h3>
                            </div>
                        </div>
                        <small>Cukup<br><span class="text-muted">(2.00-2.49)</span></small>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-danger bg-opacity-10 mb-2">
                            <div class="card-body py-3">
                                <h3 class="text-danger mb-0">{{ $distribusiIpk['kurang'] }}</h3>
                            </div>
                        </div>
                        <small>Kurang<br><span class="text-muted">(<2.00)</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Mahasiswa Baru -->
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Trend Penerimaan Mahasiswa Baru (5 Tahun Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    @forelse($trendMahasiswaBaru->reverse() as $data)
                    <div class="col-md-2 text-center mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h3 class="text-primary mb-1">{{ $data->jumlah }}</h3>
                                <small class="text-muted">Angkatan {{ $data->angkatan }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">
                        Tidak ada data trend mahasiswa baru
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
