@extends('layouts.app')

@section('title', 'Laporan Rekonsiliasi')

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 12px; }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="h3 mb-0">Laporan Rekonsiliasi</h1>
            <p class="text-muted mb-0">{{ $rekonsiliasi->nomor_rekonsiliasi }}</p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
            <a href="{{ route('rekonsiliasi.show', $rekonsiliasi) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Report Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="text-center mb-4">
                <h4 class="mb-1">LAPORAN REKONSILIASI BANK</h4>
                <h5 class="text-muted">{{ $rekonsiliasi->nomor_rekonsiliasi }}</h5>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%">Bank</td>
                            <td>: <strong>{{ $rekonsiliasi->akunBank->nama_bank }}</strong></td>
                        </tr>
                        <tr>
                            <td>No. Rekening</td>
                            <td>: {{ $rekonsiliasi->akunBank->nomor_rekening }}</td>
                        </tr>
                        <tr>
                            <td>Nama Rekening</td>
                            <td>: {{ $rekonsiliasi->akunBank->nama_rekening }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%">Periode</td>
                            <td>: {{ $rekonsiliasi->periode_awal->format('d/m/Y') }} - {{ $rekonsiliasi->periode_akhir->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Dibuat</td>
                            <td>: {{ $rekonsiliasi->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: 
                                @if($rekonsiliasi->status == 'approved')
                                    <span class="badge bg-success">APPROVED</span>
                                @else
                                    <span class="badge bg-info">{{ strtoupper($rekonsiliasi->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Ringkasan Rekonsiliasi</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr class="table-light">
                            <th colspan="2">Saldo Menurut Bank</th>
                        </tr>
                        <tr>
                            <td>Saldo Awal ({{ $rekonsiliasi->periode_awal->format('d/m/Y') }})</td>
                            <td class="text-end">Rp {{ number_format($rekonsiliasi->saldo_awal_bank, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Kredit</td>
                            <td class="text-end text-success">+ Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Debit</td>
                            <td class="text-end text-danger">- Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-secondary fw-bold">
                            <td>Saldo Akhir ({{ $rekonsiliasi->periode_akhir->format('d/m/Y') }})</td>
                            <td class="text-end">Rp {{ number_format($rekonsiliasi->saldo_akhir_bank, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr class="table-light">
                            <th colspan="2">Saldo Menurut Sistem</th>
                        </tr>
                        <tr>
                            <td>Saldo Sistem</td>
                            <td class="text-end">Rp {{ number_format($rekonsiliasi->saldo_sistem, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Transaksi Matched</td>
                            <td class="text-end">{{ $rekonsiliasi->total_matched }} transaksi</td>
                        </tr>
                        <tr>
                            <td>Total Mutasi Pending</td>
                            <td class="text-end">{{ $rekonsiliasi->total_unmatched }} transaksi</td>
                        </tr>
                        <tr class="{{ $rekonsiliasi->selisih == 0 ? 'table-success' : 'table-danger' }} fw-bold">
                            <td>SELISIH</td>
                            <td class="text-end">Rp {{ number_format(abs($rekonsiliasi->selisih), 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Matched Items -->
    <div class="card mb-4">
        <div class="card-header bg-success bg-opacity-25">
            <h5 class="card-title mb-0">
                <i class="bi bi-check-circle"></i> Item Matched ({{ $matchedItems->count() }})
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="25%">Mutasi Bank</th>
                            <th width="25%">Transaksi Sistem</th>
                            <th width="15%" class="text-end">Nominal Mutasi</th>
                            <th width="15%" class="text-end">Nominal Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matchedItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->mutasiBank->tanggal->format('d/m/Y') }}</td>
                            <td>
                                {{ Str::limit($item->mutasiBank->keterangan, 40) }}
                                @if($item->mutasiBank->referensi)
                                <br><code class="small">{{ $item->mutasiBank->referensi }}</code>
                                @endif
                            </td>
                            <td>
                                {{ $item->transaksiPembayaran->nomor_transaksi ?? '-' }}
                                @if($item->transaksiPembayaran)
                                <br><small>{{ $item->transaksiPembayaran->tagihan->mahasiswa->nama ?? '' }}</small>
                                @endif
                            </td>
                            <td class="text-end {{ $item->mutasiBank->tipe == 'kredit' ? 'text-success' : 'text-danger' }}">
                                {{ $item->mutasiBank->tipe == 'kredit' ? '+' : '-' }}
                                Rp {{ number_format($item->mutasiBank->nominal, 0, ',', '.') }}
                            </td>
                            <td class="text-end text-success">
                                @if($item->transaksiPembayaran)
                                Rp {{ number_format($item->transaksiPembayaran->jumlah, 0, ',', '.') }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">Tidak ada item matched</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($matchedItems->count() > 0)
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end">Total Matched:</td>
                            <td class="text-end text-success">Rp {{ number_format($matchedItems->sum(fn($i) => $i->mutasiBank->tipe == 'kredit' ? $i->mutasiBank->nominal : 0), 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format($matchedItems->sum(fn($i) => $i->transaksiPembayaran->jumlah ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Unmatched Items -->
    @if($unmatchedItems->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-warning bg-opacity-25">
            <h5 class="card-title mb-0">
                <i class="bi bi-exclamation-triangle"></i> Item Belum Matched / Manual ({{ $unmatchedItems->count() }})
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Tanggal</th>
                            <th>Keterangan</th>
                            <th width="10%">Referensi</th>
                            <th width="10%">Status</th>
                            <th width="15%" class="text-end">Nominal</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unmatchedItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->mutasiBank->tanggal->format('d/m/Y') }}</td>
                            <td>{{ Str::limit($item->mutasiBank->keterangan, 50) }}</td>
                            <td><code>{{ $item->mutasiBank->referensi ?? '-' }}</code></td>
                            <td>
                                @if($item->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($item->status == 'manual')
                                    <span class="badge bg-secondary">Manual</span>
                                @else
                                    <span class="badge bg-info">{{ $item->status }}</span>
                                @endif
                            </td>
                            <td class="text-end {{ $item->mutasiBank->tipe == 'kredit' ? 'text-success' : 'text-danger' }}">
                                {{ $item->mutasiBank->tipe == 'kredit' ? '+' : '-' }}
                                Rp {{ number_format($item->mutasiBank->nominal, 0, ',', '.') }}
                            </td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end">Total:</td>
                            <td class="text-end">
                                Rp {{ number_format($unmatchedItems->sum(fn($i) => $i->mutasiBank->nominal), 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Signature -->
    <div class="card">
        <div class="card-body">
            <div class="row mt-4">
                <div class="col-md-4 text-center">
                    <p>Dibuat oleh:</p>
                    <br><br><br>
                    <p class="mb-0 fw-bold">{{ $rekonsiliasi->createdBy->name ?? '________________' }}</p>
                    <small class="text-muted">Staff Keuangan</small>
                </div>
                <div class="col-md-4 text-center">
                    <p>Diperiksa oleh:</p>
                    <br><br><br>
                    <p class="mb-0 fw-bold">________________</p>
                    <small class="text-muted">Supervisor</small>
                </div>
                <div class="col-md-4 text-center">
                    <p>Disetujui oleh:</p>
                    <br><br><br>
                    <p class="mb-0 fw-bold">{{ $rekonsiliasi->approvedBy->name ?? '________________' }}</p>
                    <small class="text-muted">Kepala Bagian Keuangan</small>
                    @if($rekonsiliasi->approved_at)
                    <br><small class="text-muted">{{ $rekonsiliasi->approved_at->format('d/m/Y') }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="text-center text-muted mt-4 no-print">
        <small>Dicetak pada {{ now()->format('d/m/Y H:i:s') }}</small>
    </div>
</div>
@endsection
