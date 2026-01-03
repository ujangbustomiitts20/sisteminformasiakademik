@extends('layouts.app')

@section('title', 'Laporan Statistik Kelulusan')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Statistik Kelulusan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">Statistik Kelulusan</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('laporan.kelulusan', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.kelulusan') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Tahun Kelulusan</label>
                <select name="tahun" class="form-select">
                    <option value="">Tahun Ini ({{ date('Y') }})</option>
                    @foreach($tahunList as $thn)
                    <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('laporan.kelulusan') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['total_lulusan'] ?? 0 }}</h3>
                <small>Total Lulusan ({{ $tahunFilter }})</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['cum_laude'] ?? 0 }}</h3>
                <small>Cum Laude</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ number_format($summary['ipk_rata'] ?? 0, 2) }}</h3>
                <small>Rata-rata IPK</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>{{ number_format($summary['masa_studi_rata'] ?? 0, 1) }} Th</h3>
                <small>Rata-rata Masa Studi</small>
            </div>
        </div>
    </div>
</div>

<!-- Chart Statistik Kelulusan per Tahun -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>Trend Kelulusan per Tahun
            </div>
            <div class="card-body">
                <canvas id="kelulusanChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Distribusi Predikat
            </div>
            <div class="card-body">
                <canvas id="predikatChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Kelulusan per Tahun -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-table me-2"></i>Data Kelulusan per Tahun
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th class="text-center">Jumlah Lulusan</th>
                        <th class="text-center">Cum Laude</th>
                        <th class="text-center">Sangat Memuaskan</th>
                        <th class="text-center">Memuaskan</th>
                        <th class="text-center">Rata-rata IPK</th>
                        <th class="text-center">Rata-rata Masa Studi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelulusanPerTahun as $data)
                    <tr>
                        <td><strong>{{ $data['tahun'] }}</strong></td>
                        <td class="text-center">{{ $data['total'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $data['cumlaude'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $data['sangat_memuaskan'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $data['memuaskan'] }}</span>
                        </td>
                        <td class="text-center">{{ number_format($data['avg_ipk'], 2) }}</td>
                        <td class="text-center">{{ number_format($data['avg_masa_studi'], 1) }} tahun</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total/Rata-rata</th>
                        <th class="text-center">{{ $summary['total_lulusan'] ?? 0 }}</th>
                        <th class="text-center">{{ collect($kelulusanPerTahun)->sum('cumlaude') }}</th>
                        <th class="text-center">{{ collect($kelulusanPerTahun)->sum('sangat_memuaskan') }}</th>
                        <th class="text-center">{{ collect($kelulusanPerTahun)->sum('memuaskan') }}</th>
                        <th class="text-center">{{ number_format($summary['ipk_rata'] ?? 0, 2) }}</th>
                        <th class="text-center">{{ number_format($summary['masa_studi_rata'] ?? 0, 1) }} tahun</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Kelulusan per Program Studi -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-building me-2"></i>Kelulusan per Program Studi
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Program Studi</th>
                        <th class="text-center">Total Lulusan</th>
                        <th class="text-center">Cum Laude</th>
                        <th class="text-center">Sangat Memuaskan</th>
                        <th class="text-center">Memuaskan</th>
                        <th class="text-center">Rata-rata IPK</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelulusanPerProdi as $data)
                    <tr>
                        <td><strong>{{ $data['prodi'] }}</strong></td>
                        <td class="text-center">{{ $data['total'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $data['cumlaude'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $data['sangat_memuaskan'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $data['memuaskan'] }}</span>
                        </td>
                        <td class="text-center">{{ number_format($data['avg_ipk'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trend Kelulusan Chart
    const kelulusanCtx = document.getElementById('kelulusanChart').getContext('2d');
    new Chart(kelulusanCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($kelulusanPerTahun)->pluck('tahun')) !!},
            datasets: [{
                label: 'Jumlah Lulusan',
                data: {!! json_encode(collect($kelulusanPerTahun)->pluck('total')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Predikat Distribution Chart
    const predikatCtx = document.getElementById('predikatChart').getContext('2d');
    new Chart(predikatCtx, {
        type: 'doughnut',
        data: {
            labels: ['Cum Laude', 'Sangat Memuaskan', 'Memuaskan'],
            datasets: [{
                data: [
                    {{ collect($kelulusanPerTahun)->sum('cumlaude') }},
                    {{ collect($kelulusanPerTahun)->sum('sangat_memuaskan') }},
                    {{ collect($kelulusanPerTahun)->sum('memuaskan') }}
                ],
                backgroundColor: ['#28a745', '#007bff', '#17a2b8']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endpush
