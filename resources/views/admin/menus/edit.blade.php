@extends('layouts.app')

@section('title', 'Edit Menu - ' . $menu->nama)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Menu: {{ $menu->nama }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menu</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nama Menu <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                           value="{{ old('nama', $menu->nama) }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Urutan</label>
                                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $menu->urutan) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Parent Menu</label>
                            <select name="parent_id" class="form-select">
                                <option value="">-- Root Menu --</option>
                                @foreach($parentMenus as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Icon</label>
                                    <select name="icon" class="form-select" id="iconSelect">
                                        <option value="">-- Pilih Icon --</option>
                                        @foreach($icons as $icon => $label)
                                            <option value="{{ $icon }}" {{ old('icon', $menu->icon) == $icon ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2">
                                        Preview: <span id="iconPreview"><i class="{{ $menu->icon ?? 'bi-circle' }}"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Permission</label>
                                    <select name="permission_slug" class="form-select">
                                        <option value="">-- Tidak Ada --</option>
                                        @foreach($permissions as $permission)
                                            <option value="{{ $permission->slug }}" {{ old('permission_slug', $menu->permission_slug) == $permission->slug ? 'selected' : '' }}>
                                                [{{ $permission->grup }}] {{ $permission->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Route Name</label>
                            <input type="text" name="route_name" class="form-control @error('route_name') is-invalid @enderror" 
                                   value="{{ old('route_name', $menu->route_name) }}" placeholder="contoh: mahasiswa.index">
                            @error('route_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">URL Manual</label>
                            <input type="text" name="url" class="form-control" 
                                   value="{{ old('url', $menu->getRawOriginal('url')) }}" placeholder="contoh: /admin/mahasiswa">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Badge Text</label>
                                    <input type="text" name="badge_text" class="form-control" 
                                           value="{{ old('badge_text', $menu->badge_text) }}" placeholder="contoh: New">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Badge Color</label>
                                    <select name="badge_color" class="form-select">
                                        <option value="">-- Pilih Warna --</option>
                                        @foreach($badgeColors as $color => $label)
                                            <option value="{{ $color }}" {{ old('badge_color', $menu->badge_color) == $color ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                           {{ old('is_active', $menu->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Menu Aktif</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="is_divider" class="form-check-input" id="is_divider" 
                                           {{ old('is_divider', $menu->is_divider) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_divider">Sebagai Divider</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles Assignment -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-check me-2"></i>Akses Role
                            <span class="badge bg-primary ms-1">{{ count($menuRoles) }}</span>
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllRoles()">
                            Toggle All
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Pilih role yang dapat mengakses menu ini:</p>
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                               class="form-check-input role-checkbox" id="role_{{ $role->id }}"
                                               {{ in_array($role->id, old('roles', $menuRoles)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            <span class="badge bg-{{ $role->warna }} me-1">
                                                <i class="bi bi-shield-check"></i>
                                            </span>
                                            {{ $role->nama }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($menu->children->count() > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-diagram-3 me-2"></i>Sub Menu ({{ $menu->children->count() }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach($menu->children as $child)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($child->icon)<i class="{{ $child->icon }} me-2"></i>@endif
                                            {{ $child->nama }}
                                        </div>
                                        <a href="{{ route('admin.menus.edit', $child) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Update Menu
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleAllRoles() {
    const checkboxes = document.querySelectorAll('.role-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

document.getElementById('iconSelect')?.addEventListener('change', function() {
    const preview = document.getElementById('iconPreview');
    preview.innerHTML = this.value ? `<i class="${this.value}"></i>` : '<i class="bi-circle"></i>';
});
</script>
@endsection
