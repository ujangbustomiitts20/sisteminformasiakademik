@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Tambah Role Baru</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Role</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        
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
                        <div class="mb-3">
                            <label class="form-label">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                   value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                   value="{{ old('slug') }}" placeholder="Auto-generate jika kosong">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Digunakan untuk identifikasi sistem</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Warna Badge</label>
                            <select name="warna" class="form-select">
                                @foreach($warna as $key => $label)
                                    <option value="{{ $key }}" {{ old('warna', 'primary') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', 0) }}" min="0">
                        </div>

                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Role Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Simpan Role
                    </button>
                </div>
            </div>

            <!-- Menus (PRIORITAS UTAMA) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 border-primary" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list me-2"></i>Akses Menu <span class="badge bg-warning text-dark ms-2">WAJIB</span>
                        </h5>
                        <button type="button" class="btn btn-sm btn-light" onclick="toggleAllMenus()">
                            Toggle All
                        </button>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Pilih menu = Otomatis dapat akses halaman + permission</strong><br>
                            Contoh: Pilih "Calon Mahasiswa" → User bisa lihat, tambah, edit, hapus data calon mahasiswa.
                        </div>
                        @foreach($menus as $menu)
                            <div class="mb-2">
                                <div class="form-check">
                                    <input type="checkbox" name="menus[]" value="{{ $menu->id }}" 
                                           class="form-check-input menu-checkbox menu-parent" id="menu_{{ $menu->id }}"
                                           data-parent="{{ $menu->id }}"
                                           {{ in_array($menu->id, old('menus', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="menu_{{ $menu->id }}">
                                        @if($menu->icon)<i class="{{ $menu->icon }} me-1"></i>@endif
                                        {{ $menu->nama }}
                                    </label>
                                </div>
                                @if($menu->children->count() > 0)
                                    <div class="ms-4 mt-1">
                                        @foreach($menu->children as $child)
                                            <div class="form-check">
                                                <input type="checkbox" name="menus[]" value="{{ $child->id }}" 
                                                       class="form-check-input menu-checkbox menu-child" id="menu_{{ $child->id }}"
                                                       data-parent="{{ $menu->id }}"
                                                       {{ in_array($child->id, old('menus', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="menu_{{ $child->id }}">
                                                    @if($child->icon)<i class="{{ $child->icon }} me-1"></i>@endif
                                                    {{ $child->nama }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Permissions (OPSIONAL - Collapsed by default) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <a class="text-decoration-none d-flex justify-content-between align-items-center" 
                           data-bs-toggle="collapse" href="#permissionSection" role="button" aria-expanded="false">
                            <h5 class="card-title mb-0 text-secondary">
                                <i class="bi bi-key me-2"></i>Permission Tambahan
                                <span class="badge bg-secondary ms-2">OPSIONAL</span>
                            </h5>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                    </div>
                    <div class="collapse" id="permissionSection">
                        <div class="card-body border-top" style="max-height: 600px; overflow-y: auto;">
                            <div class="alert alert-warning small mb-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                <strong>Untuk pengguna tingkat lanjut!</strong><br>
                                Permission sudah otomatis dari menu yang dipilih. Gunakan ini hanya jika ingin membatasi aksi tertentu.
                            </div>
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllPermissions()">
                                    Toggle All
                                </button>
                            </div>
                            @foreach($permissions as $grup => $items)
                                <div class="mb-3">
                                    <h6 class="text-uppercase text-muted small mb-2">
                                        <i class="bi bi-folder me-1"></i>{{ \App\Models\Permission::GRUP[$grup] ?? $grup }}
                                    </h6>
                                    @foreach($items as $permission)
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                   class="form-check-input permission-checkbox" id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->nama }}
                                                <br><small class="text-muted">{{ $permission->slug }}</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleAllPermissions() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

function toggleAllMenus() {
    const checkboxes = document.querySelectorAll('.menu-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

// Auto-check parent when child is checked
document.querySelectorAll('.menu-child').forEach(child => {
    child.addEventListener('change', function() {
        if (this.checked) {
            const parentId = this.dataset.parent;
            const parent = document.querySelector(`.menu-parent[data-parent="${parentId}"]`);
            if (parent) parent.checked = true;
        }
    });
});

// Auto-uncheck children when parent is unchecked
document.querySelectorAll('.menu-parent').forEach(parent => {
    parent.addEventListener('change', function() {
        if (!this.checked) {
            const parentId = this.dataset.parent;
            document.querySelectorAll(`.menu-child[data-parent="${parentId}"]`).forEach(child => {
                child.checked = false;
            });
        }
    });
});
</script>
@endsection
