@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Detail Transaksi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('transaksi-pembayaran.index') }}">Transaksi</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Informasi Transaksi</h5>
                    @php
                        $statusColor = match($transaksiPembayaran->status) {
                            'Verified' => 'success',
                            'Pending' => 'warning',
                            'Rejected' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $statusColor }} fs-6">{{ $transaksiPembayaran->status }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">No. Transaksi</label>
                            <p class="mb-0 fw-bold">{{ $transaksiPembayaran->no_transaksi }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">No. Tagihan</label>
                            <p class="mb-0">{{ $transaksiPembayaran->tagihan->no_tagihan ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Bayar</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($transaksiPembayaran->tanggal_bayar)->format('d F Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Metode Pembayaran</label>
                            <p class="mb-0"><span class="badge bg-secondary">{{ $transaksiPembayaran->metode_pembayaran }}</span></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Jumlah Bayar</label>
                            <p class="mb-0 fs-4 fw-bold text-success">Rp {{ number_format($transaksiPembayaran->jumlah, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tahun Akademik</label>
                            <p class="mb-0">{{ $transaksiPembayaran->tagihan->tahunAkademik->nama ?? '-' }}</p>
                        </div>
                    </div>
                    @if($transaksiPembayaran->catatan)
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="text-muted small">Catatan</label>
                            <p class="mb-0">{{ $transaksiPembayaran->catatan }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Verifikasi Info -->
            @if($transaksiPembayaran->verified_at)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Informasi Verifikasi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">Diverifikasi Oleh</label>
                            <p class="mb-0">{{ $transaksiPembayaran->verifier->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Verifikasi</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($transaksiPembayaran->verified_at)->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Bukti Pembayaran -->
            @if($transaksiPembayaran->bukti_bayar)
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-image me-2"></i>Bukti Pembayaran</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/bukti-pembayaran/' . $transaksiPembayaran->bukti_bayar) }}" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 400px;">
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Info Mahasiswa -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">NIM</td>
                            <td class="text-end fw-bold">{{ $transaksiPembayaran->tagihan->mahasiswa->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td class="text-end">{{ $transaksiPembayaran->tagihan->mahasiswa->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Program Studi</td>
                            <td class="text-end">{{ $transaksiPembayaran->tagihan->mahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Aksi -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($transaksiPembayaran->status === 'Pending')
                        <form action="{{ route('transaksi-pembayaran.verify', $transaksiPembayaran) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Verifikasi pembayaran ini?')">
                                <i class="bi bi-check-lg me-1"></i>Verifikasi
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-lg me-1"></i>Tolak
                        </button>
                        @endif
                        <a href="{{ route('tagihan.show', $transaksiPembayaran->tagihan) }}" class="btn btn-outline-info">
                            <i class="bi bi-receipt me-1"></i>Lihat Tagihan
                        </a>
                        <a href="{{ route('transaksi-pembayaran.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if($transaksiPembayaran->status === 'Pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('transaksi-pembayaran.reject', $transaksiPembayaran) }}" method="POST">
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
@endsection
