@extends('layouts.app')

@section('title', 'Master Fakultas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Master Fakultas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Fakultas</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-1"></i>Tambah Fakultas
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
                                <i class="bi bi-building text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Fakultas</h6>
                            <h4 class="mb-0">{{ $fakultas->count() }}</h4>
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
                                <i class="bi bi-mortarboard text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Program Studi</h6>
                            <h4 class="mb-0">{{ $fakultas->sum('program_studi_count') }}</h4>
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
                                <i class="bi bi-award text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Terakreditasi</h6>
                            <h4 class="mb-0">{{ $fakultas->whereNotNull('akreditasi')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fakultas Grid -->
    <div class="row g-4">
        @forelse($fakultas as $fak)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        @if($fak->logo)
                        <img src="{{ asset('storage/' . $fak->logo) }}" alt="Logo" class="me-2" style="height: 40px;">
                        @else
                        <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-2">
                            <i class="bi bi-building text-primary"></i>
                        </div>
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $fak->nama }}</h6>
                            <small class="text-muted">{{ $fak->kode }}</small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" 
                                   onclick="showDetail('{{ $fak->hashid }}')">
                                    <i class="bi bi-eye me-2"></i>Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" 
                                   onclick="editFakultas('{{ $fak->hashid }}', {{ json_encode($fak) }})">
                                    <i class="bi bi-pencil me-2"></i>Edit
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('fakultas.destroy', $fak->hashid) }}" method="POST" 
                                      onsubmit="return confirm('Yakin hapus fakultas ini?')">
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
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Program Studi</small>
                            <strong>{{ $fak->program_studi_count }} Prodi</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Akreditasi</small>
                            @if($fak->akreditasi)
                            <span class="badge bg-{{ $fak->akreditasi_badge }}">{{ $fak->akreditasi }}</span>
                            @else
                            <span class="badge bg-secondary">-</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($fak->dekan)
                    <div class="mb-2">
                        <small class="text-muted d-block">Dekan</small>
                        <span>{{ $fak->dekan }}</span>
                    </div>
                    @endif
                    
                    <div class="d-flex flex-wrap gap-1">
                        @if($fak->email)
                        <a href="mailto:{{ $fak->email }}" class="badge bg-light text-dark text-decoration-none">
                            <i class="bi bi-envelope me-1"></i>{{ $fak->email }}
                        </a>
                        @endif
                        @if($fak->telepon)
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-telephone me-1"></i>{{ $fak->telepon }}
                        </span>
                        @endif
                    </div>
                </div>
                @if($fak->visi)
                <div class="card-footer bg-transparent">
                    <small class="text-muted">
                        <strong>Visi:</strong> {{ Str::limit($fak->visi, 100) }}
                    </small>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Belum ada data fakultas</h5>
                    <p class="text-muted">Klik tombol "Tambah Fakultas" untuk menambahkan data.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('fakultas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Tambah Fakultas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="createTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#create-umum" type="button">
                                <i class="bi bi-info-circle me-1"></i>Umum
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#create-kontak" type="button">
                                <i class="bi bi-telephone me-1"></i>Kontak
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#create-visimisi" type="button">
                                <i class="bi bi-bullseye me-1"></i>Visi & Misi
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- Tab Umum -->
                        <div class="tab-pane fade show active" id="create-umum">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Kode <span class="text-danger">*</span></label>
                                    <input type="text" name="kode" class="form-control" required maxlength="10">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Nama Fakultas <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Singkatan</label>
                                    <input type="text" name="singkatan" class="form-control" maxlength="20">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Dekan</label>
                                    <select name="dekan" class="form-select">
                                        <option value="">- Pilih Dekan -</option>
                                        @foreach($dosen as $d)
                                        <option value="{{ $d->nama }}">{{ $d->nama }} ({{ $d->nidn }})</option>
                                        @endforeach
                                    </select>
                                </div>
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
                                    <label class="form-label">Tanggal Berdiri</label>
                                    <input type="date" name="tanggal_berdiri" class="form-control">
                                </div>
                                <div class="col-12">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Fakultas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="editTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#edit-umum" type="button">
                                <i class="bi bi-info-circle me-1"></i>Umum
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-kontak" type="button">
                                <i class="bi bi-telephone me-1"></i>Kontak
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-visimisi" type="button">
                                <i class="bi bi-bullseye me-1"></i>Visi & Misi
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- Tab Umum -->
                        <div class="tab-pane fade show active" id="edit-umum">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Kode <span class="text-danger">*</span></label>
                                    <input type="text" name="kode" id="edit_kode" class="form-control" required maxlength="10">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Nama Fakultas <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Singkatan</label>
                                    <input type="text" name="singkatan" id="edit_singkatan" class="form-control" maxlength="20">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Dekan</label>
                                    <select name="dekan" id="edit_dekan" class="form-select">
                                        <option value="">- Pilih Dekan -</option>
                                        @foreach($dosen as $d)
                                        <option value="{{ $d->nama }}">{{ $d->nama }} ({{ $d->nidn }})</option>
                                        @endforeach
                                    </select>
                                </div>
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
                                    <label class="form-label">Tanggal Berdiri</label>
                                    <input type="date" name="tanggal_berdiri" id="edit_tanggal_berdiri" class="form-control">
                                </div>
                                <div class="col-12">
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
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Detail Fakultas</h5>
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
function editFakultas(hashid, data) {
    document.getElementById('editForm').action = '{{ url("fakultas") }}/' + hashid;
    document.getElementById('edit_kode').value = data.kode || '';
    document.getElementById('edit_nama').value = data.nama || '';
    document.getElementById('edit_singkatan').value = data.singkatan || '';
    document.getElementById('edit_dekan').value = data.dekan || '';
    document.getElementById('edit_akreditasi').value = data.akreditasi || '';
    document.getElementById('edit_tanggal_akreditasi').value = data.tanggal_akreditasi ? data.tanggal_akreditasi.split('T')[0] : '';
    document.getElementById('edit_tanggal_berdiri').value = data.tanggal_berdiri ? data.tanggal_berdiri.split('T')[0] : '';
    document.getElementById('edit_sk_pendirian').value = data.sk_pendirian || '';
    document.getElementById('edit_alamat').value = data.alamat || '';
    document.getElementById('edit_telepon').value = data.telepon || '';
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_website').value = data.website || '';
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
    const fakultas = @json($fakultas->keyBy('hashid'));
    const data = fakultas[hashid];
    
    if (!data) {
        alert('Data fakultas tidak ditemukan');
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
                    : '<div class="bg-primary bg-opacity-10 rounded-3 p-5 d-inline-block"><i class="bi bi-building text-primary" style="font-size: 4rem;"></i></div>'}
            </div>
            <div class="col-md-8">
                <h4 class="mb-1">${data.nama || '-'}</h4>
                <p class="text-muted mb-2">
                    <span class="badge bg-light text-dark me-1">Kode: ${data.kode || '-'}</span>
                    ${data.singkatan ? '<span class="badge bg-light text-dark">' + data.singkatan + '</span>' : ''}
                </p>
                ${data.akreditasi ? '<span class="badge bg-' + getBadgeClass(data.akreditasi) + ' mb-3"><i class="bi bi-award me-1"></i>Akreditasi: ' + data.akreditasi + '</span>' : '<span class="badge bg-secondary mb-3">Belum Terakreditasi</span>'}
                
                <div class="mt-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-badge text-primary me-2"></i>
                        <span><strong>Dekan:</strong> ${data.dekan || '<span class="text-muted">Belum diatur</span>'}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-collection text-primary me-2"></i>
                        <span><strong>Jumlah Prodi:</strong> ${data.program_studi_count || 0} Program Studi</span>
                    </div>
                </div>
            </div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Umum</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="40%">Tanggal Berdiri</td>
                                <td>${formatDate(data.tanggal_berdiri)}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">SK Pendirian</td>
                                <td>${data.sk_pendirian || '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Akreditasi</td>
                                <td>${data.akreditasi || '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tgl Akreditasi</td>
                                <td>${formatDate(data.tanggal_akreditasi)}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-telephone me-2 text-primary"></i>Kontak</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="30%">Alamat</td>
                                <td>${data.alamat || '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Telepon</td>
                                <td>${data.telepon ? '<a href="tel:' + data.telepon + '">' + data.telepon + '</a>' : '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>${data.email ? '<a href="mailto:' + data.email + '">' + data.email + '</a>' : '-'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Website</td>
                                <td>${data.website ? '<a href="' + data.website + '" target="_blank">' + data.website + '</a>' : '-'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        ${data.visi || data.misi ? `
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
        </div>` : ''}
    `;
    
    document.getElementById('detailContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}
</script>
@endpush
