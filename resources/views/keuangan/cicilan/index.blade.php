@extends('layouts.app')

@section('title', 'Manajemen Cicilan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Cicilan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('laporan.dashboard') }}">Keuangan</a></li>
                    <li class="breadcrumb-item active">Cicilan</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('skema-cicilan.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-gear me-1"></i> Skema Cicilan
            </a>
            <a href="{{ route('cicilan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Buat Cicilan
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Cicilan</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="bi bi-list-check display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Cicilan Aktif</h6>
                            <h3 class="mb-0">{{ $stats['aktif'] }}</h3>
                        </div>
                        <i class="bi bi-hourglass-split display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Lunas</h6>
                            <h3 class="mb-0">{{ $stats['lunas'] }}</h3>
                        </div>
                        <i class="bi bi-check-circle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-dark opacity-75">Total Piutang</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_piutang'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-cash-stack display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama/NIM mahasiswa..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="Gagal" {{ request('status') == 'Gagal' ? 'selected' : '' }}>Gagal</option>
                        <option value="Batal" {{ request('status') == 'Batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('cicilan.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Tagihan</th>
                            <th>Skema</th>
                            <th class="text-end">Total Cicilan</th>
                            <th class="text-center">Progress</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cicilan as $cic)
                        <tr>
                            <td>
                                <strong>{{ $cic->tagihan->mahasiswa->nama ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $cic->tagihan->mahasiswa->nim ?? '-' }} - {{ $cic->tagihan->mahasiswa->programStudi->nama ?? '-' }}</small>
                            </td>
                            <td>
                                {{ $cic->tagihan->jenis_tagihan }}
                                <br><small class="text-muted">{{ $cic->tagihan->tahun_akademik ?? '-' }}</small>
                            </td>
                            <td>{{ $cic->skemaCicilan->nama ?? '-' }}</td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($cic->total_harus_dibayar, 0, ',', '.') }}</strong>
                                <br><small class="text-muted">@ Rp {{ number_format($cic->nominal_per_cicilan, 0, ',', '.') }}</small>
                            </td>
                            <td class="text-center" style="min-width: 150px;">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" style="width: {{ $cic->progress_persen }}%">
                                        {{ $cic->cicilan_terbayar }}/{{ $cic->jumlah_cicilan }}
                                    </div>
                                </div>
                                <small class="text-muted">{{ $cic->progress_persen }}% selesai</small>
                            </td>
                            <td class="text-center">{!! $cic->status_badge !!}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('cicilan.show', $cic) }}" class="btn btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($cic->status == 'Aktif' && $cic->cicilan_terbayar == 0)
                                    <form action="{{ route('cicilan.batalkan', $cic) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan cicilan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Batalkan">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada data cicilan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($cicilan->hasPages())
        <div class="card-footer">
            {{ $cicilan->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
