@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Riwayat Pembayaran Saya</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Riwayat Pembayaran</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Daftar Transaksi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Transaksi</th>
                            <th>No. Tagihan</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                        <tr>
                            <td><strong>{{ $item->no_transaksi }}</strong></td>
                            <td>{{ $item->tagihan->no_tagihan ?? '-' }}</td>
                            <td><span class="badge bg-info">{{ $item->tagihan->jenis_tagihan ?? '-' }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}</td>
                            <td>{{ $item->metode_pembayaran }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
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
                                @if($item->status === 'Rejected' && $item->catatan)
                                    <br><small class="text-danger">{{ $item->catatan }}</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada riwayat pembayaran
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
