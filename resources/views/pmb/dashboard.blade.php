@extends('layouts.app')

@section('title', 'Dashboard PMB')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Dashboard PMB</h1>
            <p class="text-muted mb-0">Penerimaan Mahasiswa Baru</p>
        </div>
        <div>
            @if($periodeAktif)
                <span class="badge bg-success fs-6">{{ $periodeAktif->nama }}</span>
            @else
                <span class="badge bg-warning fs-6">Tidak ada periode aktif</span>
            @endif
        </div>
    </div>

    <!-- Info Periode & Gelombang -->
    @if($periodeAktif && $gelombangAktif)
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-calendar-event me-2"></i>Periode Aktif
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $periodeAktif->nama }}</h5>
                    <p class="card-text mb-1">
                        <strong>Tahun Akademik:</strong> {{ $periodeAktif->tahun_akademik }}
                    </p>
                    <p class="card-text mb-1">
                        <strong>Periode:</strong> {{ $periodeAktif->tanggal_mulai->format('d M Y') }} - {{ $periodeAktif->tanggal_selesai->format('d M Y') }}
                    </p>
                    <p class="card-text mb-0">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $periodeAktif->status_badge }}">{{ $periodeAktif->status }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-layers me-2"></i>Gelombang Aktif
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $gelombangAktif->nama }}</h5>
                    <p class="card-text mb-1">
                        <strong>Pendaftaran:</strong> {{ $gelombangAktif->tanggal_mulai_daftar->format('d M Y') }} - {{ $gelombangAktif->tanggal_selesai_daftar->format('d M Y') }}
                    </p>
                    @if($gelombangAktif->tanggal_ujian)
                    <p class="card-text mb-1">
                        <strong>Ujian:</strong> {{ $gelombangAktif->tanggal_ujian->format('d M Y') }}
                    </p>
                    @endif
                    <p class="card-text mb-0">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $gelombangAktif->status_badge }}">{{ $gelombangAktif->status_pendaftaran }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-4 col-lg-2 mb-3">
            <a href="{{ route('pmb.calon-mahasiswa.index') }}" class="text-decoration-none">
                <div class="card bg-primary text-white h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Pendaftar</h6>
                                <h2 class="mb-0">{{ number_format($stats['total_pendaftar']) }}</h2>
                            </div>
                            <i class="bi bi-people-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small class="text-white-50"><i class="bi bi-arrow-right-circle me-1"></i>Lihat Detail</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <a href="{{ route('pmb.calon-mahasiswa.index', ['status' => 'terdaftar']) }}" class="text-decoration-none">
                <div class="card bg-info text-white h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Terdaftar</h6>
                                <h2 class="mb-0">{{ number_format($stats['pendaftar_terdaftar']) }}</h2>
                            </div>
                            <i class="bi bi-person-check-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small class="text-white-50"><i class="bi bi-arrow-right-circle me-1"></i>Lihat Detail</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <a href="{{ route('pmb.seleksi.hasil', ['status' => 'lulus']) }}" class="text-decoration-none">
                <div class="card bg-success text-white h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Lulus Seleksi</h6>
                                <h2 class="mb-0">{{ number_format($stats['lulus']) }}</h2>
                            </div>
                            <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small class="text-white-50"><i class="bi bi-arrow-right-circle me-1"></i>Lihat Detail</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <a href="{{ route('pmb.seleksi.hasil', ['status' => 'tidak_lulus']) }}" class="text-decoration-none">
                <div class="card bg-danger text-white h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Tidak Lulus</h6>
                                <h2 class="mb-0">{{ number_format($stats['tidak_lulus']) }}</h2>
                            </div>
                            <i class="bi bi-x-circle-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small class="text-white-50"><i class="bi bi-arrow-right-circle me-1"></i>Lihat Detail</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <a href="{{ route('pmb.calon-mahasiswa.index', ['status' => 'daftar_ulang']) }}" class="text-decoration-none">
                <div class="card bg-warning text-dark h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Daftar Ulang</h6>
                                <h2 class="mb-0">{{ number_format($stats['daftar_ulang']) }}</h2>
                            </div>
                            <i class="bi bi-arrow-repeat fs-1 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small><i class="bi bi-arrow-right-circle me-1"></i>Lihat Detail</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <a href="{{ route('pmb.calon-mahasiswa.index', ['status' => 'menjadi_mahasiswa']) }}" class="text-decoration-none">
                <div class="card bg-dark text-white h-100 stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Jadi Mahasiswa</h6>
                                <h2 class="mb-0">{{ number_format($stats['menjadi_mahasiswa']) }}</h2>
                            </div>
                            <i class="bi bi-mortarboard-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small class="text-white-50"><i class="bi bi-arrow-right-circle me-1"></i>Lihat Detail</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Statistik Daftar Ulang & Pembayaran -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-arrow-repeat me-2"></i>Statistik Daftar Ulang</span>
                    <a href="{{ route('pmb.daftar-ulang.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <a href="{{ route('pmb.daftar-ulang.index') }}" class="text-decoration-none">
                                <h4 class="text-primary mb-0">{{ $statsDaftarUlang['total'] }}</h4>
                                <small class="text-muted">Total</small>
                            </a>
                        </div>
                        <div class="col-3">
                            <a href="{{ route('pmb.daftar-ulang.index', ['status' => 'pending']) }}" class="text-decoration-none">
                                <h4 class="text-warning mb-0">{{ $statsDaftarUlang['pending'] }}</h4>
                                <small class="text-muted">Pending</small>
                            </a>
                        </div>
                        <div class="col-3">
                            <a href="{{ route('pmb.daftar-ulang.index', ['status' => 'lunas']) }}" class="text-decoration-none">
                                <h4 class="text-info mb-0">{{ $statsDaftarUlang['lunas'] }}</h4>
                                <small class="text-muted">Lunas</small>
                            </a>
                        </div>
                        <div class="col-3">
                            <a href="{{ route('pmb.daftar-ulang.index', ['status' => 'selesai']) }}" class="text-decoration-none">
                                <h4 class="text-success mb-0">{{ $statsDaftarUlang['selesai'] }}</h4>
                                <small class="text-muted">Selesai</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-cash-stack me-2"></i>Statistik Pembayaran</span>
                    <a href="{{ route('pmb.pembayaran.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('pmb.pembayaran.index', ['jenis' => 'pendaftaran']) }}" class="text-decoration-none d-block mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-dark">Pendaftaran:</span>
                                    <strong class="text-primary">{{ $statsPembayaran['terverifikasi_pendaftaran'] }}/{{ $statsPembayaran['total_pendaftaran'] }}</strong>
                                </div>
                            </a>
                            <a href="{{ route('pmb.pembayaran.index', ['jenis' => 'daftar_ulang']) }}" class="text-decoration-none d-block">
                                <div class="d-flex justify-content-between">
                                    <span class="text-dark">Daftar Ulang:</span>
                                    <strong class="text-primary">{{ $statsPembayaran['terverifikasi_daftar_ulang'] }}/{{ $statsPembayaran['total_daftar_ulang'] }}</strong>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 text-center border-start">
                            <a href="{{ route('pmb.pembayaran.index', ['status' => 'terverifikasi']) }}" class="text-decoration-none">
                                <small class="text-muted d-block">Total Pendapatan</small>
                                <h4 class="text-success mb-0">Rp {{ number_format($statsPembayaran['total_pendapatan'], 0, ',', '.') }}</h4>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables -->
    <div class="row">
        <!-- Grafik Pie - Status Pendaftaran -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-pie-chart-fill me-2"></i>Status Pendaftaran
                </div>
                <div class="card-body">
                    <canvas id="chartStatusPendaftaran" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Bar - Pendaftar per Prodi -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-bar-chart-fill me-2"></i>Pendaftar per Program Studi
                </div>
                <div class="card-body">
                    <canvas id="chartPendaftarProdi" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Grafik Line - Pendaftar 7 Hari Terakhir -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-graph-up me-2"></i>Tren Pendaftar 7 Hari Terakhir
                </div>
                <div class="card-body">
                    <canvas id="chartPendaftarHarian" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Doughnut - Jalur Seleksi -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-signpost-split-fill me-2"></i>Pendaftar per Jalur
                </div>
                <div class="card-body">
                    <canvas id="chartJalurSeleksi" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Grafik Horizontal Bar - Progress Daftar Ulang -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-arrow-repeat me-2"></i>Progress Daftar Ulang
                </div>
                <div class="card-body">
                    <canvas id="chartDaftarUlang" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Hasil Seleksi -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-trophy-fill me-2"></i>Hasil Seleksi
                </div>
                <div class="card-body">
                    <canvas id="chartHasilSeleksi" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-link-45deg me-2"></i>Menu PMB
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.periode.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-calendar-event fs-1 mb-2 d-block"></i>
                                Periode PMB
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.gelombang.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-layers fs-1 mb-2 d-block"></i>
                                Gelombang
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.jalur-seleksi.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-signpost-split fs-1 mb-2 d-block"></i>
                                Jalur Seleksi
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.biaya-pendaftaran.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-cash-coin fs-1 mb-2 d-block"></i>
                                Biaya Pendaftaran
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.kuota.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-bar-chart fs-1 mb-2 d-block"></i>
                                Kuota PMB
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.calon-mahasiswa.index') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="bi bi-people fs-1 mb-2 d-block"></i>
                                Calon Mahasiswa
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.pembayaran.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-cash fs-1 mb-2 d-block"></i>
                                Pembayaran
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.seleksi.input-nilai') }}" class="btn btn-outline-danger w-100 py-3">
                                <i class="bi bi-pencil-square fs-1 mb-2 d-block"></i>
                                Input Nilai
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.seleksi.hasil') }}" class="btn btn-outline-dark w-100 py-3">
                                <i class="bi bi-trophy fs-1 mb-2 d-block"></i>
                                Hasil Seleksi
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('pmb.daftar-ulang.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-mortarboard fs-1 mb-2 d-block"></i>
                                Daftar Ulang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    .card-header {
        font-weight: 600;
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.3;
    }
    /* Stat card clickable */
    .stat-card {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.2) !important;
    }
    .stat-card .card-footer {
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
    .stat-card:hover .card-footer {
        opacity: 1;
    }
    /* Clickable numbers */
    a h4:hover {
        text-decoration: underline;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Warna yang konsisten
    const colors = {
        primary: '#0d6efd',
        success: '#198754',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#0dcaf0',
        secondary: '#6c757d',
        dark: '#212529',
        purple: '#6f42c1',
        pink: '#d63384',
        orange: '#fd7e14',
        teal: '#20c997',
        cyan: '#0dcaf0'
    };

    // Chart 1: Status Pendaftaran (Pie)
    new Chart(document.getElementById('chartStatusPendaftaran'), {
        type: 'pie',
        data: {
            labels: ['Terdaftar', 'Daftar Ulang', 'Jadi Mahasiswa'],
            datasets: [{
                data: [
                    {{ $stats['pendaftar_terdaftar'] }},
                    {{ $stats['daftar_ulang'] }},
                    {{ $stats['menjadi_mahasiswa'] }}
                ],
                backgroundColor: [colors.info, colors.warning, colors.dark],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Chart 2: Pendaftar per Prodi (Bar)
    new Chart(document.getElementById('chartPendaftarProdi'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($pendaftarPerProdi->pluck('programStudi.nama')->map(fn($n) => $n ?? 'N/A')) !!},
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: {!! json_encode($pendaftarPerProdi->pluck('total')) !!},
                backgroundColor: [
                    colors.primary, colors.success, colors.warning, colors.danger,
                    colors.info, colors.purple, colors.pink, colors.orange,
                    colors.teal, colors.secondary
                ],
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });

    // Chart 3: Pendaftar Harian (Line)
    new Chart(document.getElementById('chartPendaftarHarian'), {
        type: 'line',
        data: {
            labels: {!! json_encode($pendaftarHarian->pluck('tanggal')->map(fn($t) => \Carbon\Carbon::parse($t)->format('d M'))) !!},
            datasets: [{
                label: 'Pendaftar',
                data: {!! json_encode($pendaftarHarian->pluck('total')) !!},
                borderColor: colors.primary,
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: colors.primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Chart 4: Jalur Seleksi (Doughnut)
    new Chart(document.getElementById('chartJalurSeleksi'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($pendaftarPerJalur->pluck('jalurSeleksi.nama')->map(fn($n) => $n ?? 'N/A')) !!},
            datasets: [{
                data: {!! json_encode($pendaftarPerJalur->pluck('total')) !!},
                backgroundColor: [colors.success, colors.info, colors.warning, colors.danger, colors.purple],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, usePointStyle: true }
                }
            }
        }
    });

    // Chart 5: Daftar Ulang (Horizontal Bar)
    new Chart(document.getElementById('chartDaftarUlang'), {
        type: 'bar',
        data: {
            labels: ['Pending', 'Lunas', 'Selesai'],
            datasets: [{
                label: 'Jumlah',
                data: [
                    {{ $statsDaftarUlang['pending'] }},
                    {{ $statsDaftarUlang['lunas'] }},
                    {{ $statsDaftarUlang['selesai'] }}
                ],
                backgroundColor: [colors.warning, colors.info, colors.success],
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Chart 6: Hasil Seleksi (Bar dengan 2 dataset)
    new Chart(document.getElementById('chartHasilSeleksi'), {
        type: 'bar',
        data: {
            labels: ['Hasil Seleksi'],
            datasets: [
                {
                    label: 'Lulus',
                    data: [{{ $stats['lulus'] }}],
                    backgroundColor: colors.success,
                    borderRadius: 8
                },
                {
                    label: 'Tidak Lulus',
                    data: [{{ $stats['tidak_lulus'] }}],
                    backgroundColor: colors.danger,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush
