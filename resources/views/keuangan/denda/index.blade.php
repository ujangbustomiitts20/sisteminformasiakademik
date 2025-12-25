@extends('layouts.app')

@section('title', 'Pengaturan Denda')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pengaturan Denda</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengaturan Denda</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pengaturan-denda.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Pengaturan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Denda</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th>Periode</th>
                            <th>Grace Period</th>
                            <th>Maks. Denda</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengaturan as $item)
                        <tr>
                            <td><strong>{{ $item->nama }}</strong></td>
                            <td><span class="badge bg-info">{{ $item->tipe }}</span></td>
                            <td>{{ $item->nilai_label }}</td>
                            <td>{{ \App\Models\PengaturanDenda::PERIODE[$item->periode] ?? $item->periode }}</td>
                            <td>{{ $item->grace_period ?? 0 }} hari</td>
                            <td>
                                @if($item->maksimal_denda)
                                    Rp {{ number_format($item->maksimal_denda, 0, ',', '.') }}
                                @else
                                    Tidak Terbatas
                                @endif
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
                                    <a href="{{ route('pengaturan-denda.edit', $item) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('pengaturan-denda.toggle-status', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $item->is_active ? 'warning' : 'success' }}" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-{{ $item->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('pengaturan-denda.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pengaturan ini?')">
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
                                Belum ada pengaturan denda
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pengaturan->hasPages())
        <div class="card-footer bg-white">
            {{ $pengaturan->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
