@extends('layouts.app')

@section('title', 'Laporan Wisuda & Lulusan')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Wisuda & Lulusan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">Wisuda & Lulusan</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('laporan.wisuda', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.wisuda') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Periode Wisuda</label>
                <select name="periode_wisuda_id" class="form-select">
                    <option value="">Semua Periode</option>
                    @foreach($periodeWisuda as $periode)
                    <option value="{{ $periode->id }}" {{ request('periode_wisuda_id') == $periode->id ? 'selected' : '' }}>
                        {{ $periode->nama_periode ?? 'Periode ' . ($periode->tanggal_wisuda ? $periode->tanggal_wisuda->format('Y') : '-') }} 
                        ({{ $periode->tanggal_wisuda ? $periode->tanggal_wisuda->format('d/m/Y') : '-' }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('laporan.wisuda') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Statistik -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['cum_laude'] ?? 0 }}</h3>
                <small>Cum Laude</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['sangat_memuaskan'] ?? 0 }}</h3>
                <small>Sangat Memuaskan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['memuaskan'] ?? 0 }}</h3>
                <small>Memuaskan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['total_lulusan'] ?? 0 }}</h3>
                <small>Total Lulusan</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h2 class="text-primary">{{ number_format($summary['ipk_rata'] ?? 0, 2) }}</h2>
                <small>Rata-rata IPK</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h2 class="text-info">{{ number_format(($summary['masa_studi_rata'] ?? 0) / 12, 1) }} Tahun</h2>
                <small>Rata-rata Masa Studi</small>
            </div>
        </div>
    </div>
</div>

<!-- Hasil -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-mortarboard me-2"></i>Data Lulusan 
        @if($periodeAktif)
            - {{ $periodeAktif->nama_periode ?? 'Periode ' . ($periodeAktif->tanggal_wisuda ? $periodeAktif->tanggal_wisuda->format('Y') : '') }}
        @endif
        ({{ $yudisium->count() }} orang)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>IPK</th>
                        <th>Total SKS</th>
                        <th>Predikat</th>
                        <th>Tanggal Lulus</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($yudisium as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $data->mahasiswa->nim ?? '-' }}</code></td>
                        <td>{{ $data->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $data->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td><strong>{{ number_format($data->ipk_akhir ?? 0, 2) }}</strong></td>
                        <td>{{ $data->total_sks ?? 0 }} SKS</td>
                        <td>
                            <span class="badge bg-{{ $data->predikat == 'Cum Laude' ? 'success' : ($data->predikat == 'Sangat Memuaskan' ? 'primary' : 'info') }}">
                                {{ $data->predikat }}
                            </span>
                        </td>
                        <td>{{ $data->tanggal_lulus ? $data->tanggal_lulus->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">Tidak ada data lulusan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
