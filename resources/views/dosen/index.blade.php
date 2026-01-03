@extends('layouts.app')

@section('title', 'Kelola Dosen')

@section('content')
<div class="page-title">
    <h4>Kelola Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Dosen</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge me-2"></i>Daftar Dosen</span>
        <a href="{{ route('dosen.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Dosen
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('dosen.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIDN atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="program_studi" class="form-select">
                        <option value="">-- Semua Program Studi --</option>
                        @foreach($programStudi as $ps)
                        <option value="{{ $ps->id }}" {{ request('program_studi') == $ps->id ? 'selected' : '' }}>{{ $ps->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('dosen.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th width="120">NIDN</th>
                        <th>Nama Dosen</th>
                        <th>Program Studi</th>
                        <th width="100">Status</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosen as $index => $d)
                    <tr>
                        <td>{{ $dosen->firstItem() + $index }}</td>
                        <td><code>{{ $d->nidn }}</code></td>
                        <td>
                            <strong>{{ $d->nama }}</strong>
                            @if($d->email)
                            <br><small class="text-muted">{{ $d->email }}</small>
                            @endif
                        </td>
                        <td>{{ $d->programStudi->nama ?? '-' }}</td>
                        <td>
                            @if($d->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('dosen.show', $d) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('kepegawaian.index', $d) }}" class="btn btn-outline-primary" title="Kepegawaian">
                                    <i class="bi bi-person-badge"></i>
                                </a>
                                <a href="{{ route('dosen.edit', $d) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('dosen.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dosen ini?')">
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
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-person-badge text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data dosen</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($dosen->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $dosen->firstItem() }} - {{ $dosen->lastItem() }} dari {{ $dosen->total() }} data
            </div>
            {{ $dosen->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
