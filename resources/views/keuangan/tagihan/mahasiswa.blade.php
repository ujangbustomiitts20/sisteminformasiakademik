@extends('layouts.app')

@section('title', 'Tagihan Saya')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Tagihan Saya</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Tagihan Saya</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-gradient bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white-50">Total Tagihan Belum Lunas</h6>
                            <h2 class="mb-0">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h2>
                        </div>
                        <i class="bi bi-wallet2 display-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-gradient bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white-50">Mahasiswa</h6>
                            <h5 class="mb-0">{{ $mahasiswa->nim }}</h5>
                            <p class="mb-0">{{ $mahasiswa->nama }}</p>
                        </div>
                        <i class="bi bi-person display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Tagihan -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Daftar Tagihan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Tagihan</th>
                            <th>Jenis</th>
                            <th>Tahun Akademik</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Sisa</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihan as $item)
                        <tr>
                            <td><strong>{{ $item->no_tagihan }}</strong></td>
                            <td><span class="badge bg-info">{{ $item->jenis_tagihan }}</span></td>
                            <td>{{ $item->tahunAkademik->nama ?? '-' }}</td>
                            <td class="text-end">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                            <td class="text-end">
                                @if($item->sisa_tagihan > 0)
                                    <span class="text-danger fw-bold">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success">Rp 0</span>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}
                                @if($item->is_overdue)
                                    <br><span class="badge bg-danger">Terlambat</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusColor = match($item->status) {
                                        'Belum Bayar' => 'danger',
                                        'Cicilan' => 'warning',
                                        'Lunas' => 'success',
                                        'Batal' => 'secondary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ $item->status }}</span>
                            </td>
                            <td class="text-center">
                                @if($item->sisa_tagihan > 0)
                                <a href="{{ route('pembayaran.bayar', $item) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-credit-card me-1"></i>Bayar
                                </a>
                                @else
                                <span class="text-success"><i class="bi bi-check-circle"></i></span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Tidak ada tagihan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tagihan->hasPages())
        <div class="card-footer bg-white">
            {{ $tagihan->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
