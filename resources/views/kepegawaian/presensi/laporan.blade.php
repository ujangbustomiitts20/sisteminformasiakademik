@extends('layouts.app')

@section('title', 'Laporan Presensi')

@section('content')
<div class="page-title">
    <h4>Laporan Presensi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.presensi.index') }}">Presensi</a></li>
            <li class="breadcrumb-item active">Laporan</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-bar-chart me-2"></i>Laporan Presensi Bulanan</span>
        <div>
            <a href="{{ route('kepegawaian.presensi.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.presensi.laporan') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="bulan" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>Tampilkan
                    </button>
                </div>
            </div>
        </form>

        @if($rekap->count() > 0)
        <!-- Chart -->
        <div class="mb-4">
            <canvas id="chartKehadiran" height="100"></canvas>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Total Pegawai</h6>
                        <h3 class="mb-0 text-primary">{{ $rekap->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Rata-rata Kehadiran</h6>
                        <h3 class="mb-0 text-success">{{ number_format($rekap->avg('persentase_kehadiran'), 1) }}%</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Total Keterlambatan</h6>
                        <h3 class="mb-0 text-warning">{{ $rekap->sum('terlambat') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Total Alpha</h6>
                        <h3 class="mb-0 text-danger">{{ $rekap->sum('alpha') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Terlambat</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Cuti</th>
                        <th class="text-center">Alpha</th>
                        <th class="text-center">% Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $index => $r)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($r->dosen)
                            <strong>{{ $r->dosen->nama_lengkap ?? $r->dosen->nama }}</strong>
                            <br><small class="text-muted">Dosen</small>
                            @elseif($r->pegawai)
                            <strong>{{ $r->pegawai->nama }}</strong>
                            <br><small class="text-muted">Tendik</small>
                            @endif
                        </td>
                        <td class="text-center"><span class="badge bg-success">{{ $r->hadir }}</span></td>
                        <td class="text-center"><span class="badge bg-warning text-dark">{{ $r->terlambat }}</span></td>
                        <td class="text-center"><span class="badge bg-info">{{ $r->sakit }}</span></td>
                        <td class="text-center"><span class="badge bg-primary">{{ $r->izin }}</span></td>
                        <td class="text-center"><span class="badge bg-secondary">{{ $r->cuti }}</span></td>
                        <td class="text-center"><span class="badge bg-danger">{{ $r->alpha }}</span></td>
                        <td class="text-center">
                            @php
                                $persen = $r->persentase_kehadiran;
                                $class = $persen >= 90 ? 'success' : ($persen >= 75 ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-{{ $class }}">{{ number_format($persen, 1) }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-bar-chart text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2">Belum ada data rekap untuk periode ini</p>
            <a href="{{ route('kepegawaian.presensi.rekap') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Generate Rekap
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if(isset($chartData) && count($chartData['labels']) > 0)
const ctx = document.getElementById('chartKehadiran').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartData['labels']),
        datasets: [{
            label: 'Persentase Kehadiran (%)',
            data: @json($chartData['kehadiran']),
            backgroundColor: 'rgba(40, 167, 69, 0.7)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Top 10 Pegawai dengan Kehadiran Tertinggi'
            }
        }
    }
});
@endif
</script>
@endpush
