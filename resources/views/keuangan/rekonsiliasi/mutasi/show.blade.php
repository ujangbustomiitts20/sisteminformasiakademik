@extends('layouts.app')

@section('title', 'Detail Mutasi Bank')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Mutasi Bank</h1>
            <p class="text-muted mb-0">{{ $mutasiBank->akunBank->nama_bank }} - {{ $mutasiBank->tanggal->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('mutasi-bank.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Mutasi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Akun Bank</td>
                            <td>
                                <strong>{{ $mutasiBank->akunBank->nama_bank }}</strong>
                                <br>
                                <small class="text-muted">{{ $mutasiBank->akunBank->nomor_rekening }}</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal</td>
                            <td>{{ $mutasiBank->tanggal->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe</td>
                            <td>
                                @if($mutasiBank->tipe == 'kredit')
                                    <span class="badge bg-success fs-6">Kredit (Masuk)</span>
                                @else
                                    <span class="badge bg-danger fs-6">Debit (Keluar)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nominal</td>
                            <td class="{{ $mutasiBank->tipe == 'kredit' ? 'text-success' : 'text-danger' }} fs-4 fw-bold">
                                {{ $mutasiBank->tipe == 'kredit' ? '+' : '-' }} Rp {{ number_format($mutasiBank->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Saldo</td>
                            <td>{{ $mutasiBank->saldo ? 'Rp ' . number_format($mutasiBank->saldo, 0, ',', '.') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Referensi</td>
                            <td><code>{{ $mutasiBank->referensi ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Keterangan</td>
                            <td>{{ $mutasiBank->keterangan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($mutasiBank->status == 'matched')
                                    <span class="badge bg-success">Matched</span>
                                @elseif($mutasiBank->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($mutasiBank->status == 'unmatched')
                                    <span class="badge bg-danger">Unmatched</span>
                                @else
                                    <span class="badge bg-secondary">Manual</span>
                                @endif
                            </td>
                        </tr>
                        @if($mutasiBank->catatan)
                        <tr>
                            <td class="text-muted">Catatan</td>
                            <td>{{ $mutasiBank->catatan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($mutasiBank->status == 'pending')
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#matchModal">
                            <i class="bi bi-link"></i> Match dengan Transaksi
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="markManual()">
                            <i class="bi bi-hand-index"></i> Tandai sebagai Manual
                        </button>
                    </div>
                </div>
            </div>
            @endif

            @if($mutasiBank->status == 'matched')
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-warning w-100" onclick="unmatch()">
                        <i class="bi bi-link-45deg"></i> Batalkan Match
                    </button>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-6">
            @if($mutasiBank->transaksiPembayaran)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-check-circle"></i> Matched dengan Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">No. Transaksi</td>
                            <td>
                                <a href="{{ route('transaksi-pembayaran.show', $mutasiBank->transaksiPembayaran) }}">
                                    <strong>{{ $mutasiBank->transaksiPembayaran->nomor_transaksi }}</strong>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Mahasiswa</td>
                            <td>
                                {{ $mutasiBank->transaksiPembayaran->tagihan->mahasiswa->nama ?? '-' }}
                                <br>
                                <small class="text-muted">{{ $mutasiBank->transaksiPembayaran->tagihan->mahasiswa->nim ?? '' }}</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Bayar</td>
                            <td>{{ $mutasiBank->transaksiPembayaran->tanggal_bayar->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah</td>
                            <td class="text-success fw-bold">
                                Rp {{ number_format($mutasiBank->transaksiPembayaran->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metode</td>
                            <td>{{ ucfirst($mutasiBank->transaksiPembayaran->metode_pembayaran) }}</td>
                        </tr>
                    </table>
                    
                    @if($mutasiBank->matched_at)
                    <hr>
                    <small class="text-muted">
                        Di-match pada {{ $mutasiBank->matched_at->format('d/m/Y H:i') }}
                        @if($mutasiBank->matchedBy)
                            oleh {{ $mutasiBank->matchedBy->name }}
                        @endif
                    </small>
                    @endif
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transaksi yang Mungkin Cocok</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($potentialMatches ?? [] as $trx)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $trx->nomor_transaksi }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $trx->tagihan->mahasiswa->nama ?? '-' }} - 
                                    {{ $trx->tanggal_bayar->format('d/m/Y') }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="text-success fw-bold">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</span>
                                @if($mutasiBank->status == 'pending')
                                <br>
                                <button type="button" class="btn btn-sm btn-primary mt-1" onclick="matchWith({{ $trx->id }})">
                                    <i class="bi bi-link"></i> Match
                                </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            Tidak ada transaksi yang cocok
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Match Modal -->
<div class="modal fade" id="matchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Match dengan Transaksi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <strong>Nominal Mutasi:</strong> Rp {{ number_format($mutasiBank->nominal, 0, ',', '.') }}
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchTransaksi" placeholder="Cari transaksi...">
                </div>
                <div id="transaksiList" class="list-group" style="max-height: 400px; overflow-y: auto;">
                    @foreach($allUnmatchedTransaksi ?? [] as $trx)
                    <button type="button" class="list-group-item list-group-item-action" onclick="matchWith({{ $trx->id }})">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $trx->nomor_transaksi }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $trx->tagihan->mahasiswa->nama ?? '-' }} - 
                                    {{ $trx->tanggal_bayar->format('d/m/Y') }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="text-success fw-bold">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function matchWith(transaksiId) {
    fetch(`{{ route('mutasi-bank.match', $mutasiBank) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ transaksi_pembayaran_id: transaksiId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function unmatch() {
    if (!confirm('Batalkan match mutasi ini?')) return;
    
    fetch(`{{ route('mutasi-bank.unmatch', $mutasiBank) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function markManual() {
    const catatan = prompt('Catatan (opsional):');
    
    fetch(`{{ route('mutasi-bank.mark-manual', $mutasiBank) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ catatan: catatan })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>
@endpush
@endsection
