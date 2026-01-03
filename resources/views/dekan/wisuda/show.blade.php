@extends('layouts.app')

@section('title', 'Detail Wisuda - ' . $periodeWisuda->nama)

@section('content')
<div class="page-title">
    <h4>Detail Periode Wisuda</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.wisuda.index') }}">Wisuda</a></li>
            <li class="breadcrumb-item active">{{ $periodeWisuda->nama }}</li>
        </ol>
    </nav>
</div>

<!-- Info Periode -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Informasi Periode Wisuda</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Nama Periode</strong></td>
                                <td>: {{ $periodeWisuda->nama }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Wisuda</strong></td>
                                <td>: {{ $periodeWisuda->tanggal_wisuda ? $periodeWisuda->tanggal_wisuda->format('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Lokasi</strong></td>
                                <td>: {{ $periodeWisuda->lokasi ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Status</strong></td>
                                <td>: <span class="badge bg-{{ $periodeWisuda->status == 'Dibuka' ? 'success' : 'secondary' }}">{{ $periodeWisuda->status }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Total Pendaftar</strong></td>
                                <td>: <strong>{{ $totalPendaftar }}</strong> orang</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h2 class="mb-0">{{ $totalPendaftar }}</h2>
                <p class="mb-0">Total Pendaftar dari {{ $fakultas->nama }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistik per Prodi -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Pendaftar per Program Studi</h6>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($statsPerProdi as $prodi)
            <div class="col-md-4 mb-3">
                <div class="card bg-light">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-1">{{ $prodi->pendaftar_count }}</h4>
                        <small class="text-muted">{{ $prodi->nama }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Daftar Pendaftar -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Daftar Pendaftar Wisuda</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Status Yudisium</th>
                        <th>Tgl Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftars as $index => $pendaftar)
                    <tr>
                        <td>{{ $pendaftars->firstItem() + $index }}</td>
                        <td>{{ $pendaftar->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $pendaftar->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $pendaftar->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td>
                            @if($pendaftar->yudisium)
                                <span class="badge bg-{{ $pendaftar->yudisium->status == 'lulus' ? 'success' : ($pendaftar->yudisium->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($pendaftar->yudisium->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Belum Yudisium</span>
                            @endif
                        </td>
                        <td>{{ $pendaftar->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('dekan.mahasiswa.show', $pendaftar->mahasiswa) }}" class="btn btn-sm btn-info" title="Detail Mahasiswa">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada pendaftar dari fakultas ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $pendaftars->links() }}
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('dekan.wisuda.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
@endsection
