@extends('layouts.app')

@section('title', 'Nama Jabatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Nama Jabatan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Nama Jabatan</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Jabatan
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small>Total Jabatan</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-success text-white">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $stats['aktif'] }}</h3>
                    <small>Aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-danger text-white">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $stats['pimpinan'] }}</h3>
                    <small>Pimpinan</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-info text-white">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $stats['akademik'] }}</h3>
                    <small>Akademik</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-warning text-dark">
                <div class="card-body text-center py-3">
                    <h3 class="mb-0">{{ $stats['keuangan'] }}</h3>
                    <small>Keuangan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Filter -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $key => $label)
                            <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, kode..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('admin.nama-jabatan.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Kode</th>
                            <th>Nama Jabatan</th>
                            <th>Kategori</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jabatans as $jabatan)
                        <tr>
                            <td>{{ $jabatan->urutan }}</td>
                            <td><code>{{ $jabatan->kode }}</code></td>
                            <td>
                                <strong>{{ $jabatan->nama }}</strong>
                                @if($jabatan->nama_singkat)
                                    <br><small class="text-muted">{{ $jabatan->nama_singkat }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $jabatan->kategori_badge }}">
                                    {{ $jabatan->kategori_label }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $jabatan->level_label }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $jabatan->status_badge }}">
                                    {{ $jabatan->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $jabatan->hashid }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.nama-jabatan.toggle-status', $jabatan->hashid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $jabatan->aktif ? 'warning' : 'success' }}" title="{{ $jabatan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $jabatan->aktif ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.nama-jabatan.destroy', $jabatan->hashid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus jabatan ini?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal{{ $jabatan->hashid }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.nama-jabatan.update', $jabatan->hashid) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Nama Jabatan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Kode <span class="text-danger">*</span></label>
                                                <input type="text" name="kode" class="form-control" value="{{ $jabatan->kode }}" required>
                                                <small class="text-muted">Kode unik untuk referensi di sistem</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                                                <input type="text" name="nama" class="form-control" value="{{ $jabatan->nama }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Singkat</label>
                                                <input type="text" name="nama_singkat" class="form-control" value="{{ $jabatan->nama_singkat }}">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                                        <select name="kategori" class="form-select" required>
                                                            @foreach($kategoris as $key => $label)
                                                                <option value="{{ $key }}" {{ $jabatan->kategori == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Level</label>
                                                        <select name="level" class="form-select">
                                                            @foreach($levels as $key => $label)
                                                                <option value="{{ $key }}" {{ $jabatan->level == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Urutan</label>
                                                <input type="number" name="urutan" class="form-control" value="{{ $jabatan->urutan }}" min="0">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" rows="2">{{ $jabatan->deskripsi }}</textarea>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktifEdit{{ $jabatan->hashid }}" {{ $jabatan->aktif ? 'checked' : '' }}>
                                                <label class="form-check-label" for="aktifEdit{{ $jabatan->hashid }}">Aktif</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save me-1"></i> Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-briefcase fs-1 d-block mb-2"></i>
                                <p class="mb-0">Belum ada nama jabatan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $jabatans->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.nama-jabatan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Nama Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="kode" class="form-control" required placeholder="Contoh: rektor, dekan_fti, kaprodi_ti">
                        <small class="text-muted">Kode unik untuk referensi di sistem</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required placeholder="Contoh: Rektor, Dekan Fakultas Teknologi Industri">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Singkat</label>
                        <input type="text" name="nama_singkat" class="form-control" placeholder="Contoh: Dekan FTI">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategoris as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Level</label>
                                <select name="level" class="form-select">
                                    @foreach($levels as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" class="form-control" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi jabatan (opsional)"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktifAdd" checked>
                        <label class="form-check-label" for="aktifAdd">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
