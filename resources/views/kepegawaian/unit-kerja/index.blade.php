@extends('layouts.app')

@section('title', 'Kelola Unit Kerja')

@section('content')
<div class="page-title">
    <h4>Kelola Unit Kerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Unit Kerja</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-building me-2"></i>Daftar Unit Kerja</span>
        <a href="{{ route('kepegawaian.unit-kerja.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Unit Kerja
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.unit-kerja.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('kepegawaian.unit-kerja.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th width="100">Kode</th>
                        <th>Nama Unit Kerja</th>
                        <th>Induk</th>
                        <th width="100">Jml Pegawai</th>
                        <th width="80">Status</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unitKerja as $index => $unit)
                    <tr>
                        <td>{{ $unitKerja->firstItem() + $index }}</td>
                        <td><code>{{ $unit->kode }}</code></td>
                        <td>
                            <strong>{{ $unit->nama }}</strong>
                            @if($unit->deskripsi)
                            <br><small class="text-muted">{{ Str::limit($unit->deskripsi, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $unit->parent->nama ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $unit->pegawai_count }}</span>
                        </td>
                        <td>
                            @if($unit->is_active)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.unit-kerja.edit', $unit) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kepegawaian.unit-kerja.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus unit kerja ini?')">
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
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-building text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data unit kerja</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($unitKerja->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $unitKerja->firstItem() }} - {{ $unitKerja->lastItem() }} dari {{ $unitKerja->total() }} data
            </div>
            {{ $unitKerja->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
