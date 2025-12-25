@extends('layouts.app')

@section('title', 'Dashboard Keuangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Dashboard Keuangan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Keuangan</li>
                </ol>
            </nav>
        </div>
        <div>
            <span class="text-muted">Update terakhir: {{ now()->format('d M Y H:i') }}</span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Tagihan</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_tagihan'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-receipt display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Terbayar</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_terbayar'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-check-circle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Tunggakan</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_tunggakan'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-exclamation-triangle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Penerima Beasiswa</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_penerima_beasiswa']) }}</h4>
                        </div>
                        <i class="bi bi-award display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today & Month Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-calendar-day text-success fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pendapatan Hari Ini</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_transaksi_hari_ini'], 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-calendar-month text-primary fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pendapatan Bulan Ini</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_transaksi_bulan_ini'], 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-hourglass-split text-warning fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Menunggu Verifikasi</h6>
                            <h4 class="mb-0">{{ $stats['menunggu_verifikasi'] }} Transaksi</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Chart Pendapatan -->
        <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Tren Pendapatan 12 Bulan Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPendapatan" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Pembayaran per Metode -->
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Metode Pembayaran</h6>
                    <small class="text-muted">Bulan ini</small>
                </div>
                <div class="card-body py-2">
                    <div style="max-height: 150px;">
                        <canvas id="chartMetode" height="120"></canvas>
                    </div>
                    <div class="mt-2" style="font-size: 0.85rem;">
                        @foreach($pembayaranPerMetode as $metode)
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $metode->metode_pembayaran }}</span>
                            <span class="fw-bold">Rp {{ number_format($metode->total, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Tunggakan per Prodi -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Tunggakan per Program Studi</h5>
                    <a href="{{ route('laporan.tunggakan') }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Program Studi</th>
                                    <th class="text-center">Mahasiswa</th>
                                    <th class="text-end">Total Tunggakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tunggakanPerProdi as $item)
                                <tr>
                                    <td>{{ $item->prodi }}</td>
                                    <td class="text-center">{{ $item->jumlah_mahasiswa }}</td>
                                    <td class="text-end text-danger fw-bold">Rp {{ number_format($item->total_tunggakan, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Tidak ada tunggakan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Tunggakan -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-exclamation me-2"></i>Top 10 Mahasiswa Tunggakan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th class="text-end">Total Tunggakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topTunggakan as $item)
                                @php
                                    $mhs = \App\Models\Mahasiswa::with('programStudi')->find($item->mahasiswa_id);
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $mhs->nama ?? '-' }}</strong>
                                        <br><small class="text-muted">{{ $mhs->nim ?? '-' }} - {{ $mhs->programStudi->nama ?? '-' }}</small>
                                    </td>
                                    <td class="text-end text-danger fw-bold">Rp {{ number_format($item->total_tunggakan, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">Tidak ada tunggakan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Transaksi Terbaru</h5>
            <a href="{{ route('transaksi-pembayaran.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Mahasiswa</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTerbaru as $trx)
                        <tr>
                            <td><code>{{ $trx->no_transaksi }}</code></td>
                            <td>
                                <strong>{{ $trx->mahasiswa->nama ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $trx->mahasiswa->nim ?? '-' }}</small>
                            </td>
                            <td>{{ $trx->tanggal_bayar->format('d/m/Y') }}</td>
                            <td>{{ $trx->metode_pembayaran }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">{!! $trx->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Helper function format Rupiah
function formatRupiah(value) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
}

// Chart Pendapatan 12 Bulan Terakhir - Bar Chart
const ctxPendapatan = document.getElementById('chartPendapatan').getContext('2d');
const chartLabels = {!! json_encode($chartLabels) !!};
const chartData = {!! json_encode($chartData) !!};

// Hitung rata-rata untuk referensi
const avgPendapatan = chartData.reduce((a, b) => a + b, 0) / chartData.filter(v => v > 0).length || 0;

new Chart(ctxPendapatan, {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Pendapatan',
            data: chartData,
            backgroundColor: chartData.map(val => {
                if (val === 0) return 'rgba(200, 200, 200, 0.3)';
                if (val >= avgPendapatan * 1.5) return 'rgba(25, 135, 84, 0.9)';
                if (val >= avgPendapatan) return 'rgba(25, 135, 84, 0.7)';
                return 'rgba(25, 135, 84, 0.5)';
            }),
            borderColor: chartData.map(val => val === 0 ? 'rgba(200, 200, 200, 0.5)' : '#198754'),
            borderWidth: 1,
            borderRadius: 3,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2.5,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: { size: 12 },
                bodyFont: { size: 11 },
                padding: 10,
                displayColors: false,
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        if (context.raw === 0) return 'Belum ada pendapatan';
                        return 'Pendapatan: ' + formatRupiah(context.raw);
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    font: { size: 9 },
                    maxRotation: 45,
                    minRotation: 45
                }
            },
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: {
                    font: { size: 10 },
                    maxTicksLimit: 5,
                    callback: function(value) {
                        if (value >= 1000000) {
                            return 'Rp ' + (value / 1000000).toFixed(0) + ' Jt';
                        } else if (value >= 1000) {
                            return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                        }
                        return 'Rp ' + value;
                    }
                }
            }
        }
    }
});

// Chart Metode Pembayaran Bulan Ini
const ctxMetode = document.getElementById('chartMetode').getContext('2d');
const metodeLabels = {!! json_encode($pembayaranPerMetode->pluck('metode_pembayaran')) !!};
const metodeData = {!! json_encode($pembayaranPerMetode->pluck('total')->map(fn($v) => (int)$v)) !!};

new Chart(ctxMetode, {
    type: 'doughnut',
    data: {
        labels: metodeLabels,
        datasets: [{
            data: metodeData,
            backgroundColor: [
                '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d', '#0dcaf0', '#6610f2', '#fd7e14'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.5,
        cutout: '55%',
        plugins: {
            legend: { 
                position: 'bottom',
                labels: {
                    padding: 10,
                    usePointStyle: true,
                    font: { size: 10 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 10,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.raw / total) * 100).toFixed(1);
                        return context.label + ': ' + formatRupiah(context.raw) + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection
