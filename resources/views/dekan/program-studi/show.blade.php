@extends('layouts.app')

@section('title', 'Detail Program Studi')

@section('content')
<div class="page-title">
    <h4>{{ $programStudi->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.program-studi.index') }}">Program Studi</a></li>
            <li class="breadcrumb-item active">{{ $programStudi->nama }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="bi bi-diagram-3 fs-2"></i>
                </div>
                <h5 class="mb-1">{{ $programStudi->nama }}</h5>
                <p class="text-muted mb-1">Kode: {{ $programStudi->kode ?? '-' }}</p>
                <span class="badge bg-info">{{ $programStudi->jenjang ?? '-' }}</span>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistik Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="text-success mb-0">{{ $stats['mahasiswa_aktif'] }}</h4>
                        <small class="text-muted">Aktif</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-warning mb-0">{{ $stats['mahasiswa_cuti'] }}</h4>
                        <small class="text-muted">Cuti</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-info mb-0">{{ $stats['mahasiswa_lulus'] }}</h4>
                        <small class="text-muted">Lulus</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-danger mb-0">{{ $stats['mahasiswa_do'] }}</h4>
                        <small class="text-muted">DO/Keluar</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h4 class="text-primary mb-0">{{ number_format($stats['rata_ipk'], 2) }}</h4>
                    <small class="text-muted">Rata-rata IPK</small>
                </div>
            </div>
        </div>

        @if($kurikulumAktif)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-book me-2"></i>Kurikulum Aktif</h6>
            </div>
            <div class="card-body">
                <h6>{{ $kurikulumAktif->nama ?? 'Kurikulum '.$kurikulumAktif->tahun_mulai }}</h6>
                <p class="text-muted mb-2">Tahun: {{ $kurikulumAktif->tahun_mulai }} - {{ $kurikulumAktif->tahun_selesai ?? 'Sekarang' }}</p>
                <div class="d-flex justify-content-between">
                    <span>Jumlah Mata Kuliah:</span>
                    <strong>{{ $kurikulumAktif->mata_kuliah_count }}</strong>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Program Studi</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="35%">Kode Program Studi</td>
                        <td><strong>{{ $programStudi->kode ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Program Studi</td>
                        <td><strong>{{ $programStudi->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenjang</td>
                        <td><span class="badge bg-info">{{ $programStudi->jenjang ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fakultas</td>
                        <td>{{ $programStudi->fakultas->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ketua Program Studi</td>
                        <td>{{ $programStudi->kaprodi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total SKS Kurikulum</td>
                        <td><strong>{{ $programStudi->total_sks ?? 0 }}</strong> SKS</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Mahasiswa</td>
                        <td>{{ $programStudi->mahasiswa_count }} orang</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Dosen</td>
                        <td>{{ $programStudi->dosen_count }} orang</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Rasio Dosen:Mahasiswa</td>
                        <td>
                            @php
                                $rasio = $programStudi->dosen_count > 0 ? round($stats['mahasiswa_aktif'] / $programStudi->dosen_count) : 0;
                            @endphp
                            1:{{ $rasio }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jumlah Mata Kuliah</td>
                        <td>{{ $programStudi->mata_kuliah_count ?? 0 }} mata kuliah</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($mahasiswaPerAngkatan->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i>Mahasiswa Aktif per Angkatan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($mahasiswaPerAngkatan as $data)
                    <div class="col">
                        <a href="{{ route('dekan.program-studi.mahasiswa', ['programStudi' => $programStudi->hashid, 'angkatan' => $data->angkatan]) }}" class="text-decoration-none">
                            <div class="text-center p-3 border rounded hover-shadow" style="cursor: pointer; transition: all 0.3s;">
                                <h4 class="mb-1 text-primary">{{ $data->jumlah }}</h4>
                                <small class="text-muted">Angkatan {{ $data->angkatan }}</small>
                                <div class="mt-2">
                                    <small class="text-primary"><i class="bi bi-eye"></i> Lihat Detail</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($dosens->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Dosen Program Studi</h6>
                <a href="{{ route('dekan.program-studi.dosen', $programStudi->hashid) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-1"></i>Lihat Semua ({{ $programStudi->dosen_count }})
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIDN</th>
                                <th>Nama</th>
                                <th>Jabatan Fungsional</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dosens as $dosen)
                            <tr>
                                <td>{{ $dosen->nidn ?? '-' }}</td>
                                <td>{{ $dosen->nama }}</td>
                                <td>{{ $dosen->jabatan_fungsional ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('dekan.dosen.show', $dosen->hashid) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <a href="{{ route('dekan.program-studi.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Program Studi
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}
</style>
@endsection
