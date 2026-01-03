@extends('layouts.app')

@section('title', 'Dashboard Keuangan')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
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
    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0 !important;
    }
    .summary-box {
        padding: 1.25rem;
        border-radius: 10px;
        background: #f8f9fa;
        border-left: 4px solid;
    }
    .summary-box.success { border-color: #28a745; }
    .summary-box.danger { border-color: #dc3545; }
    .summary-box.warning { border-color: #ffc107; }
    .summary-box.info { border-color: #17a2b8; }
    .summary-box.primary { border-color: #007bff; }
    
    .table-transactions th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .badge-status {
        padding: 0.4rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }
    .progress-custom {
        height: 10px;
        border-radius: 5px;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .quick-action-btn {
        padding: 1rem;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .quick-action-btn:hover {
        transform: scale(1.02);
    }
    
    /* Clickable Elements Styles */
    .clickable-card {
        text-decoration: none;
        display: block;
        color: inherit;
    }
    .clickable-card .stat-card {
        cursor: pointer;
    }
    .clickable-item {
        transition: all 0.2s ease;
        padding: 0.5rem 0.75rem;
        margin: -0.5rem -0.75rem;
        border-radius: 6px;
    }
    .clickable-item:hover {
        background-color: rgba(102, 126, 234, 0.1);
        transform: translateX(3px);
    }
    .clickable-row {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .clickable-row:hover {
        background-color: rgba(102, 126, 234, 0.08) !important;
    }
    .summary-box.clickable-item:hover {
        transform: translateX(3px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard Keuangan</h1>
            <p class="text-muted mb-0">Ringkasan informasi keuangan akademik</p>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select" id="filterTahunAkademik" onchange="filterByTahunAkademik(this.value)">
                <option value="">Semua Tahun Akademik</option>
                @foreach($tahunAkademiks as $ta)
                    <option value="{{ $ta->hashid }}" {{ $tahunAkademikId == $ta->id ? 'selected' : '' }}>
                        {{ $ta->tahun }} {{ $ta->semester }}
                        @if($ta->is_aktif) (Aktif) @endif
                    </option>
                @endforeach
            </select>
            <a href="{{ route('tagihan.index') }}" class="btn btn-primary">
                <i class="bi bi-receipt me-1"></i> Kelola Tagihan
            </a>
        </div>
    </div>

    <!-- Statistik Utama Keuangan -->
    <div class="row g-3 mb-4">
        <!-- Total Tagihan -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('tagihan.index') }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm h-100 clickable-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label mb-1">Total Tagihan</p>
                                <h3 class="stat-value text-primary mb-0">Rp {{ number_format($tagihanStats['total_nominal'], 0, ',', '.') }}</h3>
                                <small class="text-muted">{{ number_format($tagihanStats['total']) }} tagihan</small>
                            </div>
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-receipt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Total Terbayar -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('tagihan.index', ['status' => 'Lunas']) }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm h-100 clickable-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label mb-1">Total Terbayar</p>
                                <h3 class="stat-value text-success mb-0">Rp {{ number_format($tagihanStats['total_terbayar'], 0, ',', '.') }}</h3>
                                <small class="text-muted">{{ number_format($tagihanStats['lunas']) }} lunas</small>
                            </div>
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Total Tunggakan -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('tagihan.index', ['status' => 'Belum Bayar']) }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm h-100 clickable-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label mb-1">Total Tunggakan</p>
                                <h3 class="stat-value text-danger mb-0">Rp {{ number_format($tagihanStats['total_tunggakan'], 0, ',', '.') }}</h3>
                                <small class="text-muted">{{ number_format($tagihanStats['belum_bayar'] + $tagihanStats['cicilan']) }} tagihan</small>
                            </div>
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Pendapatan Bulan Ini -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('transaksi-pembayaran.index', ['status' => 'Verified']) }}" class="text-decoration-none">
                <div class="card stat-card shadow-sm h-100 clickable-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label mb-1">Pendapatan Bulan Ini</p>
                                <h3 class="stat-value text-info mb-0">Rp {{ number_format($transaksiStats['total_bulan_ini'], 0, ',', '.') }}</h3>
                                <small class="text-muted">Hari ini: Rp {{ number_format($transaksiStats['total_hari_ini'], 0, ',', '.') }}</small>
                            </div>
                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Progress Pembayaran -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">Progress Pembayaran</h6>
                    @php
                        $persentaseTerbayar = $tagihanStats['total_nominal'] > 0 
                            ? ($tagihanStats['total_terbayar'] / $tagihanStats['total_nominal']) * 100 
                            : 0;
                    @endphp
                    <div class="progress progress-custom mb-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persentaseTerbayar }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Terbayar {{ number_format($persentaseTerbayar, 1) }}%</span>
                        <span class="text-muted small">Sisa {{ number_format(100 - $persentaseTerbayar, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Detail -->
    <div class="row g-3 mb-4">
        <!-- Statistik Transaksi -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Transaksi</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('transaksi-pembayaran.index') }}" class="d-flex justify-content-between mb-2 text-decoration-none text-dark clickable-item">
                        <span>Total Transaksi</span>
                        <strong>{{ number_format($transaksiStats['total']) }}</strong>
                    </a>
                    <a href="{{ route('transaksi-pembayaran.index', ['status' => 'Pending']) }}" class="d-flex justify-content-between mb-2 text-decoration-none text-dark clickable-item">
                        <span><span class="badge bg-warning">Pending</span></span>
                        <strong>{{ number_format($transaksiStats['pending']) }}</strong>
                    </a>
                    <a href="{{ route('transaksi-pembayaran.index', ['status' => 'Verified']) }}" class="d-flex justify-content-between mb-2 text-decoration-none text-dark clickable-item">
                        <span><span class="badge bg-success">Verified</span></span>
                        <strong>{{ number_format($transaksiStats['verified']) }}</strong>
                    </a>
                    <a href="{{ route('transaksi-pembayaran.index', ['status' => 'Rejected']) }}" class="d-flex justify-content-between text-decoration-none text-dark clickable-item">
                        <span><span class="badge bg-danger">Rejected</span></span>
                        <strong>{{ number_format($transaksiStats['rejected']) }}</strong>
                    </a>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('transaksi-pembayaran.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-arrow-right me-1"></i> Kelola Transaksi
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistik Beasiswa -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-award me-2"></i>Beasiswa</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('beasiswa.index') }}" class="d-flex justify-content-between mb-2 text-decoration-none text-dark clickable-item">
                        <span>Program Beasiswa</span>
                        <strong>{{ number_format($beasiswaStats['total_beasiswa']) }}</strong>
                    </a>
                    <a href="{{ route('beasiswa.penerima.index', ['status' => 'Disetujui']) }}" class="d-flex justify-content-between mb-2 text-decoration-none text-dark clickable-item">
                        <span>Penerima Aktif</span>
                        <strong class="text-success">{{ number_format($beasiswaStats['total_penerima']) }}</strong>
                    </a>
                    <a href="{{ route('beasiswa.penerima.index', ['status' => 'Diajukan']) }}" class="d-flex justify-content-between mb-2 text-decoration-none text-dark clickable-item">
                        <span>Pengajuan Pending</span>
                        <strong class="text-warning">{{ number_format($beasiswaStats['pengajuan_pending']) }}</strong>
                    </a>
                    <div class="d-flex justify-content-between">
                        <span>Total Nilai</span>
                        <strong>Rp {{ number_format($beasiswaStats['total_nilai_beasiswa'], 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('beasiswa.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-arrow-right me-1"></i> Kelola Beasiswa
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistik PMB -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-person-plus me-2"></i>Pembayaran PMB</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('pmb.pembayaran.index', ['jenis_pembayaran' => 'pendaftaran']) }}" class="d-flex justify-content-between mb-2 text-decoration-none text-dark clickable-item">
                        <span>Pendaftaran</span>
                        <strong>{{ number_format($pmbStats['total_pendaftaran']) }}</strong>
                    </a>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pendapatan Pendaftaran</span>
                        <strong class="text-success">Rp {{ number_format($pmbStats['pendapatan_pendaftaran'], 0, ',', '.') }}</strong>
                    </div>
                    <a href="{{ route('pmb.pembayaran.index', ['jenis_pembayaran' => 'daftar_ulang']) }}" class="d-flex justify-content-between mb-2 text-decoration-none text-dark clickable-item">
                        <span>Daftar Ulang</span>
                        <strong>{{ number_format($pmbStats['total_daftar_ulang']) }}</strong>
                    </a>
                    <div class="d-flex justify-content-between">
                        <span>Pendapatan D.U</span>
                        <strong class="text-success">Rp {{ number_format($pmbStats['pendapatan_daftar_ulang'], 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('pmb.pembayaran.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-arrow-right me-1"></i> Kelola PMB
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistik Alert -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bell me-2"></i>Perlu Perhatian</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('tagihan.index', ['jatuh_tempo' => 'overdue']) }}" class="text-decoration-none">
                        <div class="summary-box danger mb-2 clickable-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-dark">Jatuh Tempo</span>
                                <strong class="text-danger fs-5">{{ number_format($tagihanStats['jatuh_tempo']) }}</strong>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('transaksi-pembayaran.index', ['status' => 'Pending']) }}" class="text-decoration-none">
                        <div class="summary-box warning mb-2 clickable-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-dark">Transaksi Pending</span>
                                <strong class="text-warning fs-5">{{ number_format($transaksiStats['pending']) }}</strong>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('cicilan.index', ['status' => 'Aktif']) }}" class="text-decoration-none">
                        <div class="summary-box info clickable-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-dark">Cicilan Aktif</span>
                                <strong class="text-info fs-5">{{ number_format($cicilanStats['total_cicilan_aktif']) }}</strong>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row g-3 mb-4">
        <!-- Grafik Pendapatan Bulanan -->
        <div class="col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Pendapatan 12 Bulan Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartPendapatanBulanan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Status Tagihan -->
        <div class="col-xl-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Status Tagihan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartStatusTagihan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Grafik Pendapatan Harian -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Pendapatan 30 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartPendapatanHarian"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Metode Pembayaran -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Metode Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartMetodePembayaran"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Grafik Tagihan per Jenis -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-tags me-2"></i>Tagihan per Jenis</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartTagihanJenis"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Tunggakan per Prodi -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Tunggakan per Program Studi</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartTunggakanProdi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="row g-3">
        <!-- Transaksi Terbaru -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Transaksi Terbaru</h6>
                    <a href="{{ route('transaksi-pembayaran.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-transactions mb-0">
                            <thead>
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Mahasiswa</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiTerbaru as $trx)
                                <tr class="clickable-row" onclick="window.location='{{ route('transaksi-pembayaran.show', $trx->hashid) }}'">
                                    <td>
                                        <small class="fw-semibold">{{ $trx->no_transaksi }}</small><br>
                                        <small class="text-muted">{{ $trx->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $trx->tagihan->mahasiswa->nama ?? '-' }}</small><br>
                                        <small class="text-muted">{{ $trx->tagihan->mahasiswa->nim ?? '-' }}</small>
                                    </td>
                                    <td><strong>Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</strong></td>
                                    <td>
                                        @if($trx->status == 'Verified')
                                            <span class="badge bg-success badge-status">Verified</span>
                                        @elseif($trx->status == 'Pending')
                                            <span class="badge bg-warning badge-status">Pending</span>
                                        @else
                                            <span class="badge bg-danger badge-status">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada transaksi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tagihan Jatuh Tempo -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Tagihan Jatuh Tempo</h6>
                    <a href="{{ route('tagihan.index', ['status' => 'Belum Bayar']) }}" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-transactions mb-0">
                            <thead>
                                <tr>
                                    <th>No. Tagihan</th>
                                    <th>Mahasiswa</th>
                                    <th>Sisa Tagihan</th>
                                    <th>Jatuh Tempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tagihanJatuhTempo as $tagihan)
                                <tr class="clickable-row" onclick="window.location='{{ route('tagihan.show', $tagihan->hashid) }}'">
                                    <td>
                                        <small class="fw-semibold">{{ $tagihan->no_tagihan }}</small><br>
                                        <small class="text-muted">{{ $tagihan->jenis_tagihan }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $tagihan->mahasiswa->nama ?? '-' }}</small><br>
                                        <small class="text-muted">{{ $tagihan->mahasiswa->programStudi->nama ?? '-' }}</small>
                                    </td>
                                    <td><strong class="text-danger">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</strong></td>
                                    <td>
                                        <span class="badge bg-danger badge-status">
                                            {{ $tagihan->tanggal_jatuh_tempo ? $tagihan->tanggal_jatuh_tempo->diffForHumans() : '-' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-check-circle text-success fs-4"></i><br>
                                        Tidak ada tagihan jatuh tempo
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    // Data dari PHP
    const pendapatanBulanan = @json($pendapatanBulanan);
    const pendapatanHarian = @json($pendapatanHarian);
    const statusTagihan = @json($statusTagihan);
    const metodePembayaran = @json($metodePembayaran);
    const tagihanPerJenis = @json($tagihanPerJenis);
    const tunggakanPerProdi = @json($tunggakanPerProdi);

    // Format Rupiah
    function formatRupiah(value) {
        return 'Rp ' + value.toLocaleString('id-ID');
    }

    // Chart 1: Pendapatan Bulanan (Line Chart)
    new Chart(document.getElementById('chartPendapatanBulanan'), {
        type: 'line',
        data: {
            labels: pendapatanBulanan.map(d => d.bulan),
            datasets: [{
                label: 'Pendapatan',
                data: pendapatanBulanan.map(d => d.pendapatan),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => formatRupiah(ctx.raw)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => 'Rp ' + (value / 1000000) + 'jt'
                    }
                }
            }
        }
    });

    // Chart 2: Status Tagihan (Doughnut)
    new Chart(document.getElementById('chartStatusTagihan'), {
        type: 'doughnut',
        data: {
            labels: statusTagihan.map(d => d.status),
            datasets: [{
                data: statusTagihan.map(d => d.jumlah),
                backgroundColor: statusTagihan.map(d => d.color),
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20 }
                }
            },
            cutout: '60%',
        }
    });

    // Chart 3: Pendapatan Harian (Bar Chart)
    new Chart(document.getElementById('chartPendapatanHarian'), {
        type: 'bar',
        data: {
            labels: pendapatanHarian.map(d => d.tanggal),
            datasets: [{
                label: 'Pendapatan',
                data: pendapatanHarian.map(d => d.pendapatan),
                backgroundColor: '#28a745',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => formatRupiah(ctx.raw)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => 'Rp ' + (value / 1000000) + 'jt'
                    }
                }
            }
        }
    });

    // Chart 4: Metode Pembayaran (Pie)
    const metodColors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'];
    new Chart(document.getElementById('chartMetodePembayaran'), {
        type: 'pie',
        data: {
            labels: metodePembayaran.map(d => d.metode_pembayaran || 'Lainnya'),
            datasets: [{
                data: metodePembayaran.map(d => d.jumlah),
                backgroundColor: metodColors,
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
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const item = metodePembayaran[ctx.dataIndex];
                            return `${ctx.label}: ${ctx.raw} transaksi (${formatRupiah(parseFloat(item.total))})`;
                        }
                    }
                }
            }
        }
    });

    // Chart 5: Tagihan per Jenis (Horizontal Bar)
    new Chart(document.getElementById('chartTagihanJenis'), {
        type: 'bar',
        data: {
            labels: tagihanPerJenis.map(d => d.jenis_tagihan || 'Lainnya'),
            datasets: [{
                label: 'Total Tagihan',
                data: tagihanPerJenis.map(d => parseFloat(d.total)),
                backgroundColor: '#17a2b8',
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => formatRupiah(ctx.raw)
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => 'Rp ' + (value / 1000000) + 'jt'
                    }
                }
            }
        }
    });

    // Chart 6: Tunggakan per Prodi (Horizontal Bar)
    new Chart(document.getElementById('chartTunggakanProdi'), {
        type: 'bar',
        data: {
            labels: tunggakanPerProdi.map(d => d.prodi),
            datasets: [{
                label: 'Total Tunggakan',
                data: tunggakanPerProdi.map(d => parseFloat(d.total_tunggakan)),
                backgroundColor: '#dc3545',
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => formatRupiah(ctx.raw)
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => 'Rp ' + (value / 1000000) + 'jt'
                    }
                }
            }
        }
    });

    // Filter Tahun Akademik
    function filterByTahunAkademik(tahunAkademikId) {
        const url = new URL(window.location.href);
        if (tahunAkademikId) {
            url.searchParams.set('tahun_akademik_id', tahunAkademikId);
        } else {
            url.searchParams.delete('tahun_akademik_id');
        }
        window.location.href = url.toString();
    }
</script>
@endpush
