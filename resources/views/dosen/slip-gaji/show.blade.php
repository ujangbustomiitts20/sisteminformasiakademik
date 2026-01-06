@extends('layouts.app')

@section('title', 'Detail Slip Gaji')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Detail Slip Gaji</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dosen.slip-gaji.index') }}">Slip Gaji</a></li>
                <li class="breadcrumb-item active">{{ $slipGaji->periode }}</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('dosen.slip-gaji.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        @if($slipGaji->status === 'dibayar')
        <a href="{{ route('dosen.slip-gaji.cetak', $slipGaji) }}" class="btn btn-outline-primary btn-sm me-2" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>
        <button type="button" class="btn btn-primary btn-sm" onclick="printSlipGaji()">
            <i class="bi bi-printer me-1"></i>Print
        </button>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Info Slip -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Slip Gaji</h6>
                @php
                    $statusBadge = match($slipGaji->status) {
                        'draft' => 'secondary',
                        'diproses' => 'warning',
                        'dibayar' => 'success',
                        default => 'secondary'
                    };
                @endphp
                <span class="badge bg-{{ $statusBadge }} fs-6">{{ ucfirst($slipGaji->status) }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">No. Slip</label>
                        <p class="fw-semibold mb-0">{{ $slipGaji->no_slip }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Periode</label>
                        <p class="fw-semibold mb-0">{{ $slipGaji->periode }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Tanggal Slip</label>
                        <p class="fw-semibold mb-0">{{ $slipGaji->tanggal_slip->format('d/m/Y') }}</p>
                    </div>
                    @if($slipGaji->tanggal_bayar)
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Tanggal Bayar</label>
                        <p class="fw-semibold mb-0">{{ $slipGaji->tanggal_bayar->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Metode Pembayaran</label>
                        <p class="fw-semibold mb-0">{{ $slipGaji->metode_pembayaran ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">No. Referensi</label>
                        <p class="fw-semibold mb-0">{{ $slipGaji->no_referensi ?? '-' }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Komponen Pendapatan -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white py-3">
                <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Pendapatan</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Komponen</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Gaji Pokok</td>
                            <td class="text-end">{{ format_rupiah($slipGaji->gaji_pokok) }}</td>
                        </tr>
                        @foreach($pendapatan as $item)
                        <tr>
                            <td>{{ $item->nama_komponen }}</td>
                            <td class="text-end">{{ format_rupiah($item->nilai) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-success">
                        <tr>
                            <th>Total Pendapatan</th>
                            <th class="text-end">{{ format_rupiah($slipGaji->gaji_kotor) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Komponen Potongan -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white py-3">
                <h6 class="mb-0"><i class="bi bi-dash-circle me-2"></i>Potongan</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Komponen</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($potongan as $item)
                        <tr>
                            <td>{{ $item->nama_komponen }}</td>
                            <td class="text-end">{{ format_rupiah($item->nilai) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">Tidak ada potongan</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-danger">
                        <tr>
                            <th>Total Potongan</th>
                            <th class="text-end">{{ format_rupiah($slipGaji->total_potongan) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($slipGaji->catatan)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Catatan</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $slipGaji->catatan }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Ringkasan -->
        <div class="card shadow-sm border-primary mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>Ringkasan</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Pendapatan</span>
                    <span class="fw-semibold text-success">{{ format_rupiah($slipGaji->gaji_kotor) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Potongan</span>
                    <span class="fw-semibold text-danger">- {{ format_rupiah($slipGaji->total_potongan) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Take Home Pay</span>
                    <span class="fw-bold text-primary fs-5">{{ format_rupiah($slipGaji->gaji_bersih) }}</span>
                </div>
            </div>
        </div>

        <!-- Info Pegawai -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Info Pegawai</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td class="fw-semibold">{{ $dosen->nama_lengkap ?? $dosen->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIDN</td>
                        <td class="fw-semibold">{{ $dosen->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIP</td>
                        <td class="fw-semibold">{{ $dosen->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Unit Kerja</td>
                        <td class="fw-semibold">{{ $dosen->programStudi->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .sidebar, .navbar, .breadcrumb, .btn, nav, footer {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
    .card-header {
        background: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .badge {
        border: 1px solid #000 !important;
    }
    body {
        font-size: 12px;
    }
    .col-lg-8 {
        width: 70% !important;
        float: left;
    }
    .col-lg-4 {
        width: 30% !important;
        float: right;
    }
}
</style>
@endpush

@push('scripts')
<script>
function printSlipGaji() {
    window.print();
}
</script>
@endpush
