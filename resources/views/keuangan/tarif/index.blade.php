@extends('layouts.app')

@section('title', 'Kelola Tarif')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Tarif</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tarif</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('tarif.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Tarif
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari tarif..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach(\App\Models\Tarif::JENIS as $key => $label)
                            <option value="{{ $key }}" {{ request('jenis') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
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
                            <th>Nama Tarif</th>
                            <th>Jenis</th>
                            <th>Program Studi</th>
                            <th>Angkatan</th>
                            <th>Periode</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tarif as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->nama_tarif }}</strong>
                                @if($item->keterangan)
                                    <br><small class="text-muted">{{ Str::limit($item->keterangan, 50) }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $item->jenis }}</span></td>
                            <td>{{ $item->programStudi->nama ?? 'Semua' }}</td>
                            <td>{{ $item->angkatan ?? 'Semua' }}</td>
                            <td>{{ $item->periode }}</td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($item->nominal, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-center">
                                @if($item->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('tarif.edit', $item) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('tarif.toggle-status', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $item->is_active ? 'warning' : 'success' }}" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-{{ $item->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('tarif.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus tarif ini?')">
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
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada data tarif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tarif->hasPages())
        <div class="card-footer bg-white">
            {{ $tarif->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
