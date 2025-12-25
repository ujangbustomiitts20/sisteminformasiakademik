@extends('layouts.app')

@section('title', 'Transaksi Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Transaksi Pembayaran</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Transaksi</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('transaksi-pembayaran.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Input Pembayaran
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Pemasukan Hari Ini</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_hari_ini'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-calendar-check display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Pemasukan Bulan Ini</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_bulan_ini'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-calendar-month display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-muted">Menunggu Verifikasi</h6>
                            <h4 class="mb-0">{{ number_format($stats['menunggu_verifikasi']) }}</h4>
                        </div>
                        <i class="bi bi-hourglass-split display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari transaksi..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Verified" {{ request('status') == 'Verified' ? 'selected' : '' }}>Verified</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="metode" class="form-select">
                        <option value="">Semua Metode</option>
                        @foreach(\App\Models\TransaksiPembayaran::METODE as $key => $label)
                            <option value="{{ $key }}" {{ request('metode') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}" placeholder="Dari Tanggal">
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}" placeholder="Sampai Tanggal">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Mahasiswa</th>
                            <th>No. Tagihan</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                        <tr>
                            <td><strong>{{ $item->no_transaksi }}</strong></td>
                            <td>
                                <strong>{{ $item->tagihan->mahasiswa->nama ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $item->tagihan->mahasiswa->nim ?? '-' }}</small>
                            </td>
                            <td>{{ $item->tagihan->no_tagihan ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-secondary">{{ $item->metode_pembayaran }}</span></td>
                            <td class="text-end">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @php
                                    $statusColor = match($item->status) {
                                        'Verified' => 'success',
                                        'Pending' => 'warning',
                                        'Rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ $item->status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('transaksi-pembayaran.show', $item) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($item->status === 'Pending')
                                    <form action="{{ route('transaksi-pembayaran.verify', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success" title="Verifikasi" onclick="return confirm('Verifikasi pembayaran ini?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-outline-danger" title="Tolak" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    @endif
                                </div>

                                <!-- Reject Modal -->
                                @if($item->status === 'Pending')
                                <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('transaksi-pembayaran.reject', $item) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tolak Pembayaran</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                        <textarea name="catatan_penolakan" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada data transaksi
                            </td>
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
