@extends('layouts.app')

@section('title', 'Mahasiswa Bermasalah')

@section('content')
<div class="page-title">
    <h4>Mahasiswa Bermasalah - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.mahasiswa.index') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active">Bermasalah</li>
        </ol>
    </nav>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-danger mb-1">{{ $summary['ipk_rendah'] ?? 0 }}</h5>
                        <small class="text-muted">IPK Rendah (<2.00)</small>
                    </div>
                    <i class="bi bi-graph-down-arrow text-danger" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-warning mb-1">{{ $summary['cuti'] ?? 0 }}</h5>
                        <small class="text-muted">Sedang Cuti</small>
                    </div>
                    <i class="bi bi-pause-circle text-warning" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-secondary mb-1">{{ $summary['tidak_aktif'] ?? 0 }}</h5>
                        <small class="text-muted">Tidak Aktif</small>
                    </div>
                    <i class="bi bi-person-x text-secondary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary per Prodi -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-building me-2"></i>Ringkasan per Program Studi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Program Studi</th>
                        <th class="text-center">Cuti</th>
                        <th class="text-center">Tidak Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summaryPerProdi as $prodi)
                    <tr>
                        <td>{{ $prodi->nama }}</td>
                        <td class="text-center"><span class="badge bg-warning">{{ $prodi->cuti_count }}</span></td>
                        <td class="text-center"><span class="badge bg-secondary">{{ $prodi->tidak_aktif_count }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- IPK Rendah -->
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Mahasiswa dengan IPK Rendah (<2.00)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th class="text-center">IPK</th>
                        <th class="text-center">Total SKS</th>
                        <th>Dosen PA</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswaIpkRendah as $mhs)
                    <tr>
                        <td>{{ $mhs->nim }}</td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td class="text-center"><span class="badge bg-danger">{{ number_format($mhs->ipk ?? 0, 2) }}</span></td>
                        <td class="text-center">{{ $mhs->total_sks ?? 0 }}</td>
                        <td>{{ $mhs->dosenWali->nama ?? '-' }}</td>
                        <td>
                            <a href="{{ route('dekan.mahasiswa.show', $mhs) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">Tidak ada mahasiswa dengan IPK rendah</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mahasiswa Cuti -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h6 class="mb-0"><i class="bi bi-pause-circle me-2"></i>Mahasiswa Sedang Cuti</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>Alasan Cuti</th>
                        <th>Periode Cuti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswaCuti as $mhs)
                    <tr>
                        <td>{{ $mhs->nim }}</td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td>{{ $mhs->cuti->alasan ?? '-' }}</td>
                        <td>{{ $mhs->cuti->tahunAkademik->nama ?? '-' }}</td>
                        <td>
                            <a href="{{ route('dekan.mahasiswa.show', $mhs) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Tidak ada mahasiswa cuti</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mahasiswa Tidak Aktif -->
<div class="card">
    <div class="card-header bg-secondary text-white">
        <h6 class="mb-0"><i class="bi bi-person-x me-2"></i>Mahasiswa Tidak Aktif</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>Status</th>
                        <th>Terakhir Aktif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswaTidakAktif as $mhs)
                    <tr>
                        <td>{{ $mhs->nim }}</td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td><span class="badge bg-secondary">{{ $mhs->status }}</span></td>
                        <td>{{ $mhs->updated_at?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('dekan.mahasiswa.show', $mhs) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Tidak ada mahasiswa tidak aktif</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
