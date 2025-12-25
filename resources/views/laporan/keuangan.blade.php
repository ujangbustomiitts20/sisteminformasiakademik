@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Keuangan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">Keuangan</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('laporan.keuangan', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
        <i class="bi bi-file-pdf me-1"></i>Export PDF
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.keuangan') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select">
                    @foreach($tahunAkademik as $ta)
                    <option value="{{ $ta->id }}" {{ ($tahunAkademikAktif?->id == $ta->id) ? 'selected' : '' }}>
                        {{ $ta->tahun }} - {{ $ta->semester }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Tagihan</h6>
                <h4>Rp {{ number_format($summary['total'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Lunas</h6>
                <h4>Rp {{ number_format($summary['lunas'], 0, ',', '.') }}</h4>
                <small>{{ $summary['count_lunas'] }} transaksi</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">Belum Lunas</h6>
                <h4>Rp {{ number_format($summary['belum_lunas'], 0, ',', '.') }}</h4>
                <small>{{ $summary['count_belum'] }} transaksi</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Persentase Lunas</h6>
                <h4>{{ $summary['total'] > 0 ? round(($summary['lunas'] / $summary['total']) * 100, 1) : 0 }}%</h4>
            </div>
        </div>
    </div>
</div>

<!-- Data Pembayaran -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-cash-stack me-2"></i>Data Pembayaran
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaran as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $p->mahasiswa->nim }}</code></td>
                        <td>{{ $p->mahasiswa->nama }}</td>
                        <td>{{ $p->jenis }}</td>
                        <td>Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $p->status == 'Lunas' ? 'success' : 'warning' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td>{{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Tidak ada data pembayaran</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
