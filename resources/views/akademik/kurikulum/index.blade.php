@extends('layouts.app')

@section('title', 'Kurikulum')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Manajemen Kurikulum</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kurikulum</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('kurikulum.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kurikulum
    </a>
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

<div class="card">
    <div class="card-header">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="prodi" class="form-select form-select-sm">
                    <option value="">-- Semua Prodi --</option>
                    @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                        {{ $prodi->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Status --</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kode/nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">
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
                        <th>Kode</th>
                        <th>Nama Kurikulum</th>
                        <th>Program Studi</th>
                        <th>Tahun</th>
                        <th class="text-center">SKS Wajib</th>
                        <th class="text-center">SKS Pilihan</th>
                        <th class="text-center">Total Lulus</th>
                        <th class="text-center">Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurikulums as $kurikulum)
                    <tr>
                        <td><code>{{ $kurikulum->kode }}</code></td>
                        <td>
                            <a href="{{ route('kurikulum.show', $kurikulum) }}" class="text-decoration-none fw-medium">
                                {{ $kurikulum->nama }}
                            </a>
                        </td>
                        <td>{{ $kurikulum->programStudi->nama }}</td>
                        <td>{{ $kurikulum->tahun_mulai }}{{ $kurikulum->tahun_selesai ? ' - ' . $kurikulum->tahun_selesai : ' - sekarang' }}</td>
                        <td class="text-center">{{ $kurikulum->total_sks_wajib }}</td>
                        <td class="text-center">{{ $kurikulum->total_sks_pilihan }}</td>
                        <td class="text-center"><strong>{{ $kurikulum->total_sks_lulus }}</strong></td>
                        <td class="text-center">
                            @if($kurikulum->is_aktif)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kurikulum.show', $kurikulum) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('kurikulum.mata-kuliah', $kurikulum) }}" class="btn btn-outline-primary" title="Kelola MK">
                                    <i class="bi bi-book"></i>
                                </a>
                                <a href="{{ route('kurikulum.edit', $kurikulum) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$kurikulum->is_aktif)
                                <form action="{{ route('kurikulum.set-aktif', $kurikulum) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Set Aktif">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Belum ada data kurikulum</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($kurikulums->hasPages())
    <div class="card-footer">
        {{ $kurikulums->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
