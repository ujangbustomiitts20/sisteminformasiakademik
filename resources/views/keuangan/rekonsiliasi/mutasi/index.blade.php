@extends('layouts.app')

@section('title', 'Mutasi Bank')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Mutasi Bank</h1>
            <p class="text-muted mb-0">Kelola mutasi rekening dari statement bank</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('mutasi-bank.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Manual
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload"></i> Import CSV
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Akun Bank</label>
                    <select name="akun_bank_id" class="form-select">
                        <option value="">Semua Akun</option>
                        @foreach($akunBank as $akun)
                        <option value="{{ $akun->id }}" {{ request('akun_bank_id') == $akun->id ? 'selected' : '' }}>
                            {{ $akun->nama_bank }} - {{ $akun->nomor_rekening }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="kredit" {{ request('tipe') == 'kredit' ? 'selected' : '' }}>Kredit (Masuk)</option>
                        <option value="debit" {{ request('tipe') == 'debit' ? 'selected' : '' }}>Debit (Keluar)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="matched" {{ request('status') == 'matched' ? 'selected' : '' }}>Matched</option>
                        <option value="unmatched" {{ request('status') == 'unmatched' ? 'selected' : '' }}>Unmatched</option>
                        <option value="manual" {{ request('status') == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Kredit</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_kredit'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-arrow-down-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Debit</h6>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_debit'], 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-arrow-up-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Matched</h6>
                            <h4 class="mb-0">{{ $stats['matched'] }} <small class="fs-6">mutasi</small></h4>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="opacity-75">Pending</h6>
                            <h4 class="mb-0">{{ $stats['pending'] }} <small class="fs-6">mutasi</small></h4>
                        </div>
                        <i class="bi bi-clock fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar Mutasi</span>
            @if(request('akun_bank_id'))
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="autoMatch()">
                <i class="bi bi-magic"></i> Auto Match
            </button>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Bank</th>
                            <th>Keterangan</th>
                            <th>Referensi</th>
                            <th>Tipe</th>
                            <th class="text-end">Nominal</th>
                            <th>Status</th>
                            <th>Match</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mutasi as $item)
                        <tr>
                            <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                            <td>
                                <small>{{ $item->akunBank->nama_bank }}</small>
                                <br>
                                <span class="text-muted">{{ $item->akunBank->nomor_rekening }}</span>
                            </td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $item->keterangan }}">
                                    {{ $item->keterangan ?? '-' }}
                                </span>
                            </td>
                            <td><code>{{ $item->referensi ?? '-' }}</code></td>
                            <td>
                                @if($item->tipe == 'kredit')
                                    <span class="badge bg-success">Kredit</span>
                                @else
                                    <span class="badge bg-danger">Debit</span>
                                @endif
                            </td>
                            <td class="text-end {{ $item->tipe == 'kredit' ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ $item->tipe == 'kredit' ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($item->status == 'matched')
                                    <span class="badge bg-success">Matched</span>
                                @elseif($item->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($item->status == 'unmatched')
                                    <span class="badge bg-danger">Unmatched</span>
                                @else
                                    <span class="badge bg-secondary">Manual</span>
                                @endif
                            </td>
                            <td>
                                @if($item->transaksiPembayaran)
                                    <a href="{{ route('transaksi-pembayaran.show', $item->transaksiPembayaran) }}" class="text-decoration-none">
                                        <small>{{ $item->transaksiPembayaran->nomor_transaksi }}</small>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('mutasi-bank.show', $item) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($item->status == 'pending')
                                    <button type="button" class="btn btn-outline-primary" title="Match Manual" 
                                            onclick="showMatchModal({{ $item->id }}, {{ $item->nominal }}, '{{ $item->tipe }}')">
                                        <i class="bi bi-link"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" title="Mark Manual" 
                                            onclick="markManual({{ $item->id }})">
                                        <i class="bi bi-hand-index"></i>
                                    </button>
                                    @elseif($item->status == 'matched')
                                    <button type="button" class="btn btn-outline-warning" title="Unmatch" 
                                            onclick="unmatch({{ $item->id }})">
                                        <i class="bi bi-link-45deg"></i>
                                    </button>
                                    @endif
                                    @if(!$item->transaksiPembayaran)
                                    <form action="{{ route('mutasi-bank.destroy', $item) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus mutasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                Tidak ada data mutasi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($mutasi->hasPages())
        <div class="card-footer">
            {{ $mutasi->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Mutasi Bank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mutasi-bank.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Akun Bank <span class="text-danger">*</span></label>
                        <select name="akun_bank_id" class="form-select" required>
                            <option value="">Pilih Akun Bank</option>
                            @foreach($akunBank as $akun)
                            <option value="{{ $akun->id }}">{{ $akun->nama_bank }} - {{ $akun->nomor_rekening }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File CSV <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted">Format: tanggal, keterangan, debit, kredit, saldo, referensi</small>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i>
                        <strong>Format CSV:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Kolom 1: Tanggal (DD/MM/YYYY atau YYYY-MM-DD)</li>
                            <li>Kolom 2: Keterangan</li>
                            <li>Kolom 3: Debit (nominal keluar)</li>
                            <li>Kolom 4: Kredit (nominal masuk)</li>
                            <li>Kolom 5: Saldo (opsional)</li>
                            <li>Kolom 6: Referensi (opsional)</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload"></i> Import
                    </button>
                </div>
            </form>
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
                    <strong>Nominal Mutasi:</strong> <span id="matchNominal">Rp 0</span>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchTransaksi" placeholder="Cari transaksi...">
                </div>
                <div id="transaksiList" class="list-group" style="max-height: 400px; overflow-y: auto;">
                    <!-- Loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentMutasiId = null;

function showMatchModal(mutasiId, nominal, tipe) {
    currentMutasiId = mutasiId;
    document.getElementById('matchNominal').textContent = 'Rp ' + nominal.toLocaleString('id-ID');
    
    // Load transaksi yang belum di-match
    fetch(`{{ route('transaksi-pembayaran.unmatched') }}?nominal=${nominal}&tipe=${tipe}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            data.forEach(trx => {
                html += `
                    <button type="button" class="list-group-item list-group-item-action" onclick="matchMutasi(${trx.id})">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>${trx.nomor_transaksi}</strong>
                                <br>
                                <small class="text-muted">${trx.mahasiswa_nama} - ${trx.tanggal}</small>
                            </div>
                            <div class="text-end">
                                <span class="text-success fw-bold">Rp ${parseInt(trx.jumlah).toLocaleString('id-ID')}</span>
                            </div>
                        </div>
                    </button>
                `;
            });
            document.getElementById('transaksiList').innerHTML = html || '<p class="text-center text-muted p-3">Tidak ada transaksi yang cocok</p>';
            new bootstrap.Modal(document.getElementById('matchModal')).show();
        });
}

function matchMutasi(transaksiId) {
    fetch(`{{ url('mutasi-bank') }}/${currentMutasiId}/match`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ transaksi_pembayaran_id: transaksiId })
    })
    .then(response => {
        if (response.redirected) {
            location.reload();
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data && data.success) {
            location.reload();
        } else if (data && data.message) {
            alert(data.message);
        }
    })
    .catch(() => location.reload());
}

function unmatch(mutasiId) {
    if (!confirm('Batalkan match mutasi ini?')) return;
    
    fetch(`{{ url('mutasi-bank') }}/${mutasiId}/unmatch`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.redirected) {
            location.reload();
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data && data.success) {
            location.reload();
        } else if (data && data.message) {
            alert(data.message);
        }
    })
    .catch(() => location.reload());
}

function markManual(mutasiId) {
    const catatan = prompt('Catatan (opsional):');
    if (catatan === null) return; // User cancelled
    
    fetch(`{{ url('mutasi-bank') }}/${mutasiId}/mark-manual`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ catatan: catatan || 'Diproses manual' })
    })
    .then(response => {
        if (response.redirected) {
            location.reload();
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data && data.success) {
            location.reload();
        } else if (data && data.message) {
            alert(data.message);
        }
    })
    .catch(() => location.reload());
}

function autoMatch() {
    if (!confirm('Jalankan auto-match untuk akun bank ini?')) return;
    
    const akunBankId = document.querySelector('[name="akun_bank_id"]').value;
    
    fetch(`{{ route('mutasi-bank.auto-match') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ akun_bank_id: akunBankId })
    })
    .then(response => {
        if (response.redirected) {
            location.reload();
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data) {
            alert(data.message || `Auto-match selesai. ${data.matched || 0} mutasi berhasil di-match.`);
            location.reload();
        }
    })
    .catch(() => location.reload());
}
</script>
@endpush
@endsection
