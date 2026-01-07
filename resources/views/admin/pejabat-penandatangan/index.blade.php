@extends('layouts.app')

@section('title', 'Pejabat Penandatangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pejabat Penandatangan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pejabat Penandatangan</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Pejabat
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small>Total Pejabat</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['aktif'] }}</h3>
                    <small>Aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['pimpinan'] }}</h3>
                    <small>Pimpinan</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['akademik'] }}</h3>
                    <small>Akademik</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-warning text-dark">
                <div class="card-body text-center">
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
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, jabatan, kode..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Pejabat</th>
                            <th>Jabatan</th>
                            <th>Kategori</th>
                            <th>TTD/Stempel</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pejabats as $pejabat)
                        <tr>
                            <td>{{ $pejabat->urutan }}</td>
                            <td>
                                <strong>{{ $pejabat->nama_lengkap }}</strong>
                                @if($pejabat->nip)
                                    <br><small class="text-muted">NIP: {{ $pejabat->nip }}</small>
                                @endif
                                <br><code class="small">{{ $pejabat->kode }}</code>
                            </td>
                            <td>
                                {{ $pejabat->jabatan }}
                                @if($pejabat->pangkat_golongan)
                                    <br><small class="text-muted">{{ $pejabat->pangkat_golongan }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $pejabat->kategori == 'pimpinan' ? 'danger' : ($pejabat->kategori == 'akademik' ? 'info' : ($pejabat->kategori == 'keuangan' ? 'warning' : 'secondary')) }}">
                                    {{ $kategoris[$pejabat->kategori] ?? $pejabat->kategori }}
                                </span>
                            </td>
                            <td>
                                @if($pejabat->tanda_tangan)
                                    <i class="bi bi-pen-fill text-success" title="Tanda tangan tersedia"></i>
                                @else
                                    <i class="bi bi-pen text-muted" title="Belum ada tanda tangan"></i>
                                @endif
                                @if($pejabat->stempel)
                                    <i class="bi bi-patch-check-fill text-primary ms-1" title="Stempel tersedia"></i>
                                @else
                                    <i class="bi bi-patch-check text-muted ms-1" title="Belum ada stempel"></i>
                                @endif
                            </td>
                            <td>
                                @if($pejabat->isBerlaku())
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                                @if($pejabat->berlaku_sampai && $pejabat->berlaku_sampai < now()->addMonths(1))
                                    <br><small class="text-danger">Segera berakhir</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.pejabat-penandatangan.show', $pejabat->hashid) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.pejabat-penandatangan.edit', $pejabat->hashid) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.pejabat-penandatangan.toggle-status', $pejabat->hashid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $pejabat->aktif ? 'warning' : 'success' }}" title="{{ $pejabat->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $pejabat->aktif ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.pejabat-penandatangan.destroy', $pejabat->hashid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pejabat ini?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-person-badge fs-1"></i>
                                <p class="mb-0">Belum ada pejabat penandatangan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $pejabats->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.pejabat-penandatangan.store') }}" method="POST" enctype="multipart/form-data" id="formTambahPejabat">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pejabat Penandatangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Error Container for AJAX validation -->
                    <div id="formErrorContainer" class="alert alert-danger d-none"></div>
                    
                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                                <select name="nama_jabatan_id" class="form-select @error('nama_jabatan_id') is-invalid @enderror" id="namaJabatanAdd" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($namaJabatans as $jab)
                                        <option value="{{ $jab->id }}" data-kategori="{{ $jab->kategori }}" data-kode="{{ $jab->kode }}" {{ old('nama_jabatan_id') == $jab->id ? 'selected' : '' }}>{{ $jab->nama }}</option>
                                    @endforeach
                                </select>
                                @error('nama_jabatan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kode akan di-generate otomatis dari jabatan yang dipilih</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required id="kategoriAdd">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategoris as $key => $label)
                                        <option value="{{ $key }}" {{ old('kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    <h6 class="mb-3"><i class="bi bi-person me-2"></i>Data Pejabat <span class="text-danger">*</span></h6>
                    
                    @if($errors->has('pegawai_id') || $errors->has('dosen_id'))
                    <div class="alert alert-danger py-2 mb-3">
                        <small><i class="bi bi-exclamation-triangle me-1"></i>Pilih salah satu: Pegawai atau Dosen</small>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pilih Pegawai</label>
                                <select name="pegawai_id" class="form-select @error('pegawai_id') is-invalid @enderror" id="pegawaiAdd">
                                    <option value="">-- Pilih Pegawai --</option>
                                    @foreach($pegawais as $pegawai)
                                        <option value="{{ $pegawai->id }}" 
                                            data-nama="{{ $pegawai->nama }}" 
                                            data-nip="{{ $pegawai->nip }}" 
                                            data-pangkat="{{ $pegawai->pangkat }} ({{ $pegawai->golongan }})"
                                            {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                            {{ $pegawai->nama }} - {{ $pegawai->nip ?? 'No NIP' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Data diambil dari menu Pegawai</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Atau Pilih Dosen</label>
                                <select name="dosen_id" class="form-select @error('dosen_id') is-invalid @enderror" id="dosenAdd">
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach($dosens as $dosen)
                                        <option value="{{ $dosen->id }}" 
                                            data-nama="{{ $dosen->nama }}" 
                                            data-nip="{{ $dosen->nip }}" 
                                            data-gelar-depan="{{ $dosen->gelar_depan }}" 
                                            data-gelar-belakang="{{ $dosen->gelar_belakang }}"
                                            {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->nama }} - {{ $dosen->nidn ?? $dosen->nip ?? 'No ID' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Data diambil dari menu Dosen</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info Pejabat (Read Only) -->
                    <div id="infoPejabatAdd" class="alert alert-light d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Nama:</small>
                                <div id="displayNamaAdd" class="fw-bold">-</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">NIP/NIDN:</small>
                                <div id="displayNipAdd">-</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    <h6 class="mb-3"><i class="bi bi-calendar me-2"></i>Masa Berlaku & Pengaturan</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" class="form-control" value="{{ old('urutan', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Berlaku Mulai</label>
                                <input type="date" name="berlaku_mulai" class="form-control" value="{{ old('berlaku_mulai') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Berlaku Sampai</label>
                                <input type="date" name="berlaku_sampai" class="form-control" value="{{ old('berlaku_sampai') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanda Tangan Digital</label>
                                <input type="file" name="tanda_tangan" class="form-control" accept="image/png,image/jpeg">
                                <small class="text-muted">Format: PNG/JPG, Max: 1MB. Rekomendasi: PNG transparan</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stempel</label>
                                <input type="file" name="stempel" class="form-control" accept="image/png,image/jpeg">
                                <small class="text-muted">Format: PNG/JPG, Max: 1MB. Rekomendasi: PNG transparan</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dokumen yang Ditandatangani</label>
                        <div class="row">
                            @foreach($dokumens as $key => $label)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dokumen_terkait[]" value="{{ $key }}" id="dok_{{ $key }}">
                                    <label class="form-check-label small" for="dok_{{ $key }}">{{ $label }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktifCheck" checked>
                        <label class="form-check-label" for="aktifCheck">Aktif</label>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addForm = document.getElementById('formTambahPejabat');
    const btnSimpan = addForm?.querySelector('button[type="submit"]');
    const errorContainer = document.getElementById('formErrorContainer');
    
    // Handle form submission with AJAX
    addForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        errorContainer.innerHTML = '';
        errorContainer.classList.add('d-none');
        addForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        addForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        // Show loading state
        if (btnSimpan) {
            btnSimpan.disabled = true;
            btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
        }
        
        // Submit via AJAX
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                // Success - redirect
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return; // Already redirected
            
            if (data.errors) {
                // Show validation errors
                let errorHtml = '<ul class="mb-0">';
                for (const [field, messages] of Object.entries(data.errors)) {
                    messages.forEach(msg => {
                        errorHtml += `<li>${msg}</li>`;
                    });
                    // Add is-invalid class to field
                    const input = addForm.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                    }
                }
                errorHtml += '</ul>';
                errorContainer.innerHTML = errorHtml;
                errorContainer.classList.remove('d-none');
                
                // Scroll to top of modal
                document.querySelector('#addModal .modal-body').scrollTop = 0;
            } else if (data.success) {
                // Success - reload page
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorContainer.innerHTML = '<p class="mb-0">Terjadi kesalahan. Silakan coba lagi.</p>';
            errorContainer.classList.remove('d-none');
        })
        .finally(() => {
            // Reset button state
            if (btnSimpan) {
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = '<i class="bi bi-save me-1"></i> Simpan';
            }
        });
    });

    // Auto-fill from Pegawai
    const pegawaiSelect = document.getElementById('pegawaiAdd');
    const dosenSelect = document.getElementById('dosenAdd');
    const infoPejabat = document.getElementById('infoPejabatAdd');
    const displayNama = document.getElementById('displayNamaAdd');
    const displayNip = document.getElementById('displayNipAdd');

    function updatePejabatInfo() {
        let nama = '-';
        let nip = '-';
        
        if (pegawaiSelect.value) {
            const selected = pegawaiSelect.options[pegawaiSelect.selectedIndex];
            nama = selected.dataset.nama || '-';
            nip = selected.dataset.nip || '-';
        } else if (dosenSelect.value) {
            const selected = dosenSelect.options[dosenSelect.selectedIndex];
            const gelarDepan = selected.dataset.gelarDepan || '';
            const gelarBelakang = selected.dataset.gelarBelakang || '';
            nama = (gelarDepan ? gelarDepan + ' ' : '') + (selected.dataset.nama || '') + (gelarBelakang ? ', ' + gelarBelakang : '');
            nip = selected.dataset.nip || '-';
        }
        
        if (pegawaiSelect.value || dosenSelect.value) {
            infoPejabat.classList.remove('d-none');
            displayNama.textContent = nama;
            displayNip.textContent = nip;
        } else {
            infoPejabat.classList.add('d-none');
        }
    }

    pegawaiSelect?.addEventListener('change', function() {
        if (this.value) {
            dosenSelect.value = ''; // Clear dosen selection
        }
        updatePejabatInfo();
    });

    dosenSelect?.addEventListener('change', function() {
        if (this.value) {
            pegawaiSelect.value = ''; // Clear pegawai selection
        }
        updatePejabatInfo();
    });

    // Auto-set kategori from NamaJabatan
    const namaJabatanSelect = document.getElementById('namaJabatanAdd');
    const kategoriSelect = document.getElementById('kategoriAdd');

    namaJabatanSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value && selected.dataset.kategori && kategoriSelect) {
            kategoriSelect.value = selected.dataset.kategori;
        }
    });
});
</script>
@endpush
@endsection
