@extends('layouts.app')

@section('title', 'Dashboard Kepegawaian')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
    .stat-card .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
    }
    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .card-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .card-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    .card-gradient-info {
        background: linear-gradient(135deg, #00c6fb 0%, #005bea 100%);
        color: white;
    }
    .card-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    .summary-box {
        padding: 1rem;
        border-radius: 10px;
        background: #f8f9fa;
        border-left: 4px solid;
        transition: all 0.2s ease;
    }
    .summary-box:hover {
        transform: translateX(5px);
    }
    .summary-box.primary { border-color: #667eea; }
    .summary-box.success { border-color: #28a745; }
    .summary-box.danger { border-color: #dc3545; }
    .summary-box.warning { border-color: #ffc107; }
    .summary-box.info { border-color: #17a2b8; }
    
    .menu-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
    }
    .menu-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    .badge-notification {
        position: absolute;
        top: -5px;
        right: -5px;
        font-size: 0.65rem;
    }
    .progress-custom {
        height: 8px;
        border-radius: 4px;
    }
    .chart-container {
        position: relative;
        height: 250px;
    }
    .birthday-item {
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .birthday-item:hover {
        background: #f8f9fa;
    }
    .alert-card {
        border-radius: 12px;
        border: none;
    }
    .clickable-card {
        cursor: pointer;
        text-decoration: none;
    }
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .clickable-row:hover {
        background-color: rgba(102, 126, 234, 0.08) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard Kepegawaian</h1>
            <p class="text-muted mb-0">Ringkasan informasi sumber daya manusia</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dosen.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Dosen
            </a>
            <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i> Tambah Tendik
            </a>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row g-3 mb-4">
        <!-- Total Dosen -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('dosen.index') }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm h-100 clickable-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label mb-1">Total Dosen</p>
                                <h3 class="stat-value text-primary mb-0">{{ number_format($totalDosen) }}</h3>
                                <small class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $dosenAktif }} aktif</small>
                            </div>
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-person-badge"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Total Tendik -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('kepegawaian.pegawai.index') }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm h-100 clickable-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label mb-1">Tenaga Kependidikan</p>
                                <h3 class="stat-value text-success mb-0">{{ number_format($totalPegawai) }}</h3>
                                <small class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $pegawaiAktif }} aktif</small>
                            </div>
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Unit Kerja -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('kepegawaian.unit-kerja.index') }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm h-100 clickable-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label mb-1">Unit Kerja</p>
                                <h3 class="stat-value text-info mb-0">{{ number_format($totalUnitKerja) }}</h3>
                                <small class="text-info"><i class="bi bi-building me-1"></i>{{ $unitKerjaAktif }} aktif</small>
                            </div>
                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-building"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Total SDM Aktif -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">Total SDM Aktif</p>
                            <h3 class="stat-value text-warning mb-0">{{ number_format($totalAktif) }}</h3>
                            <small class="text-muted">Dosen + Tendik</small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-person-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Presensi Hari Ini & Perlu Perhatian -->
    <div class="row g-3 mb-4">
        <!-- Presensi Hari Ini -->
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-calendar-check me-2 text-success"></i>Presensi Hari Ini</h6>
                    <a href="{{ route('kepegawaian.presensi.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 fw-bold text-success">{{ $presensiStats['persentase_hadir'] }}%</div>
                        <small class="text-muted">Tingkat Kehadiran</small>
                    </div>
                    <div class="progress progress-custom mb-3">
                        <div class="progress-bar bg-success" style="width: {{ $presensiStats['persentase_hadir'] }}%"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="fw-bold text-success">{{ $presensiStats['hadir'] }}</div>
                            <small class="text-muted">Hadir</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-warning">{{ $presensiStats['terlambat'] }}</div>
                            <small class="text-muted">Terlambat</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-danger">{{ $presensiStats['belum_hadir'] }}</div>
                            <small class="text-muted">Belum</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perlu Perhatian -->
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bell me-2 text-warning"></i>Perlu Persetujuan</h6>
                </div>
                <div class="card-body">
                    @php $totalPending = $pendingStats['cuti_pending'] + $pendingStats['mutasi_pending'] + $pendingStats['kgb_pending'] + $pendingStats['pangkat_pending']; @endphp
                    
                    @if($totalPending > 0)
                    <a href="{{ route('kepegawaian.cuti.index', ['status' => 'diajukan']) }}" class="text-decoration-none">
                        <div class="summary-box warning mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-dark"><i class="bi bi-calendar-x me-2"></i>Pengajuan Cuti</span>
                                <span class="badge bg-warning text-dark">{{ $pendingStats['cuti_pending'] }}</span>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('kepegawaian.penugasan.index', ['status' => 'diajukan']) }}" class="text-decoration-none">
                        <div class="summary-box info mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-dark"><i class="bi bi-arrow-left-right me-2"></i>Mutasi/Penugasan</span>
                                <span class="badge bg-info">{{ $pendingStats['mutasi_pending'] }}</span>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('kepegawaian.kgb.index', ['status' => 'diajukan']) }}" class="text-decoration-none">
                        <div class="summary-box primary mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-dark"><i class="bi bi-cash-stack me-2"></i>KGB</span>
                                <span class="badge bg-primary">{{ $pendingStats['kgb_pending'] }}</span>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('kepegawaian.kenaikan-pangkat.index', ['status' => 'diajukan']) }}" class="text-decoration-none">
                        <div class="summary-box success">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-dark"><i class="bi bi-graph-up-arrow me-2"></i>Kenaikan Pangkat</span>
                                <span class="badge bg-success">{{ $pendingStats['pangkat_pending'] }}</span>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle text-success fs-1"></i>
                        <p class="mb-0 mt-2">Tidak ada pengajuan pending</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistik Cuti -->
        <div class="col-xl-4 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-calendar-minus me-2 text-info"></i>Statistik Cuti</h6>
                    <a href="{{ route('kepegawaian.cuti.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <div class="h4 mb-0 text-primary">{{ $cutiStats['sedang_cuti'] }}</div>
                                <small class="text-muted">Sedang Cuti</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <div class="h4 mb-0 text-warning">{{ $cutiStats['diajukan'] }}</div>
                                <small class="text-muted">Menunggu</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Total Pengajuan</span>
                        <span class="fw-semibold">{{ $cutiStats['total_pengajuan'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Disetujui</span>
                        <span class="text-success fw-semibold">{{ $cutiStats['disetujui'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Ditolak</span>
                        <span class="text-danger fw-semibold">{{ $cutiStats['ditolak'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Distribusi Pendidikan Dosen -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Distribusi Pendidikan Dosen</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartPendidikan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Unit Kerja -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Pegawai per Unit Kerja</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartUnitKerja"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu & Info Row -->
    <div class="row g-3 mb-4">
        <!-- Quick Menu -->
        <div class="col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Menu Manajemen SDM</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <a href="{{ route('dosen.index') }}" class="text-decoration-none">
                                <div class="card menu-card h-100 border-0 shadow-sm text-center py-4">
                                    <i class="bi bi-person-badge text-primary" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-0">Data Dosen</h6>
                                    <small class="text-muted">{{ $totalDosen }} orang</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('kepegawaian.pegawai.index') }}" class="text-decoration-none">
                                <div class="card menu-card h-100 border-0 shadow-sm text-center py-4">
                                    <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-0">Data Tendik</h6>
                                    <small class="text-muted">{{ $totalPegawai }} orang</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('kepegawaian.cuti.index') }}" class="text-decoration-none">
                                <div class="card menu-card h-100 border-0 shadow-sm text-center py-4 position-relative">
                                    <i class="bi bi-calendar-minus text-warning" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-0">Cuti Pegawai</h6>
                                    <small class="text-muted">Kelola cuti</small>
                                    @if($pendingStats['cuti_pending'] > 0)
                                    <span class="badge bg-danger badge-notification">{{ $pendingStats['cuti_pending'] }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('kepegawaian.presensi.index') }}" class="text-decoration-none">
                                <div class="card menu-card h-100 border-0 shadow-sm text-center py-4">
                                    <i class="bi bi-calendar-check text-info" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-0">Presensi</h6>
                                    <small class="text-muted">Kehadiran</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('kepegawaian.penugasan.index') }}" class="text-decoration-none">
                                <div class="card menu-card h-100 border-0 shadow-sm text-center py-4 position-relative">
                                    <i class="bi bi-arrow-left-right text-primary" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-0">Mutasi</h6>
                                    <small class="text-muted">Penugasan</small>
                                    @if($pendingStats['mutasi_pending'] > 0)
                                    <span class="badge bg-danger badge-notification">{{ $pendingStats['mutasi_pending'] }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('kepegawaian.kgb.index') }}" class="text-decoration-none">
                                <div class="card menu-card h-100 border-0 shadow-sm text-center py-4 position-relative">
                                    <i class="bi bi-cash-stack text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-0">KGB</h6>
                                    <small class="text-muted">{{ $kgbBulanIni }} bulan ini</small>
                                    @if($pendingStats['kgb_pending'] > 0)
                                    <span class="badge bg-danger badge-notification">{{ $pendingStats['kgb_pending'] }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('kepegawaian.kenaikan-pangkat.index') }}" class="text-decoration-none">
                                <div class="card menu-card h-100 border-0 shadow-sm text-center py-4 position-relative">
                                    <i class="bi bi-graph-up-arrow text-info" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-0">Pangkat</h6>
                                    <small class="text-muted">{{ $pangkatBulanIni }} bulan ini</small>
                                    @if($pendingStats['pangkat_pending'] > 0)
                                    <span class="badge bg-danger badge-notification">{{ $pendingStats['pangkat_pending'] }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('kepegawaian.pensiun.index') }}" class="text-decoration-none">
                                <div class="card menu-card h-100 border-0 shadow-sm text-center py-4">
                                    <i class="bi bi-person-dash text-danger" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-0">Pensiun</h6>
                                    <small class="text-muted">Data pensiun</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-xl-4">
            <!-- Ulang Tahun Bulan Ini -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-balloon me-2 text-danger"></i>Ulang Tahun Bulan {{ now()->translatedFormat('F') }}</h6>
                </div>
                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                    @forelse($ulangTahunBulanIni as $p)
                    <div class="birthday-item d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="fw-semibold">{{ $p->nama }}</div>
                            <small class="text-muted">
                                <span class="badge bg-{{ $p->jenis == 'Dosen' ? 'primary' : 'success' }} badge-sm">{{ $p->jenis }}</span>
                                {{ \Carbon\Carbon::parse($p->tanggal_lahir)->format('d M') }}
                            </small>
                        </div>
                        <i class="bi bi-gift text-danger"></i>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-balloon"></i>
                        <p class="mb-0 small">Tidak ada ulang tahun bulan ini</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Akan Pensiun -->
            @if($akanPensiun->isNotEmpty())
            <div class="card shadow-sm alert-card border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="mb-0 text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Akan Pensiun (12 Bln)</h6>
                </div>
                <div class="card-body">
                    @foreach($akanPensiun as $p)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="fw-semibold small">{{ $p->nama }}</div>
                            <small class="text-muted">{{ $p->jenis }} - {{ $p->nidn ?? $p->nip }}</small>
                        </div>
                        <small class="text-warning">
                            {{ \Carbon\Carbon::parse($p->tanggal_lahir)->addYears(60)->format('M Y') }}
                        </small>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-3">
        <!-- Pegawai Terbaru -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pegawai Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Unit/Prodi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pegawaiBaru as $p)
                                <tr class="clickable-row" onclick="window.location='{{ isset($p->nidn) ? route('dosen.show', $p->hashid) : route('kepegawaian.pegawai.show', $p->hashid) }}'">
                                    <td>
                                        <strong>{{ $p->nama }}</strong>
                                        <br><small class="text-muted">{{ $p->nip ?? $p->nidn ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if(isset($p->nidn))
                                        <span class="badge bg-primary">Dosen</span>
                                        @else
                                        <span class="badge bg-success">Tendik</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $p->unitKerja->nama ?? $p->programStudi->nama ?? '-' }}</small></td>
                                    <td>
                                        <span class="badge bg-{{ $p->status == 'Aktif' ? 'success' : 'secondary' }}">{{ $p->status }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekap Status & Jenis -->
        <div class="col-xl-6">
            <div class="row g-3">
                <!-- Rekap Jenis -->
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Rekap Jenis</h6>
                        </div>
                        <div class="card-body">
                            @foreach($rekapJenis as $jenis => $jumlah)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $jenis }}</span>
                                <span class="badge bg-primary rounded-pill">{{ $jumlah }}</span>
                            </div>
                            @endforeach
                            <hr>
                            <div class="d-flex justify-content-between align-items-center fw-bold">
                                <span>Total</span>
                                <span class="badge bg-dark rounded-pill">{{ $totalDosen + $totalPegawai }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Rekap Status -->
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Rekap Status</h6>
                        </div>
                        <div class="card-body">
                            @foreach($rekapStatus as $status => $jumlah)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $status }}</span>
                                <span class="badge bg-{{ $status == 'Aktif' ? 'success' : ($status == 'Cuti' ? 'warning' : 'secondary') }} rounded-pill">{{ $jumlah }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk charts
    const distribusiPendidikan = @json($distribusiPendidikan);
    const distribusiUnitKerja = @json($distribusiUnitKerja);

    // Chart Pendidikan (Doughnut)
    const pendidikanColors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe', '#43e97b'];
    new Chart(document.getElementById('chartPendidikan'), {
        type: 'doughnut',
        data: {
            labels: distribusiPendidikan.map(d => d.pendidikan_terakhir || 'Lainnya'),
            datasets: [{
                data: distribusiPendidikan.map(d => d.jumlah),
                backgroundColor: pendidikanColors,
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { padding: 15 }
                }
            },
            cutout: '60%',
        }
    });

    // Chart Unit Kerja (Horizontal Bar)
    new Chart(document.getElementById('chartUnitKerja'), {
        type: 'bar',
        data: {
            labels: distribusiUnitKerja.map(d => d.nama.length > 20 ? d.nama.substring(0, 20) + '...' : d.nama),
            datasets: [{
                label: 'Jumlah Pegawai',
                data: distribusiUnitKerja.map(d => d.pegawai_count),
                backgroundColor: '#667eea',
                borderRadius: 4,
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
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush
