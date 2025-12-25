@extends('layouts.app')

@section('title', 'Proses Rekonsiliasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Proses Rekonsiliasi</h1>
            <p class="text-muted mb-0">
                {{ $rekonsiliasi->nomor_rekonsiliasi }} - 
                {{ $rekonsiliasi->akunBank->nama_bank }} ({{ $rekonsiliasi->akunBank->nomor_rekening }})
            </p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" onclick="autoMatchAll()">
                <i class="bi bi-magic"></i> Auto Match All
            </button>
            <a href="{{ route('rekonsiliasi.show', $rekonsiliasi) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Saldo Bank</h6>
                    <h4 class="mb-0">Rp {{ number_format($rekonsiliasi->saldo_akhir_bank, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Saldo Sistem</h6>
                    <h4 class="mb-0">Rp {{ number_format($rekonsiliasi->saldo_sistem, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 {{ $rekonsiliasi->selisih == 0 ? 'bg-success bg-opacity-25' : 'bg-danger bg-opacity-25' }}">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Selisih</h6>
                    <h4 class="mb-0 {{ $rekonsiliasi->selisih == 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format(abs($rekonsiliasi->selisih), 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50 mb-1">Progress</h6>
                    <h4 class="mb-0">
                        {{ $rekonsiliasi->total_matched }} / {{ $rekonsiliasi->total_mutasi }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Mutasi Pending -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-warning bg-opacity-25">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock"></i> Mutasi Pending
                        <span class="badge bg-warning text-dark">{{ count($pendingMutasi) }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                        @forelse($pendingMutasi as $mutasi)
                        <div class="list-group-item mutasi-item" data-mutasi-id="{{ $mutasi->id }}" data-nominal="{{ $mutasi->nominal }}"
                             onclick="selectMutasi({{ $mutasi->id }}, {{ $mutasi->nominal }}, '{{ $mutasi->tipe }}')">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small class="text-muted">{{ $mutasi->tanggal->format('d/m/Y') }}</small>
                                    <p class="mb-1 text-truncate" style="max-width: 250px;">
                                        {{ $mutasi->keterangan ?? 'Tanpa keterangan' }}
                                    </p>
                                    @if($mutasi->referensi)
                                    <code class="small">{{ $mutasi->referensi }}</code>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="{{ $mutasi->tipe == 'kredit' ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ $mutasi->tipe == 'kredit' ? '+' : '-' }}
                                        Rp {{ number_format($mutasi->nominal, 0, ',', '.') }}
                                    </span>
                                    <br>
                                    <div class="btn-group btn-group-sm mt-1">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                onclick="event.stopPropagation(); markAsManual({{ $mutasi->id }})" title="Manual">
                                            <i class="bi bi-hand-index"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                onclick="event.stopPropagation(); ignoreItem({{ $mutasi->id }})" title="Ignore">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-check-circle display-4 text-success d-block mb-2"></i>
                            Semua mutasi sudah di-match!
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi Unmatched -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-info bg-opacity-25">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt"></i> Transaksi Sistem
                        <span class="badge bg-info">{{ count($unmatchedTransaksi) }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="mb-3 p-3 border-bottom">
                        <input type="text" class="form-control" id="searchTransaksi" placeholder="Cari transaksi...">
                    </div>
                    <div class="list-group list-group-flush" id="transaksiList" style="max-height: 450px; overflow-y: auto;">
                        @forelse($unmatchedTransaksi as $trx)
                        <div class="list-group-item transaksi-item" data-trx-id="{{ $trx->id }}" data-nominal="{{ $trx->jumlah }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $trx->nomor_transaksi }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $trx->tagihan->mahasiswa->nama ?? '-' }}
                                        <br>
                                        {{ $trx->tanggal_bayar->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="text-success fw-bold">
                                        Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                                    </span>
                                    <br>
                                    <button type="button" class="btn btn-primary btn-sm mt-1 match-btn" 
                                            onclick="matchTransaksi({{ $trx->id }})" disabled>
                                        <i class="bi bi-link"></i> Match
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                            Semua transaksi sudah di-match
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Button -->
    <div class="card mt-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <strong>Status:</strong>
                @if($rekonsiliasi->total_matched == $rekonsiliasi->total_mutasi && $rekonsiliasi->selisih == 0)
                    <span class="badge bg-success">Siap untuk diselesaikan</span>
                @elseif($rekonsiliasi->total_matched == $rekonsiliasi->total_mutasi)
                    <span class="badge bg-warning">Semua di-match tapi ada selisih</span>
                @else
                    <span class="badge bg-secondary">Masih ada mutasi pending</span>
                @endif
            </div>
            <form action="{{ route('rekonsiliasi.complete', $rekonsiliasi) }}" method="POST"
                  onsubmit="return confirm('Selesaikan rekonsiliasi ini?')">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg"></i> Selesaikan Rekonsiliasi
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedMutasiId = null;
let selectedMutasiNominal = null;
let selectedMutasiTipe = null;

function selectMutasi(id, nominal, tipe) {
    selectedMutasiId = id;
    selectedMutasiNominal = nominal;
    selectedMutasiTipe = tipe;
    
    // Highlight selected
    document.querySelectorAll('.mutasi-item').forEach(el => el.classList.remove('active'));
    document.querySelector(`[data-mutasi-id="${id}"]`).classList.add('active');
    
    // Enable match buttons
    document.querySelectorAll('.match-btn').forEach(btn => btn.disabled = false);
    
    // Highlight matching nominal
    document.querySelectorAll('.transaksi-item').forEach(el => {
        el.classList.remove('list-group-item-success');
        if (parseInt(el.dataset.nominal) === nominal && tipe === 'kredit') {
            el.classList.add('list-group-item-success');
        }
    });
}

function matchTransaksi(transaksiId) {
    if (!selectedMutasiId) {
        alert('Pilih mutasi terlebih dahulu');
        return;
    }
    
    fetch(`{{ route('rekonsiliasi.match-detail', $rekonsiliasi) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            mutasi_bank_id: selectedMutasiId,
            transaksi_pembayaran_id: transaksiId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function markAsManual(mutasiId) {
    const catatan = prompt('Catatan:');
    
    fetch(`{{ route('rekonsiliasi.mark-manual', $rekonsiliasi) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            mutasi_bank_id: mutasiId,
            catatan: catatan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function ignoreItem(mutasiId) {
    if (!confirm('Ignore mutasi ini?')) return;
    
    fetch(`{{ route('rekonsiliasi.ignore-detail', $rekonsiliasi) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ mutasi_bank_id: mutasiId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function autoMatchAll() {
    if (!confirm('Jalankan auto-match untuk semua mutasi pending?')) return;
    
    fetch(`{{ route('rekonsiliasi.auto-match', $rekonsiliasi) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Check if redirected (non-JSON response)
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data) {
            alert(data.message || 'Auto-match selesai');
            location.reload();
        }
    })
    .catch(error => {
        // Just reload on error - likely a redirect
        location.reload();
    });
}

// Search functionality
document.getElementById('searchTransaksi').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.transaksi-item').forEach(el => {
        const text = el.textContent.toLowerCase();
        el.style.display = text.includes(query) ? '' : 'none';
    });
});
</script>
@endpush

@push('styles')
<style>
.mutasi-item, .transaksi-item {
    cursor: pointer;
    transition: all 0.2s;
}
.mutasi-item:hover, .transaksi-item:hover {
    background-color: #f8f9fa;
}
.mutasi-item.active {
    background-color: #e7f1ff;
    border-left: 3px solid #0d6efd;
}
</style>
@endpush
@endsection
