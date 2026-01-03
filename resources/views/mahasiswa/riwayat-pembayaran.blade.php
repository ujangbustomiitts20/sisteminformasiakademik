@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h3 mb-0">Riwayat Pembayaran</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Riwayat Pembayaran</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('mahasiswa.rekap-pembayaran.download') }}" class="btn btn-primary">
        <i class="bi bi-download me-1"></i>Download PDF
    </a>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($mahasiswa)
<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('mahasiswa.rekap-pembayaran') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select">
                    <option value="">Semua Periode</option>
                    @foreach($tahunAkademiks as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                        {{ $ta->tahun }}/{{ $ta->tahun + 1 }} - {{ $ta->semester }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Metode Pembayaran</label>
                <select name="metode" class="form-select">
                    <option value="">Semua Metode</option>
                    @foreach($metodePembayarans as $metode)
                    <option value="{{ $metode }}" {{ request('metode') == $metode ? 'selected' : '' }}>{{ $metode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('mahasiswa.rekap-pembayaran') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Info Mahasiswa -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%">NIM</td>
                        <td><strong>{{ $mahasiswa->nim }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $mahasiswa->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fakultas</td>
                        <td>{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Ringkasan Pembayaran -->
    <div class="col-lg-8 mb-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Pembayaran</h6>
                                <h4 class="mb-0">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h4>
                            </div>
                            <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Jumlah Transaksi</h6>
                                <h4 class="mb-0">{{ $transaksi->count() }} Transaksi</h4>
                            </div>
                            <i class="bi bi-receipt-cutoff fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Transaksi -->
<div class="card shadow">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi Pembayaran</h6>
    </div>
    <div class="card-body p-0">
        @if($transaksi->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox display-4 text-muted"></i>
            <p class="text-muted mt-2">Belum ada riwayat pembayaran</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>No. Transaksi</th>
                        <th>Tagihan</th>
                        <th>Metode</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($item->tanggal_bayar)
                            {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y H:i') }}
                            @else
                            -
                            @endif
                        </td>
                        <td><code>{{ $item->no_transaksi ?? '-' }}</code></td>
                        <td>
                            @if($item->tagihan)
                            <small>{{ $item->tagihan->no_tagihan ?? '-' }}</small><br>
                            <small class="text-muted">
                                @if($item->tagihan->tahunAkademik)
                                {{ $item->tagihan->tahunAkademik->tahun }}/{{ $item->tagihan->tahunAkademik->tahun + 1 }}
                                @endif
                            </small>
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if($item->metode_pembayaran)
                            <span class="badge bg-secondary">{{ $item->metode_pembayaran }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-end">
                            <strong class="text-success">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-center">
                            @if($item->status == 'Berhasil' || $item->status == 'Success')
                            <span class="badge bg-success">Berhasil</span>
                            @elseif($item->status == 'Pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($item->status == 'Gagal' || $item->status == 'Failed')
                            <span class="badge bg-danger">Gagal</span>
                            @else
                            <span class="badge bg-secondary">{{ $item->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end">Total Pembayaran:</th>
                        <th class="text-end text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>Data per {{ now()->format('d F Y H:i') }}
            </small>
            <a href="{{ route('mahasiswa.rekap-pembayaran.download') }}" class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Download PDF
            </a>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data mahasiswa tidak ditemukan. Silakan hubungi admin.
</div>
@endif
@endsection
