@extends('layouts.app')

@section('title', 'Rekonsiliasi Bank')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Rekonsiliasi Bank</h1>
            <p class="text-muted mb-0">Pencocokan mutasi bank dengan transaksi pembayaran</p>
        </div>
        <a href="{{ route('rekonsiliasi.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Buat Rekonsiliasi Baru
        </a>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Rekonsiliasi</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="bi bi-file-earmark-text fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="opacity-75">Draft</h6>
                            <h3 class="mb-0">{{ $stats['draft'] }}</h3>
                        </div>
                        <i class="bi bi-pencil fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">In Progress</h6>
                            <h3 class="mb-0">{{ $stats['in_progress'] }}</h3>
                        </div>
                        <i class="bi bi-arrow-repeat fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Approved</h6>
                            <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
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
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('rekonsiliasi.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Rekonsiliasi</th>
                            <th>Akun Bank</th>
                            <th>Periode</th>
                            <th class="text-end">Saldo Bank</th>
                            <th class="text-end">Saldo Sistem</th>
                            <th class="text-end">Selisih</th>
                            <th class="text-center">Matched</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekonsiliasi as $rekon)
                        <tr>
                            <td>
                                <a href="{{ route('rekonsiliasi.show', $rekon) }}" class="fw-bold text-decoration-none">
                                    {{ $rekon->nomor_rekonsiliasi }}
                                </a>
                                <br>
                                <small class="text-muted">{{ $rekon->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                {{ $rekon->akunBank->nama_bank }}
                                <br>
                                <small class="text-muted">{{ $rekon->akunBank->nomor_rekening }}</small>
                            </td>
                            <td>
                                {{ $rekon->periode_awal->format('d/m/Y') }}
                                <br>
                                <span class="text-muted">s/d</span> {{ $rekon->periode_akhir->format('d/m/Y') }}
                            </td>
                            <td class="text-end">Rp {{ number_format($rekon->saldo_akhir_bank, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($rekon->saldo_sistem, 0, ',', '.') }}</td>
                            <td class="text-end {{ $rekon->selisih == 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                Rp {{ number_format(abs($rekon->selisih), 0, ',', '.') }}
                                @if($rekon->selisih != 0)
                                    <i class="bi bi-exclamation-triangle"></i>
                                @else
                                    <i class="bi bi-check-circle"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $rekon->total_matched }}</span>
                                /
                                <span class="badge bg-secondary">{{ $rekon->total_mutasi }}</span>
                            </td>
                            <td>
                                @if($rekon->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($rekon->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @elseif($rekon->status == 'in_progress')
                                    <span class="badge bg-warning">In Progress</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('rekonsiliasi.show', $rekon) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($rekon->status == 'draft' || $rekon->status == 'in_progress')
                                    <a href="{{ route('rekonsiliasi.process', $rekon) }}" class="btn btn-outline-primary" title="Proses">
                                        <i class="bi bi-gear"></i>
                                    </a>
                                    @endif
                                    @if($rekon->status == 'completed' || $rekon->status == 'approved')
                                    <a href="{{ route('rekonsiliasi.report', $rekon) }}" class="btn btn-outline-success" title="Laporan">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    @endif
                                    @if($rekon->status == 'draft')
                                    <form action="{{ route('rekonsiliasi.destroy', $rekon) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus rekonsiliasi ini?')">
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
                                Belum ada data rekonsiliasi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($rekonsiliasi->hasPages())
        <div class="card-footer">
            {{ $rekonsiliasi->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
