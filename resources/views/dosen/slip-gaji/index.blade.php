@extends('layouts.app')

@section('title', 'Slip Gaji')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Slip Gaji</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Slip Gaji</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Statistik -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total_slip'] }}</div>
                        <div class="small">Total Slip Gaji</div>
                    </div>
                    <i class="bi bi-receipt fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-5 fw-bold">{{ format_rupiah($stats['total_gaji_tahun_ini']) }}</div>
                        <div class="small">Total Gaji {{ date('Y') }}</div>
                    </div>
                    <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if($stats['slip_terakhir'])
                        <div class="fs-5 fw-bold">{{ format_rupiah($stats['slip_terakhir']->gaji_bersih) }}</div>
                        <div class="small">Slip Terakhir ({{ $stats['slip_terakhir']->periode }})</div>
                        @else
                        <div class="fs-5 fw-bold">-</div>
                        <div class="small">Belum ada slip gaji</div>
                        @endif
                    </div>
                    <i class="bi bi-wallet2 fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('dosen.slip-gaji.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Slip Gaji -->
<div class="row g-3">
    @forelse($slipGajiList as $slip)
    <div class="col-md-4">
        <div class="card shadow-sm h-100 {{ $slip->status === 'dibayar' ? 'border-success' : '' }}">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h6 class="mb-0 fw-semibold">{{ $slip->periode }}</h6>
                    <small class="text-muted">{{ $slip->no_slip }}</small>
                </div>
                @php
                    $statusBadge = match($slip->status) {
                        'draft' => 'secondary',
                        'diproses' => 'warning',
                        'dibayar' => 'success',
                        default => 'secondary'
                    };
                @endphp
                <span class="badge bg-{{ $statusBadge }}">{{ ucfirst($slip->status) }}</span>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6 border-end">
                        <div class="small text-muted">Gaji Kotor</div>
                        <div class="fw-semibold">{{ format_rupiah($slip->gaji_kotor) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted">Potongan</div>
                        <div class="fw-semibold text-danger">{{ format_rupiah($slip->total_potongan) }}</div>
                    </div>
                </div>
                <div class="text-center py-3 bg-light rounded">
                    <div class="small text-muted">Take Home Pay</div>
                    <div class="h4 fw-bold text-success mb-0">{{ format_rupiah($slip->gaji_bersih) }}</div>
                </div>
                @if($slip->tanggal_bayar)
                <div class="text-center mt-2">
                    <small class="text-muted">
                        <i class="bi bi-calendar-check me-1"></i>
                        Dibayar: {{ $slip->tanggal_bayar->format('d/m/Y') }}
                    </small>
                </div>
                @endif
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex gap-2">
                    <a href="{{ route('dosen.slip-gaji.show', $slip) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                    @if($slip->status === 'dibayar')
                    <a href="{{ route('dosen.slip-gaji.cetak', $slip) }}" class="btn btn-sm btn-success flex-fill" target="_blank" title="Download/Print PDF">
                        <i class="bi bi-printer me-1"></i>Print
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                Belum ada slip gaji
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($slipGajiList->hasPages())
<div class="mt-4">
    {{ $slipGajiList->links() }}
</div>
@endif
@endsection
