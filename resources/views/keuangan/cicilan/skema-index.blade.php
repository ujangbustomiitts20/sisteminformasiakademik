@extends('layouts.app')

@section('title', 'Skema Cicilan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Skema Cicilan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('laporan.dashboard') }}">Keuangan</a></li>
                    <li class="breadcrumb-item active">Skema Cicilan</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('skema-cicilan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Skema
        </a>
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
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Skema</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="bi bi-list-ol display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Aktif</h6>
                            <h3 class="mb-0">{{ $stats['aktif'] }}</h3>
                        </div>
                        <i class="bi bi-check-circle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Nonaktif</h6>
                            <h3 class="mb-0">{{ $stats['nonaktif'] }}</h3>
                        </div>
                        <i class="bi bi-x-circle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Skema</th>
                            <th class="text-center">Jumlah Cicilan</th>
                            <th class="text-end">Biaya Admin</th>
                            <th class="text-center">Bunga (%)</th>
                            <th class="text-end">Min. Tagihan</th>
                            <th class="text-center">Interval (Hari)</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($skemaCicilan as $skema)
                        <tr>
                            <td>
                                <strong>{{ $skema->nama }}</strong>
                                @if($skema->keterangan)
                                <br><small class="text-muted">{{ Str::limit($skema->keterangan, 50) }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $skema->jumlah_cicilan }}x</td>
                            <td class="text-end">Rp {{ number_format($skema->biaya_admin, 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($skema->persentase_bunga, 1) }}%</td>
                            <td class="text-end">Rp {{ number_format($skema->minimal_tagihan, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $skema->interval_hari }}</td>
                            <td class="text-center">{!! $skema->status_badge !!}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('skema-cicilan.edit', $skema) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('skema-cicilan.toggle-status', $skema) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $skema->is_active ? 'warning' : 'success' }}" title="{{ $skema->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-{{ $skema->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('skema-cicilan.destroy', $skema) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus skema ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada skema cicilan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
