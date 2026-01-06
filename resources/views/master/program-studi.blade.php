@extends('layouts.app')

@section('title', 'Master Program Studi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Master Program Studi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Program Studi</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-1"></i>Tambah Program Studi
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

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-mortarboard text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Prodi</h6>
                            <h4 class="mb-0">{{ $programStudi->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-award text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Terakreditasi</h6>
                            <h4 class="mb-0">{{ $programStudi->whereNotNull('akreditasi')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-people text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Mahasiswa</h6>
                            <h4 class="mb-0">{{ $programStudi->sum('mahasiswa_count') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-person-badge text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Dosen</h6>
                            <h4 class="mb-0">{{ $programStudi->sum('dosen_count') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Fakultas</label>
                    <select name="fakultas" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Fakultas</option>
                        @foreach($fakultas as $f)
                        <option value="{{ $f->id }}" {{ request('fakultas') == $f->id ? 'selected' : '' }}>{{ $f->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenjang</label>
                    <select name="jenjang" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Jenjang</option>
                        <option value="D3" {{ request('jenjang') == 'D3' ? 'selected' : '' }}>D3</option>
                        <option value="S1" {{ request('jenjang') == 'S1' ? 'selected' : '' }}>S1</option>
                        <option value="S2" {{ request('jenjang') == 'S2' ? 'selected' : '' }}>S2</option>
                        <option value="S3" {{ request('jenjang') == 'S3' ? 'selected' : '' }}>S3</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Akreditasi</label>
                    <select name="akreditasi" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Akreditasi</option>
                        <option value="A" {{ request('akreditasi') == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ request('akreditasi') == 'B' ? 'selected' : '' }}>B</option>
                        <option value="C" {{ request('akreditasi') == 'C' ? 'selected' : '' }}>C</option>
                        <option value="Unggul" {{ request('akreditasi') == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                        <option value="Baik Sekali" {{ request('akreditasi') == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                        <option value="Baik" {{ request('akreditasi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    @if(request()->hasAny(['fakultas', 'jenjang', 'akreditasi']))
                    <a href="{{ route('program-studi.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg me-1"></i>Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Program Studi Grid -->
    <div class="row g-4">
        @forelse($programStudi as $ps)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        @if($ps->logo)
                        <img src="{{ asset('storage/' . $ps->logo) }}" alt="Logo" class="me-2" style="height: 40px;">
                        @else
                        <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-2">
                            <i class="bi bi-mortarboard text-primary"></i>
                        </div>
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $ps->nama }}</h6>
                            <small class="text-muted">{{ $ps->kode }} - {{ $ps->jenjang ?? 'S1' }}</small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" onclick="showDetail('{{ $ps->hashid }}')">
                                    <i class="bi bi-eye me-2"></i>Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="editProdi('{{ $ps->hashid }}', {{ json_encode($ps) }})">
                                    <i class="bi bi-pencil me-2"></i>Edit
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('program-studi.destroy', $ps->hashid) }}" method="POST" 
                                      onsubmit="return confirm('Yakin hapus program studi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-trash me-2"></i>Hapus
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Fakultas</small>
                        <span>{{ $ps->fakultas->nama ?? '-' }}</span>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <small class="text-muted d-block">Mahasiswa</small>
                            <strong>{{ $ps->mahasiswa_count ?? 0 }}</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Dosen</small>
                            <strong>{{ $ps->dosen_count ?? 0 }}</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Total SKS</small>
                            <strong>{{ $ps->total_sks ?? '-' }}</strong>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Akreditasi</small>
                            @if($ps->akreditasi)
                            <span class="badge bg-{{ $ps->akreditasi_badge }}">{{ $ps->akreditasi }}</span>
                            @else
                            <span class="badge bg-secondary">-</span>
                            @endif
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Gelar Lulusan</small>
                            <span>{{ $ps->gelar_lulusan ?? '-' }}</span>
                        </div>
                    </div>
                    
                    @if($ps->kaprodi)
                    <div class="mb-2">
                        <small class="text-muted d-block">Kaprodi</small>
                        <span>{{ $ps->kaprodi }}</span>
                    </div>
                    @endif
                    
                    <div class="d-flex flex-wrap gap-1">
                        @if($ps->email)
                        <a href="mailto:{{ $ps->email }}" class="badge bg-light text-dark text-decoration-none">
                            <i class="bi bi-envelope me-1"></i>{{ Str::limit($ps->email, 20) }}
                        </a>
                        @endif
                        @if($ps->telepon)
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-telephone me-1"></i>{{ $ps->telepon }}
                        </span>
                        @endif
                    </div>
                </div>
                @if($ps->visi)
                <div class="card-footer bg-transparent">
                    <small class="text-muted">
                        <strong>Visi:</strong> {{ Str::limit($ps->visi, 80) }}
                    </small>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-mortarboard text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Belum ada data program studi</h5>
                    <p class="text-muted">Klik tombol "Tambah Program Studi" untuk menambahkan data.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('program-studi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Tambah Program Studi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#create-umum" type="button">
                                <i class="bi bi-info-circle me-1"></i>Umum
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#create-akademik" type="button">
                                <i class="bi bi-book me-1"></i>Akademik
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#create-kontak" type="button">
                                <i class="bi bi-telephone me-1"></i>Kontak
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#create-visimisi" type="button">
                                <i class="bi bi-bullseye me-1"></i>Visi & Misi
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- Tab Umum -->
                        <div class="tab-pane fade show active" id="create-umum">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Kode <span class="text-danger">*</span></label>
                                    <input type="text" name="kode" class="form-control" required maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama Program Studi <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Singkatan</label>
                                    <input type="text" name="singkatan" class="form-control" maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fakultas <span class="text-danger">*</span></label>
                                    <select name="fakultas_id" class="form-select" required>
                                        <option value="">- Pilih Fakultas -</option>
                                        @foreach($fakultas as $f)
                                        <option value="{{ $f->id }}">{{ $f->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jenjang <span class="text-danger">*</span></label>
                                    <select name="jenjang" class="form-select" required>
                                        <option value="D3">D3</option>
                                        <option value="S1" selected>S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Total SKS</label>
                                    <input type="number" name="total_sks" class="form-control" value="144" min="100" max="200">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kaprodi</label>
                                    <select name="kaprodi" class="form-select">
                                        <option value="">- Pilih Kaprodi -</option>
                                        @foreach($dosen as $d)
                                        <option value="{{ $d->nama }}">{{ $d->nama }} ({{ $d->nidn }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Gelar Lulusan</label>
                                    <input type="text" name="gelar_lulusan" class="form-control" placeholder="S.T., S.Kom., dll">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kuota Mahasiswa</label>
                                    <input type="number" name="kuota" class="form-control" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Berdiri</label>
                                    <input type="date" name="tanggal_berdiri" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SK Pendirian</label>
                                    <input type="text" name="sk_pendirian" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG, GIF. Maks: 2MB</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Akademik -->
                        <div class="tab-pane fade" id="create-akademik">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Akreditasi</label>
                                    <select name="akreditasi" class="form-select">
                                        <option value="">- Pilih -</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="Unggul">Unggul</option>
                                        <option value="Baik Sekali">Baik Sekali</option>
                                        <option value="Baik">Baik</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Akreditasi</label>
                                    <input type="date" name="tanggal_akreditasi" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No SK Akreditasi</label>
                                    <input type="text" name="no_sk_akreditasi" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Kompetensi Lulusan</label>
                                    <textarea name="kompetensi" class="form-control" rows="4" placeholder="Deskripsikan kompetensi yang dimiliki lulusan..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Kontak -->
                        <div class="tab-pane fade" id="create-kontak">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" name="telepon" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Website</label>
                                    <input type="url" name="website" class="form-control" placeholder="https://">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Visi Misi -->
                        <div class="tab-pane fade" id="create-visimisi">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Visi</label>
                                    <textarea name="visi" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Misi</label>
                                    <textarea name="misi" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Program Studi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#edit-umum" type="button">
                                <i class="bi bi-info-circle me-1"></i>Umum
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-akademik" type="button">
                                <i class="bi bi-book me-1"></i>Akademik
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-kontak" type="button">
                                <i class="bi bi-telephone me-1"></i>Kontak
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-visimisi" type="button">
                                <i class="bi bi-bullseye me-1"></i>Visi & Misi
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- Tab Umum -->
                        <div class="tab-pane fade show active" id="edit-umum">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Kode <span class="text-danger">*</span></label>
                                    <input type="text" name="kode" id="edit_kode" class="form-control" required maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama Program Studi <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Singkatan</label>
                                    <input type="text" name="singkatan" id="edit_singkatan" class="form-control" maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fakultas <span class="text-danger">*</span></label>
                                    <select name="fakultas_id" id="edit_fakultas_id" class="form-select" required>
                                        @foreach($fakultas as $f)
                                        <option value="{{ $f->id }}">{{ $f->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jenjang <span class="text-danger">*</span></label>
                                    <select name="jenjang" id="edit_jenjang" class="form-select" required>
                                        <option value="D3">D3</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Total SKS</label>
                                    <input type="number" name="total_sks" id="edit_total_sks" class="form-control" min="100" max="200">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kaprodi</label>
                                    <select name="kaprodi" id="edit_kaprodi" class="form-select">
                                        <option value="">- Pilih Kaprodi -</option>
                                        @foreach($dosen as $d)
                                        <option value="{{ $d->nama }}">{{ $d->nama }} ({{ $d->nidn }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Gelar Lulusan</label>
                                    <input type="text" name="gelar_lulusan" id="edit_gelar_lulusan" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kuota Mahasiswa</label>
                                    <input type="number" name="kuota" id="edit_kuota" class="form-control" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Berdiri</label>
                                    <input type="date" name="tanggal_berdiri" id="edit_tanggal_berdiri" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SK Pendirian</label>
                                    <input type="text" name="sk_pendirian" id="edit_sk_pendirian" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Logo</label>
                                    <div id="edit_logo_preview" class="mb-2"></div>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah logo</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Akademik -->
                        <div class="tab-pane fade" id="edit-akademik">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Akreditasi</label>
                                    <select name="akreditasi" id="edit_akreditasi" class="form-select">
                                        <option value="">- Pilih -</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="Unggul">Unggul</option>
                                        <option value="Baik Sekali">Baik Sekali</option>
                                        <option value="Baik">Baik</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Akreditasi</label>
                                    <input type="date" name="tanggal_akreditasi" id="edit_tanggal_akreditasi" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No SK Akreditasi</label>
                                    <input type="text" name="no_sk_akreditasi" id="edit_no_sk_akreditasi" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Kompetensi Lulusan</label>
                                    <textarea name="kompetensi" id="edit_kompetensi" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Kontak -->
                        <div class="tab-pane fade" id="edit-kontak">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" id="edit_alamat" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" name="telepon" id="edit_telepon" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" id="edit_email" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Website</label>
                                    <input type="url" name="website" id="edit_website" class="form-control" placeholder="https://">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Visi Misi -->
                        <div class="tab-pane fade" id="edit-visimisi">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Visi</label>
                                    <textarea name="visi" id="edit_visi" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Misi</label>
                                    <textarea name="misi" id="edit_misi" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-mortarboard me-2"></i>Detail Program Studi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content loaded via JS -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const prodiData = @json($programStudi->keyBy('hashid'));

function editProdi(hashid, data) {
    document.getElementById('editForm').action = '{{ url("program-studi") }}/' + hashid;
    
    // Tab Umum
    document.getElementById('edit_kode').value = data.kode || '';
    document.getElementById('edit_nama').value = data.nama || '';
    document.getElementById('edit_singkatan').value = data.singkatan || '';
    document.getElementById('edit_fakultas_id').value = data.fakultas_id || '';
    document.getElementById('edit_jenjang').value = data.jenjang || 'S1';
    document.getElementById('edit_total_sks').value = data.total_sks || '';
    document.getElementById('edit_kaprodi').value = data.kaprodi || '';
    document.getElementById('edit_gelar_lulusan').value = data.gelar_lulusan || '';
    document.getElementById('edit_kuota').value = data.kuota || '';
    document.getElementById('edit_tanggal_berdiri').value = data.tanggal_berdiri ? data.tanggal_berdiri.split('T')[0] : '';
    document.getElementById('edit_sk_pendirian').value = data.sk_pendirian || '';
    
    // Tab Akademik
    document.getElementById('edit_akreditasi').value = data.akreditasi || '';
    document.getElementById('edit_tanggal_akreditasi').value = data.tanggal_akreditasi ? data.tanggal_akreditasi.split('T')[0] : '';
    document.getElementById('edit_no_sk_akreditasi').value = data.no_sk_akreditasi || '';
    document.getElementById('edit_kompetensi').value = data.kompetensi || '';
    
    // Tab Kontak
    document.getElementById('edit_alamat').value = data.alamat || '';
    document.getElementById('edit_telepon').value = data.telepon || '';
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_website').value = data.website || '';
    
    // Tab Visi Misi
    document.getElementById('edit_visi').value = data.visi || '';
    document.getElementById('edit_misi').value = data.misi || '';
    
    // Logo preview
    if (data.logo) {
        document.getElementById('edit_logo_preview').innerHTML = '<img src="{{ asset("storage") }}/' + data.logo + '" class="img-thumbnail" style="max-height: 100px;">';
    } else {
        document.getElementById('edit_logo_preview').innerHTML = '';
    }
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function showDetail(hashid) {
    const data = prodiData[hashid];
    if (!data) {
        alert('Data program studi tidak ditemukan');
        return;
    }
    
    const formatDate = (dateStr) => {
        if (!dateStr) return '-';
        try {
            return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        } catch(e) { return '-'; }
    };
    
    const getBadgeClass = (akreditasi) => {
        const badges = {'A': 'success', 'Unggul': 'success', 'B': 'primary', 'Baik Sekali': 'primary', 'C': 'warning', 'Baik': 'warning'};
        return badges[akreditasi] || 'secondary';
    };
    
    let html = `
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                ${data.logo 
                    ? '<img src="{{ asset("storage") }}/' + data.logo + '" class="img-fluid rounded shadow-sm" style="max-height: 150px;">' 
                    : '<div class="bg-primary bg-opacity-10 rounded-3 p-5 d-inline-block"><i class="bi bi-mortarboard text-primary" style="font-size: 4rem;"></i></div>'}
            </div>
            <div class="col-md-8">
                <h4 class="mb-1">${data.nama || '-'}</h4>
                <p class="text-muted mb-2">
                    <span class="badge bg-info me-1">${data.jenjang || 'S1'}</span>
                    <span class="badge bg-light text-dark me-1">Kode: ${data.kode || '-'}</span>
                    ${data.singkatan ? '<span class="badge bg-light text-dark">' + data.singkatan + '</span>' : ''}
                </p>
                ${data.akreditasi ? '<span class="badge bg-' + getBadgeClass(data.akreditasi) + ' mb-3"><i class="bi bi-award me-1"></i>Akreditasi: ' + data.akreditasi + '</span>' : '<span class="badge bg-secondary mb-3">Belum Terakreditasi</span>'}
                
                <div class="mt-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-building text-primary me-2"></i>
                        <span><strong>Fakultas:</strong> ${data.fakultas ? data.fakultas.nama : '-'}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-badge text-primary me-2"></i>
                        <span><strong>Kaprodi:</strong> ${data.kaprodi || '<span class="text-muted">Belum diatur</span>'}</span>
                    </div>
                    ${data.gelar_lulusan ? `<div class="d-flex align-items-center mb-2">
                        <i class="bi bi-mortarboard text-primary me-2"></i>
                        <span><strong>Gelar Lulusan:</strong> ${data.gelar_lulusan}</span>
                    </div>` : ''}
                </div>
            </div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-book me-2 text-primary"></i>Informasi Akademik</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="40%">Jenjang</td>
                                <td><span class="badge bg-info">${data.jenjang || 'S1'}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total SKS</td>
                                <td><strong>${data.total_sks || '-'}</strong> SKS</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kuota</td>
                                <td>${data.kuota || '-'} mahasiswa</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Berdiri</td>
                                <td>${formatDate(data.tanggal_berdiri)}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">SK Pendirian</td>
                                <td>${data.sk_pendirian || '-'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-award me-2 text-primary"></i>Akreditasi & Kontak</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="40%">Akreditasi</td>
                                <td>${data.akreditasi ? '<span class="badge bg-' + getBadgeClass(data.akreditasi) + '">' + data.akreditasi + '</span>' : '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tgl Akreditasi</td>
                                <td>${formatDate(data.tanggal_akreditasi)}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No SK</td>
                                <td>${data.no_sk_akreditasi || '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>${data.email ? '<a href="mailto:' + data.email + '">' + data.email + '</a>' : '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Website</td>
                                <td>${data.website ? '<a href="' + data.website + '" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;">' + data.website + '</a>' : '-'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        ${data.alamat || data.telepon ? `
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i>Alamat & Kontak</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            ${data.alamat ? `<div class="col-md-8"><strong>Alamat:</strong> ${data.alamat}</div>` : ''}
                            ${data.telepon ? `<div class="col-md-4"><strong>Telepon:</strong> <a href="tel:${data.telepon}">${data.telepon}</a></div>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        </div>` : ''}
        
        ${data.visi || data.misi || data.kompetensi ? `
        <div class="row">
            ${data.visi ? `
            <div class="col-md-${data.misi ? '6' : '12'} mb-3">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary bg-opacity-10">
                        <h6 class="mb-0 text-primary"><i class="bi bi-bullseye me-2"></i>Visi</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${data.visi}</p>
                    </div>
                </div>
            </div>` : ''}
            ${data.misi ? `
            <div class="col-md-${data.visi ? '6' : '12'} mb-3">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success bg-opacity-10">
                        <h6 class="mb-0 text-success"><i class="bi bi-list-check me-2"></i>Misi</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" style="white-space: pre-line;">${data.misi}</p>
                    </div>
                </div>
            </div>` : ''}
            ${data.kompetensi ? `
            <div class="col-12 mb-3">
                <div class="card border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="mb-0 text-warning"><i class="bi bi-trophy me-2"></i>Kompetensi Lulusan</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" style="white-space: pre-line;">${data.kompetensi}</p>
                    </div>
                </div>
            </div>` : ''}
        </div>` : ''}
    `;
    
    document.getElementById('detailContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}
</script>
@endpush
