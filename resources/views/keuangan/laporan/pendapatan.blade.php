@extends('layouts.app')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Pendapatan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('laporan.dashboard') }}">Keuangan</a></li>
                    <li class="breadcrumb-item active">Pendapatan</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('laporan.pendapatan.excel', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('laporan.pendapatan.pdf', request()->all()) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Transaksi</h6>
                            <h3 class="mb-0">{{ number_format($summary['total_transaksi']) }}</h3>
                        </div>
                        <i class="bi bi-receipt display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Pendapatan</h6>
                            <h3 class="mb-0">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-cash-stack display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-2">
                    <select name="tahun_akademik_id" class="form-select">
                        <option value="">Semua Tahun Akademik</option>
                        @foreach($tahunAkademik as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}" placeholder="Dari Tanggal">
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}" placeholder="Sampai Tanggal">
                </div>
                <div class="col-md-2">
                    <select name="metode_pembayaran" class="form-select">
                        <option value="">Semua Metode</option>
                        @foreach(\App\Models\TransaksiPembayaran::METODE as $key => $label)
                            <option value="{{ $key }}" {{ request('metode_pembayaran') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('laporan.pendapatan') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Mahasiswa</th>
                            <th>No. Tagihan</th>
                            <th>Metode</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $trx)
                        <tr>
                            <td><code>{{ $trx->no_transaksi }}</code></td>
                            <td>{{ $trx->tanggal_bayar->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $trx->mahasiswa->nama ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $trx->mahasiswa->nim ?? '-' }} - {{ $trx->mahasiswa->programStudi->nama ?? '-' }}</small>
                            </td>
                            <td><code>{{ $trx->tagihan->no_tagihan ?? '-' }}</code></td>
                            <td>{{ $trx->metode_pembayaran }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('invoice.kwitansi', $trx) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada data transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transaksi->hasPages())
        <div class="card-footer bg-white">
            {{ $transaksi->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
