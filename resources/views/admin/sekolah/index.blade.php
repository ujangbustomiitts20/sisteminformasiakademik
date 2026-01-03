@extends('layouts.app')

@section('title', 'Master Sekolah')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Master Sekolah</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Sekolah</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('sekolah.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Sekolah
    </a>
</div>

<!-- Alert -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Statistik -->
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        <small>Total Sekolah</small>
                    </div>
                    <i class="bi bi-building fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ number_format($stats['sma'] + $stats['ma']) }}</h3>
                        <small>SMA/MA</small>
                    </div>
                    <i class="bi bi-mortarboard fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ number_format($stats['smk'] + $stats['mak']) }}</h3>
                        <small>SMK/MAK</small>
                    </div>
                    <i class="bi bi-tools fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ number_format($stats['negeri']) }}</h3>
                        <small>Negeri</small>
                    </div>
                    <i class="bi bi-bank fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('sekolah.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Cari</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Nama/NPSN..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Jenjang</label>
                    <select name="jenjang" class="form-select">
                        <option value="">Semua</option>
                        @foreach(['SMA', 'SMK', 'MA', 'MAK'] as $j)
                        <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="Negeri" {{ request('status') == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                        <option value="Swasta" {{ request('status') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Provinsi</label>
                    <select name="provinsi_id" id="filter_provinsi" class="form-select">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinsi as $prov)
                        <option value="{{ $prov->id }}" {{ request('provinsi_id') == $prov->id ? 'selected' : '' }}>
                            {{ $prov->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('sekolah.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Data -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Daftar Sekolah</h6>
        <span class="badge bg-secondary">{{ $sekolah->total() }} data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>NPSN</th>
                        <th>Nama Sekolah</th>
                        <th>Jenjang</th>
                        <th>Status</th>
                        <th>Lokasi</th>
                        <th class="text-center">Mahasiswa</th>
                        <th class="text-center">Aktif</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sekolah as $item)
                    <tr>
                        <td>{{ $sekolah->firstItem() + $loop->index }}</td>
                        <td>
                            <span class="font-monospace">{{ $item->npsn ?? '-' }}</span>
                        </td>
                        <td>
                            <strong>{{ $item->nama }}</strong>
                            @if($item->alamat)
                            <br><small class="text-muted">{{ Str::limit($item->alamat, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->jenjang == 'SMA' ? 'primary' : ($item->jenjang == 'SMK' ? 'info' : ($item->jenjang == 'MA' ? 'success' : 'warning')) }}">
                                {{ $item->jenjang }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->status == 'Negeri' ? 'dark' : 'secondary' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td>
                            @if($item->kabupaten)
                            {{ $item->kabupaten->nama }}
                            @if($item->provinsi)
                            <br><small class="text-muted">{{ $item->provinsi->nama }}</small>
                            @endif
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $item->mahasiswa_count }}</span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('sekolah.toggle-active', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-{{ $item->is_active ? 'success' : 'secondary' }}" 
                                        title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="bi bi-{{ $item->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                </button>
                            </form>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('sekolah.edit', $item) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('sekolah.destroy', $item) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus sekolah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data sekolah
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($sekolah->hasPages())
    <div class="card-footer">
        {{ $sekolah->links() }}
    </div>
    @endif
</div>
@endsection
