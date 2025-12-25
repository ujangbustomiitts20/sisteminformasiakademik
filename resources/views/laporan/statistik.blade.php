@extends('layouts.app')

@section('title', 'Statistik Akademik')

@section('content')
<div class="page-title">
    <h4>Statistik Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
            <li class="breadcrumb-item active">Statistik</li>
        </ol>
    </nav>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Mahasiswa Aktif</h6>
                <h3>{{ $mahasiswaPerStatus['Aktif'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6>Mahasiswa Cuti</h6>
                <h3>{{ $mahasiswaPerStatus['Cuti'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Mahasiswa Lulus</h6>
                <h3>{{ $mahasiswaPerStatus['Lulus'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <h6>Drop Out</h6>
                <h3>{{ $mahasiswaPerStatus['DO'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Mahasiswa per Program Studi -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Mahasiswa Aktif per Program Studi
            </div>
            <div class="card-body">
                @if($mahasiswaPerProdi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Program Studi</th>
                                <th class="text-end">Jumlah</th>
                                <th style="width: 40%">Chart</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $maxMhs = $mahasiswaPerProdi->max('total'); @endphp
                            @foreach($mahasiswaPerProdi as $data)
                            <tr>
                                <td>{{ $data->programStudi->nama ?? 'N/A' }}</td>
                                <td class="text-end"><strong>{{ $data->total }}</strong></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $maxMhs > 0 ? ($data->total / $maxMhs * 100) : 0 }}%">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th>Total</th>
                                <th class="text-end">{{ $mahasiswaPerProdi->sum('total') }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">Tidak ada data</p>
                @endif
            </div>
        </div>
    </div>

    <!-- IPK Rata-rata per Program Studi -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>IPK Rata-rata per Program Studi
            </div>
            <div class="card-body">
                @if($ipkPerProdi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Program Studi</th>
                                <th class="text-center">Jumlah Mhs</th>
                                <th class="text-center">IPK Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ipkPerProdi as $data)
                            <tr>
                                <td>{{ $data['prodi'] }}</td>
                                <td class="text-center">{{ $data['jumlah_mhs'] }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $data['ipk_rata'] >= 3.5 ? 'success' : ($data['ipk_rata'] >= 3.0 ? 'primary' : ($data['ipk_rata'] >= 2.5 ? 'warning' : 'danger')) }}" style="font-size: 0.9rem;">
                                        {{ number_format($data['ipk_rata'], 2) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">Tidak ada data</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Info -->
<div class="card">
    <div class="card-body">
        <h6><i class="bi bi-info-circle me-2"></i>Informasi</h6>
        <ul class="mb-0 text-muted">
            <li>Data berdasarkan Tahun Akademik: <strong>{{ $tahunAkademikAktif->tahun ?? '-' }} {{ $tahunAkademikAktif->semester ?? '' }}</strong></li>
            <li>IPK dihitung dari semua nilai yang sudah masuk</li>
            <li>Statistik diupdate secara realtime</li>
        </ul>
    </div>
</div>
@endsection
