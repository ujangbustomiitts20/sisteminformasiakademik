@extends('layouts.app')

@section('title', 'Detail Role: ' . $role->nama)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <span class="badge bg-{{ $role->warna }} me-2">
                    <i class="bi bi-shield-check"></i>
                </span>
                {{ $role->nama }}
                @if($role->is_system)
                    <span class="badge bg-secondary fs-6">Sistem</span>
                @endif
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Role</a></li>
                    <li class="breadcrumb-item active">{{ $role->nama }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Role Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informasi Role
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="120">Nama</td>
                            <td><strong>{{ $role->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Slug</td>
                            <td><code>{{ $role->slug }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Deskripsi</td>
                            <td>{{ $role->deskripsi ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Warna</td>
                            <td>
                                <span class="badge bg-{{ $role->warna }}">{{ $role->warna }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($role->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe</td>
                            <td>
                                @if($role->is_system)
                                    <span class="badge bg-warning text-dark">Role Sistem</span>
                                @else
                                    <span class="badge bg-info">Role Custom</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Urutan</td>
                            <td>{{ $role->urutan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat</td>
                            <td>{{ $role->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <h2 class="mb-0 text-primary">{{ $role->users->count() }}</h2>
                            <small class="text-muted">Users</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <h2 class="mb-0 text-success">{{ $role->permissions->count() }}</h2>
                            <small class="text-muted">Permissions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-key me-2"></i>Permissions ({{ $role->permissions->count() }})
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @php
                        $groupedPermissions = $role->permissions->groupBy('grup');
                    @endphp
                    
                    @forelse($groupedPermissions as $grup => $permissions)
                        <div class="mb-3">
                            <h6 class="text-uppercase text-muted small mb-2">
                                <i class="bi bi-folder me-1"></i>{{ \App\Models\Permission::GRUP[$grup] ?? $grup }}
                                <span class="badge bg-secondary">{{ $permissions->count() }}</span>
                            </h6>
                            @foreach($permissions as $permission)
                                <div class="d-flex align-items-center py-1">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <span>{{ $permission->nama }}</span>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-key fs-1 d-block mb-2"></i>
                            Tidak ada permission
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Menus -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list me-2"></i>Akses Menu ({{ $role->menus->count() }})
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @php
                        $rootMenus = $role->menus->whereNull('parent_id');
                        $childMenus = $role->menus->whereNotNull('parent_id')->groupBy('parent_id');
                    @endphp
                    
                    @forelse($rootMenus as $menu)
                        <div class="mb-2">
                            <div class="d-flex align-items-center py-1">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                @if($menu->icon)<i class="{{ $menu->icon }} me-1"></i>@endif
                                <strong>{{ $menu->nama }}</strong>
                            </div>
                            @if(isset($childMenus[$menu->id]))
                                <div class="ms-4">
                                    @foreach($childMenus[$menu->id] as $child)
                                        <div class="d-flex align-items-center py-1">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            @if($child->icon)<i class="{{ $child->icon }} me-1"></i>@endif
                                            <span>{{ $child->nama }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-list fs-1 d-block mb-2"></i>
                            Tidak ada akses menu
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Users with this role -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-people me-2"></i>Users dengan Role Ini ({{ $role->users->count() }})
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th class="text-center">Primary</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($role->users->take(20) as $index => $user)
                            <tr>
                                <td class="px-4">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    <br><small class="text-muted">Legacy: {{ $user->role }}</small>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td class="text-center">
                                    @if($user->pivot->is_primary)
                                        <span class="badge bg-primary">Primary</span>
                                    @else
                                        <span class="badge bg-secondary">Secondary</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('user.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    Tidak ada user dengan role ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($role->users->count() > 20)
                <div class="card-footer bg-white text-center">
                    <small class="text-muted">Menampilkan 20 dari {{ $role->users->count() }} users</small>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
