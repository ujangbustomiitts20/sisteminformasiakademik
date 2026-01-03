@extends('layouts.app')

@section('title', 'Laporan Akademik')

@section('content')
<div class="page-title">
    <h4>Laporan Akademik Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Laporan</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- IPK per Angkatan -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Rata-rata IPK per Angkatan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Angkatan</th>
                                <th>Jumlah Mahasiswa</th>
                                <th>Rata-rata IPK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ipkPerAngkatan as $data)
                            <tr>
                                <td>{{ $data->angkatan }}</td>
                                <td>{{ $data->jumlah }}</td>
                                <td>
                                    <span class="badge bg-{{ $data->rata_ipk >= 3.0 ? 'success' : ($data->rata_ipk >= 2.0 ? 'warning' : 'danger') }}">
                                        {{ number_format($data->rata_ipk, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Mahasiswa -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Distribusi Status Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Jumlah</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalMahasiswa = $statusMahasiswa->sum('jumlah');
                            @endphp
                            @forelse($statusMahasiswa as $status)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $status->status == 'aktif' ? 'success' : ($status->status == 'cuti' ? 'warning' : ($status->status == 'lulus' ? 'info' : 'secondary')) }}">
                                        {{ ucfirst($status->status) }}
                                    </span>
                                </td>
                                <td>{{ $status->jumlah }}</td>
                                <td>{{ $totalMahasiswa > 0 ? number_format(($status->jumlah / $totalMahasiswa) * 100, 1) : 0 }}%</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kelulusan per Tahun -->
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Kelulusan 5 Tahun Terakhir</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($kelulusan as $data)
                    <div class="col-md-2 text-center mb-3">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h4 class="text-success">{{ $data->jumlah }}</h4>
                                <small class="text-muted">{{ $data->tahun }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">
                        Belum ada data kelulusan
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
