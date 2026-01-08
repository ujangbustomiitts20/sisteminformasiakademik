@extends('layouts.app')

@section('title', 'Manajemen Permission')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Manajemen Permission</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Permission</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="bi bi-magic me-1"></i> Generate
            </button>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Permission
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-key text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Permission</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-folder text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Grup Permission</h6>
                            <h3 class="mb-0">{{ $stats['groups'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
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

    <div class="row">
        <!-- Permissions Table -->
        <div class="col-lg-8">
            <!-- Filter -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.permissions.index') }}" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Cari</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama atau slug..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Grup</label>
                            <select name="grup" class="form-select">
                                <option value="">Semua Grup</option>
                                @foreach(\App\Models\Permission::GRUP as $key => $label)
                                    <option value="{{ $key }}" {{ request('grup') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Permissions Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4" width="50">#</th>
                                    <th>Permission</th>
                                    <th>Slug</th>
                                    <th>Grup</th>
                                    <th class="text-center">Roles</th>
                                    <th class="text-center" width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $index => $permission)
                                    <tr>
                                        <td class="px-4">{{ $permissions->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $permission->nama }}</strong>
                                            @if($permission->deskripsi)
                                                <br><small class="text-muted">{{ $permission->deskripsi }}</small>
                                            @endif
                                        </td>
                                        <td><code>{{ $permission->slug }}</code></td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ \App\Models\Permission::GRUP[$permission->grup] ?? $permission->grup }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $permission->roles_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus permission ini?')">
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
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Tidak ada data permission
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($permissions->hasPages())
                    <div class="card-footer bg-white">
                        {{ $permissions->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Permission Groups Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Ringkasan per Grup
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($groupedPermissions as $grup => $items)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-folder me-2 text-primary"></i>
                                    {{ \App\Models\Permission::GRUP[$grup] ?? $grup }}
                                </span>
                                <span class="badge bg-primary rounded-pill">{{ $items->count() }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Permission Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.permissions.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Permission untuk Modul</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Ini akan membuat permission standar (lihat, tambah, edit, hapus, export, import) untuk modul yang dipilih.</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Modul <span class="text-danger">*</span></label>
                        <input type="text" name="module" class="form-control" required placeholder="contoh: pegawai, jadwal_kuliah">
                        <small class="text-muted">Gunakan snake_case tanpa spasi</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Grup <span class="text-danger">*</span></label>
                        <select name="grup" class="form-select" required>
                            @foreach(\App\Models\Permission::GRUP as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-magic me-1"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
