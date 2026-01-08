@extends('layouts.app')

@section('title', 'Detail Menu: ' . $menu->nama)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                @if($menu->icon)<i class="{{ $menu->icon }} me-2 text-primary"></i>@endif
                {{ $menu->nama }}
                @if($menu->is_divider)
                    <span class="badge bg-secondary">Divider</span>
                @endif
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menu</a></li>
                    <li class="breadcrumb-item active">{{ $menu->nama }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Menu Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informasi Menu
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="150">Nama</td>
                            <td><strong>{{ $menu->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Icon</td>
                            <td>
                                @if($menu->icon)
                                    <i class="{{ $menu->icon }} me-2"></i>
                                    <code>{{ $menu->icon }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Parent</td>
                            <td>
                                @if($menu->parent)
                                    <i class="{{ $menu->parent->icon ?? 'bi-folder' }} me-1"></i>
                                    {{ $menu->parent->nama }}
                                @else
                                    <span class="badge bg-info">Root Menu</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Route Name</td>
                            <td>
                                @if($menu->route_name)
                                    <code>{{ $menu->route_name }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">URL</td>
                            <td>
                                @if($menu->getRawOriginal('url'))
                                    <a href="{{ $menu->getRawOriginal('url') }}" target="_blank">
                                        {{ $menu->getRawOriginal('url') }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Permission</td>
                            <td>
                                @if($menu->permission_slug)
                                    <code>{{ $menu->permission_slug }}</code>
                                @else
                                    <span class="text-muted">Tidak ada (publik)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Urutan</td>
                            <td>{{ $menu->urutan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($menu->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Badge</td>
                            <td>
                                @if($menu->badge_text)
                                    <span class="badge bg-{{ $menu->badge_color ?? 'primary' }}">{{ $menu->badge_text }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat</td>
                            <td>{{ $menu->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Sub Menus -->
            @if($menu->children->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-nested me-2"></i>Sub Menu ({{ $menu->children->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($menu->children as $child)
                            <a href="{{ route('admin.menus.show', $child) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    @if($child->icon)<i class="{{ $child->icon }} me-2"></i>@endif
                                    {{ $child->nama }}
                                </span>
                                <span>
                                    @if($child->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Roles -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2"></i>Role dengan Akses Menu Ini ({{ $menu->roles->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($menu->roles->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($menu->roles as $role)
                                <a href="{{ route('admin.roles.show', $role) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="badge bg-{{ $role->warna }} me-2">
                                            <i class="bi bi-shield-check"></i>
                                        </span>
                                        {{ $role->nama }}
                                        @if($role->is_system)
                                            <span class="badge bg-secondary ms-1">Sistem</span>
                                        @endif
                                    </span>
                                    <span>
                                        @if($role->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-shield fs-1 d-block mb-2"></i>
                            Tidak ada role yang memiliki akses ke menu ini
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye me-2"></i>Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="bg-dark text-white rounded p-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link text-white d-flex align-items-center {{ $menu->children->count() > 0 ? '' : 'active' }}" href="#">
                                    @if($menu->icon)<i class="{{ $menu->icon }} me-2"></i>@endif
                                    {{ $menu->nama }}
                                    @if($menu->badge_text)
                                        <span class="badge bg-{{ $menu->badge_color ?? 'primary' }} ms-auto">{{ $menu->badge_text }}</span>
                                    @endif
                                    @if($menu->children->count() > 0)
                                        <i class="bi bi-chevron-down ms-auto"></i>
                                    @endif
                                </a>
                                @if($menu->children->count() > 0)
                                    <ul class="nav flex-column ms-3">
                                        @foreach($menu->children as $child)
                                            <li class="nav-item">
                                                <a class="nav-link text-white-50 small" href="#">
                                                    @if($child->icon)<i class="{{ $child->icon }} me-2"></i>@endif
                                                    {{ $child->nama }}
                                                    @if($child->badge_text)
                                                        <span class="badge bg-{{ $child->badge_color ?? 'primary' }} ms-2">{{ $child->badge_text }}</span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
