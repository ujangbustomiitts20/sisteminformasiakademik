@extends('layouts.app')

@section('title', 'Laporan Fakultas')

@section('content')
<div class="page-title">
    <h4>Laporan Fakultas</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Laporan</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Mahasiswa per Prodi -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i>Mahasiswa per Program Studi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Program Studi</th>
                                <th class="text-end">Mahasiswa Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mahasiswaPerProdi as $prodi)
                            <tr>
                                <td>{{ $prodi->nama }}</td>
                                <td class="text-end">
                                    <span class="badge bg-primary">{{ $prodi->mahasiswa_count }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td><strong>Total</strong></td>
                                <td class="text-end"><strong>{{ $mahasiswaPerProdi->sum('mahasiswa_count') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- IPK per Prodi -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Rata-rata IPK per Program Studi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Program Studi</th>
                                <th class="text-end">Rata-rata IPK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ipkPerProdi as $prodi)
                            <tr>
                                <td>{{ $prodi->nama }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ ($prodi->rata_ipk ?? 0) >= 3.0 ? 'success' : (($prodi->rata_ipk ?? 0) >= 2.0 ? 'warning' : 'danger') }}">
                                        {{ number_format($prodi->rata_ipk ?? 0, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dosen per Prodi -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person-workspace me-2"></i>Dosen per Program Studi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Program Studi</th>
                                <th class="text-end">Jumlah Dosen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dosenPerProdi as $prodi)
                            <tr>
                                <td>{{ $prodi->nama }}</td>
                                <td class="text-end">
                                    <span class="badge bg-success">{{ $prodi->dosen_count }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td><strong>Total</strong></td>
                                <td class="text-end"><strong>{{ $dosenPerProdi->sum('dosen_count') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kelulusan -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Kelulusan 5 Tahun Terakhir</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($kelulusan as $data)
                    <div class="col-6 col-md-4 text-center mb-3">
                        <div class="card border-0 bg-light">
                            <div class="card-body py-3">
                                <h4 class="text-success mb-0">{{ $data->jumlah }}</h4>
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
