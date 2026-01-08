@extends('layouts.app')

@section('title', 'Manajemen Menu')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Manajemen Menu</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Menu</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Menu
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-list text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Menu</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Menu Aktif</h6>
                            <h3 class="mb-0">{{ $stats['aktif'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-folder text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Parent Menu</h6>
                            <h3 class="mb-0">{{ $stats['parent'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-file-text text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Sub Menu</h6>
                            <h3 class="mb-0">{{ $stats['child'] }}</h3>
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
        <!-- Menu Table -->
        <div class="col-lg-8">
            <!-- Filter -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.menus.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Cari</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama menu..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Parent</label>
                            <select name="parent" class="form-select">
                                <option value="">Semua</option>
                                <option value="root" {{ request('parent') == 'root' ? 'selected' : '' }}>Root Menu</option>
                                @foreach($parentMenus as $parent)
                                    <option value="{{ $parent->hashid }}" {{ request('parent') == $parent->hashid ? 'selected' : '' }}>
                                        {{ $parent->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Menus Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4" width="50">#</th>
                                    <th>Menu</th>
                                    <th>Route/URL</th>
                                    <th class="text-center">Urutan</th>
                                    <th class="text-center">Roles</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menus as $index => $menu)
                                    <tr>
                                        <td class="px-4">{{ $menus->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($menu->icon)
                                                    <i class="{{ $menu->icon }} me-2 text-primary"></i>
                                                @endif
                                                <div>
                                                    @if($menu->parent)
                                                        <small class="text-muted">{{ $menu->parent->nama }} / </small>
                                                    @endif
                                                    <strong>{{ $menu->nama }}</strong>
                                                    @if($menu->is_divider)
                                                        <span class="badge bg-secondary ms-1">Divider</span>
                                                    @endif
                                                    @if($menu->badge_text)
                                                        <span class="badge bg-{{ $menu->badge_color ?? 'primary' }} ms-1">{{ $menu->badge_text }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($menu->route_name)
                                                <code>{{ $menu->route_name }}</code>
                                            @elseif($menu->getRawOriginal('url'))
                                                <small class="text-muted">{{ $menu->getRawOriginal('url') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $menu->urutan }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ $menu->roles->count() }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $menu->status_badge }}">
                                                {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus menu ini?')">
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
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Tidak ada data menu
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($menus->hasPages())
                    <div class="card-footer bg-white">
                        {{ $menus->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Menu Preview -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye me-2"></i>Preview Sidebar
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="sidebar-preview bg-dark text-white p-3" style="min-height: 400px;">
                        <ul class="nav flex-column">
                            @foreach($menuTree as $menu)
                                <li class="nav-item">
                                    @if($menu->children->count() > 0)
                                        <a class="nav-link text-white d-flex align-items-center" href="#">
                                            @if($menu->icon)<i class="{{ $menu->icon }} me-2"></i>@endif
                                            {{ $menu->nama }}
                                            <i class="bi bi-chevron-down ms-auto"></i>
                                        </a>
                                        <ul class="nav flex-column ms-3">
                                            @foreach($menu->children as $child)
                                                <li class="nav-item">
                                                    <a class="nav-link text-white-50 small" href="#">
                                                        @if($child->icon)<i class="{{ $child->icon }} me-2"></i>@endif
                                                        {{ $child->nama }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <a class="nav-link text-white" href="#">
                                            @if($menu->icon)<i class="{{ $menu->icon }} me-2"></i>@endif
                                            {{ $menu->nama }}
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
