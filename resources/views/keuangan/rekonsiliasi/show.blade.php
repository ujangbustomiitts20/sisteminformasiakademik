@extends('layouts.app')

@section('title', 'Detail Rekonsiliasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $rekonsiliasi->nomor_rekonsiliasi }}</h1>
            <p class="text-muted mb-0">
                {{ $rekonsiliasi->akunBank->nama_bank }} - 
                Periode {{ $rekonsiliasi->periode_awal->format('d/m/Y') }} s/d {{ $rekonsiliasi->periode_akhir->format('d/m/Y') }}
            </p>
        </div>
        <div class="btn-group">
            @if($rekonsiliasi->status == 'draft' || $rekonsiliasi->status == 'in_progress')
            <a href="{{ route('rekonsiliasi.process', $rekonsiliasi) }}" class="btn btn-primary">
                <i class="bi bi-gear"></i> Proses
            </a>
            @endif
            @if($rekonsiliasi->status == 'completed' || $rekonsiliasi->status == 'approved')
            <a href="{{ route('rekonsiliasi.report', $rekonsiliasi) }}" class="btn btn-success">
                <i class="bi bi-file-pdf"></i> Laporan
            </a>
            @endif
            <a href="{{ route('rekonsiliasi.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Summary -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Informasi Rekonsiliasi</h5>
                    @if($rekonsiliasi->status == 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($rekonsiliasi->status == 'completed')
                        <span class="badge bg-info">Completed</span>
                    @elseif($rekonsiliasi->status == 'in_progress')
                        <span class="badge bg-warning">In Progress</span>
                    @else
                        <span class="badge bg-secondary">Draft</span>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">No. Rekonsiliasi</td>
                            <td class="text-end fw-bold">{{ $rekonsiliasi->nomor_rekonsiliasi }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Akun Bank</td>
                            <td class="text-end">
                                {{ $rekonsiliasi->akunBank->nama_bank }}
                                <br>
                                <small>{{ $rekonsiliasi->akunBank->nomor_rekening }}</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Periode</td>
                            <td class="text-end">
                                {{ $rekonsiliasi->periode_awal->format('d/m/Y') }}
                                <br>
                                s/d {{ $rekonsiliasi->periode_akhir->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat</td>
                            <td class="text-end">
                                {{ $rekonsiliasi->created_at->format('d/m/Y H:i') }}
                                @if($rekonsiliasi->createdBy)
                                <br><small>{{ $rekonsiliasi->createdBy->name }}</small>
                                @endif
                            </td>
                        </tr>
                        @if($rekonsiliasi->approved_at)
                        <tr>
                            <td class="text-muted">Approved</td>
                            <td class="text-end">
                                {{ $rekonsiliasi->approved_at->format('d/m/Y H:i') }}
                                @if($rekonsiliasi->approvedBy)
                                <br><small>{{ $rekonsiliasi->approvedBy->name }}</small>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Saldo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted">Saldo Awal Bank</small>
                        <h5 class="mb-0">Rp {{ number_format($rekonsiliasi->saldo_awal_bank, 0, ',', '.') }}</h5>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted">Saldo Akhir Bank</small>
                        <h5 class="mb-0">Rp {{ number_format($rekonsiliasi->saldo_akhir_bank, 0, ',', '.') }}</h5>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted">Saldo Sistem</small>
                        <h5 class="mb-0">Rp {{ number_format($rekonsiliasi->saldo_sistem, 0, ',', '.') }}</h5>
                    </div>
                    <div class="{{ $rekonsiliasi->selisih == 0 ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }} p-3 rounded">
                        <small class="{{ $rekonsiliasi->selisih == 0 ? 'text-success' : 'text-danger' }}">Selisih</small>
                        <h4 class="mb-0 {{ $rekonsiliasi->selisih == 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format(abs($rekonsiliasi->selisih), 0, ',', '.') }}
                            @if($rekonsiliasi->selisih == 0)
                                <i class="bi bi-check-circle"></i>
                            @else
                                <i class="bi bi-exclamation-triangle"></i>
                            @endif
                        </h4>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistik Matching</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Mutasi</span>
                        <strong>{{ $rekonsiliasi->total_mutasi }}</strong>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        @php
                            $matchedPercent = $rekonsiliasi->total_mutasi > 0 
                                ? ($rekonsiliasi->total_matched / $rekonsiliasi->total_mutasi) * 100 
                                : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $matchedPercent }}%"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <span class="badge bg-success d-block mb-1">{{ $rekonsiliasi->total_matched }}</span>
                            <small class="text-muted">Matched</small>
                        </div>
                        <div class="col-4">
                            <span class="badge bg-warning d-block mb-1">{{ $rekonsiliasi->total_unmatched }}</span>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-4">
                            <span class="badge bg-secondary d-block mb-1">
                                {{ $rekonsiliasi->total_mutasi - $rekonsiliasi->total_matched - $rekonsiliasi->total_unmatched }}
                            </span>
                            <small class="text-muted">Manual</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($rekonsiliasi->status == 'completed')
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('rekonsiliasi.approve', $rekonsiliasi) }}" method="POST" 
                          onsubmit="return confirm('Approve rekonsiliasi ini?')">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Approve Rekonsiliasi
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Detail Items -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Item</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" data-filter="all">Semua</button>
                        <button type="button" class="btn btn-outline-success" data-filter="matched">Matched</button>
                        <button type="button" class="btn btn-outline-warning" data-filter="pending">Pending</button>
                        <button type="button" class="btn btn-outline-secondary" data-filter="manual">Manual</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Mutasi Bank</th>
                                    <th>Transaksi Sistem</th>
                                    <th class="text-end">Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekonsiliasi->details as $detail)
                                <tr data-status="{{ $detail->status }}">
                                    <td>{{ $detail->mutasiBank->tanggal->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                            {{ $detail->mutasiBank->keterangan ?? '-' }}
                                        </span>
                                        @if($detail->mutasiBank->referensi)
                                        <br><code class="small">{{ $detail->mutasiBank->referensi }}</code>
                                        @endif
                                    </td>
                                    <td>
                                        @if($detail->transaksiPembayaran)
                                            <a href="{{ route('transaksi-pembayaran.show', $detail->transaksiPembayaran) }}">
                                                {{ $detail->transaksiPembayaran->nomor_transaksi }}
                                            </a>
                                            <br>
                                            <small class="text-muted">
                                                {{ $detail->transaksiPembayaran->tagihan->mahasiswa->nama ?? '-' }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end {{ $detail->mutasiBank->tipe == 'kredit' ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ $detail->mutasiBank->tipe == 'kredit' ? '+' : '-' }}
                                        Rp {{ number_format($detail->mutasiBank->nominal, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($detail->status == 'matched')
                                            <span class="badge bg-success">Matched</span>
                                        @elseif($detail->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($detail->status == 'ignored')
                                            <span class="badge bg-secondary">Ignored</span>
                                        @else
                                            <span class="badge bg-info">Manual</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        Belum ada detail item
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($rekonsiliasi->catatan)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Catatan</h5>
                </div>
                <div class="card-body">
                    {{ $rekonsiliasi->catatan }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('[data-filter]').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.dataset.filter;
        
        // Update active button
        document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Filter rows
        document.querySelectorAll('tbody tr[data-status]').forEach(row => {
            if (filter === 'all' || row.dataset.status === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
@endsection
