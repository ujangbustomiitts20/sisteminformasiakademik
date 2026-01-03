@extends('layouts.app')

@section('title', 'Rekap Nilai')

@section('content')
<div class="page-title">
    <h4>Rekap Nilai - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Rekap Nilai</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Rata-rata IPK per Program Studi - {{ $tahunAktif->nama ?? 'Tahun Aktif' }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Program Studi</th>
                        <th class="text-center">Mahasiswa Aktif</th>
                        <th class="text-center">Rata-rata IPK</th>
                        <th>Grafik</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapPerProdi as $prodi)
                    <tr>
                        <td><strong>{{ $prodi->nama }}</strong></td>
                        <td class="text-center">{{ $prodi->mahasiswa_count }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $prodi->rata_ipk >= 3.0 ? 'success' : ($prodi->rata_ipk >= 2.5 ? 'warning' : 'danger') }} fs-6">
                                {{ number_format($prodi->rata_ipk, 2) }}
                            </span>
                        </td>
                        <td style="width: 40%;">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $prodi->rata_ipk >= 3.0 ? 'success' : ($prodi->rata_ipk >= 2.5 ? 'warning' : 'danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ ($prodi->rata_ipk / 4) * 100 }}%">
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">Keterangan Predikat IPK</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <span class="badge bg-success me-2">●</span> Cumlaude (≥ 3.50)
            </div>
            <div class="col-md-3">
                <span class="badge bg-primary me-2">●</span> Sangat Memuaskan (3.00 - 3.49)
            </div>
            <div class="col-md-3">
                <span class="badge bg-warning me-2">●</span> Memuaskan (2.50 - 2.99)
            </div>
            <div class="col-md-3">
                <span class="badge bg-danger me-2">●</span> Cukup/Kurang (< 2.50)
            </div>
        </div>
    </div>
</div>
@endsection
