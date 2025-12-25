@extends('layouts.app')

@section('title', 'Pembayaran Saya')

@section('content')
<div class="page-title">
    <h4>Pembayaran Saya</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pembayaran</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Total Tagihan</h6>
                        <h3 class="mb-0">Rp {{ number_format($pembayaran->sum('jumlah'), 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Sudah Dibayar</h6>
                        <h3 class="mb-0">Rp {{ number_format($pembayaran->where('status', 'Lunas')->sum('jumlah'), 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Belum Dibayar</h6>
                        <h3 class="mb-0">Rp {{ number_format($pembayaran->where('status', 'Belum Lunas')->sum('jumlah'), 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-exclamation-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-receipt me-2"></i>Riwayat Pembayaran
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Tahun Akademik</th>
                        <th>Jenis</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-center">Status</th>
                        <th>Tanggal Bayar</th>
                        <th class="text-center" width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaran as $index => $p)
                    <tr>
                        <td>{{ $pembayaran->firstItem() + $index }}</td>
                        <td>{{ $p->tahunAkademik->nama_lengkap ?? '-' }}</td>
                        <td>{{ $p->jenis }}</td>
                        <td class="text-end"><strong>Rp {{ number_format($p->jumlah, 0, ',', '.') }}</strong></td>
                        <td class="text-center">
                            @if($p->status == 'Lunas')
                            <span class="badge bg-success">Lunas</span>
                            @else
                            <span class="badge bg-danger">Belum Lunas</span>
                            @endif
                        </td>
                        <td>{{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('pembayaran.show', $p) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-receipt text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data pembayaran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $pembayaran->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
