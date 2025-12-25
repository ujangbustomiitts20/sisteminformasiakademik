@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="page-title">
    <h4>Pembayaran</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pembayaran</li>
        </ol>
    </nav>
</div>

@if(Auth::user()->isAdmin())
<!-- Admin View -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 opacity-75">Total Lunas</h6>
                <h4 class="card-title mb-0">Rp {{ number_format($pembayaran->where('status', 'lunas')->sum('jumlah'), 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 opacity-75">Menunggu Verifikasi</h6>
                <h4 class="card-title mb-0">{{ $pembayaran->where('status', 'pending')->count() }} transaksi</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 opacity-75">Belum Bayar</h6>
                <h4 class="card-title mb-0">{{ $pembayaran->where('status', 'belum_bayar')->count() }} mahasiswa</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 opacity-75">Total Pembayaran</h6>
                <h4 class="card-title mb-0">{{ $pembayaran->count() }}</h4>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-credit-card me-2"></i>Daftar Pembayaran</span>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('pembayaran.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Pembayaran
        </a>
        @endif
    </div>
    <div class="card-body">
        @if(Auth::user()->isAdmin())
        <!-- Filter untuk Admin -->
        <form method="GET" action="{{ route('pembayaran.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIM atau nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Status --</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="belum_bayar" {{ request('status') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="jenis" class="form-select">
                        <option value="">-- Jenis --</option>
                        <option value="spp" {{ request('jenis') == 'spp' ? 'selected' : '' }}>SPP</option>
                        <option value="her" {{ request('jenis') == 'her' ? 'selected' : '' }}>Her-Registrasi</option>
                        <option value="wisuda" {{ request('jenis') == 'wisuda' ? 'selected' : '' }}>Wisuda</option>
                        <option value="lainnya" {{ request('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        @if(Auth::user()->isAdmin())
                        <th width="120">NIM</th>
                        <th>Mahasiswa</th>
                        @endif
                        <th>Jenis Pembayaran</th>
                        <th>Semester</th>
                        <th class="text-end">Jumlah</th>
                        <th width="120">Status</th>
                        <th>Tanggal</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaran as $index => $p)
                    <tr>
                        <td>{{ $pembayaran->firstItem() + $index }}</td>
                        @if(Auth::user()->isAdmin())
                        <td><code>{{ $p->mahasiswa->nim ?? '-' }}</code></td>
                        <td>{{ $p->mahasiswa->nama ?? '-' }}</td>
                        @endif
                        <td>
                            <strong>{{ ucfirst($p->jenis) }}</strong>
                            @if($p->keterangan)
                            <br><small class="text-muted">{{ $p->keterangan }}</small>
                            @endif
                        </td>
                        <td>{{ $p->tahunAkademik->nama_lengkap ?? '-' }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                        <td>
                            @if($p->status == 'lunas')
                            <span class="badge bg-success">Lunas</span>
                            @elseif($p->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @else
                            <span class="badge bg-danger">Belum Bayar</span>
                            @endif
                        </td>
                        <td>{{ $p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">
                            @if(Auth::user()->isAdmin())
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('pembayaran.show', $p) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('pembayaran.edit', $p) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                            @else
                            <a href="{{ route('pembayaran.show', $p) }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->isAdmin() ? 9 : 7 }}" class="text-center py-4">
                            <i class="bi bi-credit-card text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data pembayaran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pembayaran->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $pembayaran->firstItem() }} - {{ $pembayaran->lastItem() }} dari {{ $pembayaran->total() }} data
            </div>
            {{ $pembayaran->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
