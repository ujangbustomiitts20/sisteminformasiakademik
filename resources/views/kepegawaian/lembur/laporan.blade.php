@extends('layouts.app')

@section('title', 'Laporan Lembur')

@section('content')
<div class="page-title">
    <h4>Laporan Lembur</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.lembur.index') }}">Lembur</a></li>
            <li class="breadcrumb-item active">Laporan</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.lembur.laporan') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Bulan Mulai</label>
                    <input type="month" name="bulan_mulai" class="form-control" value="{{ $filter['bulan_mulai'] ?? request('bulan_mulai', date('Y-01')) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Bulan Selesai</label>
                    <input type="month" name="bulan_selesai" class="form-control" value="{{ $filter['bulan_selesai'] ?? request('bulan_selesai', date('Y-m')) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua (Disetujui & Selesai)</option>
                        <option value="diajukan" {{ ($filter['status'] ?? request('status')) == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="disetujui" {{ ($filter['status'] ?? request('status')) == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ ($filter['status'] ?? request('status')) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="selesai" {{ ($filter['status'] ?? request('status')) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h6>Total Pegawai Lembur</h6>
                <h2>{{ $summary['total_pegawai'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h6>Total Jam Lembur</h6>
                <h2>{{ number_format($summary['total_jam'] ?? 0, 1) }} jam</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h6>Total Upah Lembur</h6>
                <h2>{{ format_rupiah($summary['total_upah'] ?? 0) }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Rekap Lembur per Pegawai</h5>
        <a href="{{ route('kepegawaian.lembur.laporan', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-excel me-1"></i> Export Excel
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Tipe</th>
                        <th>Jumlah Lembur</th>
                        <th>Total Jam</th>
                        <th>Total Upah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapPegawai as $i => $rekap)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $rekap->nama_pegawai }}</strong></td>
                            <td>{{ $rekap->tipe }}</td>
                            <td>{{ $rekap->jumlah_lembur }} kali</td>
                            <td>{{ number_format($rekap->total_jam, 1) }} jam</td>
                            <td class="fw-bold text-primary">{{ format_rupiah($rekap->total_upah) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada data lembur pada periode ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($rekapPegawai) > 0)
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3">Total</th>
                        <th>{{ $rekapPegawai->sum('jumlah_lembur') }} kali</th>
                        <th>{{ number_format($rekapPegawai->sum('total_jam'), 1) }} jam</th>
                        <th class="text-primary">{{ format_rupiah($rekapPegawai->sum('total_upah')) }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Grafik Lembur per Bulan</h5>
    </div>
    <div class="card-body">
        <canvas id="chartLembur" height="100"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartLembur').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels ?? []) !!},
        datasets: [{
            label: 'Total Jam',
            data: {!! json_encode($chartJam ?? []) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 1,
            yAxisID: 'y'
        }, {
            label: 'Total Upah (Juta)',
            data: {!! json_encode($chartUpah ?? []) !!},
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderColor: 'rgb(75, 192, 192)',
            borderWidth: 1,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Jam'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Upah (Juta Rp)'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});
</script>
@endpush
