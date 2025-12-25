@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Detail Tagihan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Info Tagihan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Informasi Tagihan</h5>
                    @php
                        $statusColor = match($tagihan->status) {
                            'Belum Bayar' => 'danger',
                            'Cicilan' => 'warning',
                            'Lunas' => 'success',
                            'Batal' => 'secondary',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $statusColor }} fs-6">{{ $tagihan->status }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">No. Tagihan</label>
                            <p class="mb-0 fw-bold">{{ $tagihan->no_tagihan }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tahun Akademik</label>
                            <p class="mb-0">{{ $tagihan->tahunAkademik->nama ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Jenis Tagihan</label>
                            <p class="mb-0"><span class="badge bg-info">{{ $tagihan->jenis_tagihan }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Jatuh Tempo</label>
                            <p class="mb-0">
                                {{ \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d F Y') }}
                                @if($tagihan->is_overdue)
                                    <span class="badge bg-danger ms-2">Terlambat</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($tagihan->keterangan_tagihan)
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="text-muted small">Keterangan</label>
                            <p class="mb-0">{{ $tagihan->keterangan_tagihan }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Rincian Biaya -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Rincian Biaya</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td>Nominal Tagihan</td>
                            <td class="text-end">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                        </tr>
                        @if($tagihan->diskon > 0)
                        <tr class="text-success">
                            <td>Diskon/Potongan</td>
                            <td class="text-end">- Rp {{ number_format($tagihan->diskon, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($tagihan->denda > 0)
                        <tr class="text-danger">
                            <td>Denda Keterlambatan</td>
                            <td class="text-end">+ Rp {{ number_format($tagihan->denda, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="border-top fw-bold">
                            <td>Total yang Harus Dibayar</td>
                            <td class="text-end">Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Jumlah Sudah Dibayar</td>
                            <td class="text-end text-success">Rp {{ number_format($tagihan->jumlah_dibayar, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top fs-5 fw-bold">
                            <td>Sisa Tagihan</td>
                            <td class="text-end {{ $tagihan->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Riwayat Pembayaran -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran</h5>
                    @if($tagihan->sisa_tagihan > 0)
                    <a href="{{ route('transaksi-pembayaran.create', ['tagihan_id' => $tagihan->id]) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Pembayaran
                    </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-center">Status</th>
                                    <th>Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tagihan->transaksi as $trx)
                                <tr>
                                    <td><strong>{{ $trx->no_transaksi }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($trx->tanggal_bayar)->format('d/m/Y') }}</td>
                                    <td>{{ $trx->metode_pembayaran }}</td>
                                    <td class="text-end">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @php
                                            $trxStatusColor = match($trx->status) {
                                                'Verified' => 'success',
                                                'Pending' => 'warning',
                                                'Rejected' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $trxStatusColor }}">{{ $trx->status }}</span>
                                    </td>
                                    <td>
                                        @if($trx->verifier)
                                            {{ $trx->verifier->name }}
                                            <br><small class="text-muted">{{ $trx->verified_at ? \Carbon\Carbon::parse($trx->verified_at)->format('d/m/Y H:i') : '' }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada riwayat pembayaran
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Info Mahasiswa -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <span class="fs-3">{{ substr($tagihan->mahasiswa->nama, 0, 1) }}</span>
                        </div>
                    </div>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">NIM</td>
                            <td class="text-end fw-bold">{{ $tagihan->mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td class="text-end">{{ $tagihan->mahasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Program Studi</td>
                            <td class="text-end">{{ $tagihan->mahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Angkatan</td>
                            <td class="text-end">{{ $tagihan->mahasiswa->angkatan }}</td>
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
                        <a href="{{ route('tagihan.edit', $tagihan) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Edit Tagihan
                        </a>
                        @if($tagihan->sisa_tagihan > 0)
                        <a href="{{ route('transaksi-pembayaran.create', ['tagihan_id' => $tagihan->id]) }}" class="btn btn-success">
                            <i class="bi bi-cash me-1"></i>Input Pembayaran
                        </a>
                        @endif
                        <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
