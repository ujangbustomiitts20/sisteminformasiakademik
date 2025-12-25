@extends('layouts.app')

@section('title', 'Kelola Beasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Beasiswa</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Beasiswa</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('beasiswa.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Beasiswa
        </a>
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

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari beasiswa..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach(\App\Models\Beasiswa::JENIS as $key => $label)
                            <option value="{{ $key }}" {{ request('jenis') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
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
                            <th>Nama Beasiswa</th>
                            <th>Jenis</th>
                            <th>Potongan</th>
                            <th>Kuota</th>
                            <th class="text-center">Penerima</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($beasiswa as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->nama }}</strong>
                                <br><small class="text-muted">{{ $item->kode }}</small>
                                @if($item->sumber_dana)
                                    <br><small class="text-muted">Sumber: {{ $item->sumber_dana }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $jenisColor = match($item->jenis) {
                                        'Beasiswa' => 'success',
                                        'Potongan' => 'info',
                                        'Keringanan' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $jenisColor }}">{{ $item->jenis }}</span>
                            </td>
                            <td>
                                @if($item->tipe_potongan === 'Persen')
                                    {{ $item->nilai_potongan }}%
                                @else
                                    Rp {{ number_format($item->nilai_potongan, 0, ',', '.') }}
                                @endif
                            </td>
                            <td>{{ $item->kuota ?? 'Tidak Terbatas' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $item->penerima_aktif_count }}</span>
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
                                    <a href="{{ route('beasiswa.show', $item) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('beasiswa.edit', $item) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('beasiswa.toggle-status', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $item->is_active ? 'warning' : 'success' }}" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-{{ $item->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('beasiswa.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus beasiswa ini?')">
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
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada data beasiswa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($beasiswa->hasPages())
        <div class="card-footer bg-white">
            {{ $beasiswa->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
