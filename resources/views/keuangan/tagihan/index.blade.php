@extends('layouts.app')

@section('title', 'Kelola Tagihan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Tagihan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tagihan</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('tagihan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Tagihan
            </a>
            <a href="{{ route('tagihan.generate') }}" class="btn btn-success">
                <i class="bi bi-magic me-1"></i>Generate Massal
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Total Tagihan</h6>
                            <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="bi bi-receipt display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Belum Bayar</h6>
                            <h3 class="mb-0">{{ number_format($stats['belum_bayar']) }}</h3>
                        </div>
                        <i class="bi bi-exclamation-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-muted">Cicilan</h6>
                            <h3 class="mb-0">{{ number_format($stats['cicilan']) }}</h3>
                        </div>
                        <i class="bi bi-clock display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Lunas</h6>
                            <h3 class="mb-0">{{ number_format($stats['lunas']) }}</h3>
                        </div>
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Total Piutang</h5>
                        </div>
                        <h3 class="mb-0 text-danger">Rp {{ number_format($stats['total_piutang'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIM/Nama/No Tagihan" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Belum Bayar" {{ request('status') == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="Cicilan" {{ request('status') == 'Cicilan' ? 'selected' : '' }}>Cicilan</option>
                        <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="Batal" {{ request('status') == 'Batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun_akademik_id" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunAkademik as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach(\App\Models\Tarif::JENIS as $jenis)
                            <option value="{{ $jenis }}" {{ request('jenis') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-2">
                        <input type="checkbox" name="overdue" value="1" class="form-check-input" id="overdue" {{ request('overdue') ? 'checked' : '' }}>
                        <label class="form-check-label" for="overdue">Terlambat</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Tagihan</th>
                            <th>Mahasiswa</th>
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
                            <td>
                                <strong>{{ $item->no_tagihan }}</strong>
                            </td>
                            <td>
                                <strong>{{ $item->mahasiswa->nama }}</strong>
                                <br><small class="text-muted">{{ $item->mahasiswa->nim }} - {{ $item->mahasiswa->programStudi->nama ?? '-' }}</small>
                            </td>
                            <td><span class="badge bg-info">{{ $item->jenis_tagihan }}</span></td>
                            <td>{{ $item->tahunAkademik->nama ?? '-' }}</td>
                            <td class="text-end">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                            <td class="text-end">
                                @if($item->sisa_tagihan > 0)
                                    <span class="text-danger">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</span>
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
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('tagihan.show', $item) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('transaksi-pembayaran.create', ['tagihan_id' => $item->id]) }}" class="btn btn-outline-success" title="Bayar">
                                        <i class="bi bi-cash"></i>
                                    </a>
                                    <a href="{{ route('tagihan.edit', $item) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada data tagihan
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
