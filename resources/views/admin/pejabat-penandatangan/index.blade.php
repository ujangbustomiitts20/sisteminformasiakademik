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
            <form action="{{ route('admin.pejabat-penandatangan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pejabat Penandatangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode <span class="text-danger">*</span></label>
                                <input type="text" name="kode" class="form-control" required placeholder="Contoh: rektor, dekan_fti, kepala_keuangan">
                                <small class="text-muted">Kode unik untuk referensi di sistem</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select" required id="kategoriAdd">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategoris as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                                <select name="nama_jabatan_id" class="form-select" id="namaJabatanAdd">
                                    <option value="">-- Pilih dari Daftar Jabatan --</option>
                                    @foreach($namaJabatans as $jab)
                                        <option value="{{ $jab->id }}" data-kategori="{{ $jab->kategori }}">{{ $jab->nama }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih dari daftar jabatan atau isi manual di bawah</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Atau Isi Jabatan Manual</label>
                                <input type="text" name="jabatan" class="form-control" id="jabatanManualAdd" placeholder="Contoh: Rektor, Dekan Fakultas Teknologi Industri">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    <h6 class="mb-3"><i class="bi bi-person me-2"></i>Data Pejabat</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pilih dari Pegawai</label>
                                <select name="pegawai_id" class="form-select" id="pegawaiAdd">
                                    <option value="">-- Pilih Pegawai --</option>
                                    @foreach($pegawais as $pegawai)
                                        <option value="{{ $pegawai->id }}" data-nama="{{ $pegawai->nama }}" data-nip="{{ $pegawai->nip }}" data-pangkat="{{ $pegawai->pangkat }} ({{ $pegawai->golongan }})">
                                            {{ $pegawai->nama }} - {{ $pegawai->nip ?? 'No NIP' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Atau Pilih dari Dosen</label>
                                <select name="dosen_id" class="form-select" id="dosenAdd">
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach($dosens as $dosen)
                                        <option value="{{ $dosen->id }}" data-nama="{{ $dosen->nama }}" data-nip="{{ $dosen->nip }}" data-gelar-depan="{{ $dosen->gelar_depan }}" data-gelar-belakang="{{ $dosen->gelar_belakang }}">
                                            {{ $dosen->nama }} - {{ $dosen->nip ?? 'No NIP' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Gelar Depan</label>
                                <input type="text" name="gelar_depan" class="form-control" id="gelarDepanAdd" placeholder="Prof. Dr.">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" id="namaAdd" placeholder="Isi jika tidak pilih pegawai/dosen">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Gelar Belakang</label>
                                <input type="text" name="gelar_belakang" class="form-control" id="gelarBelakangAdd" placeholder="M.T., Ph.D.">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NIP/NIDN</label>
                                <input type="text" name="nip" class="form-control" id="nipAdd">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pangkat/Golongan</label>
                                <input type="text" name="pangkat_golongan" class="form-control" id="pangkatAdd" placeholder="Pembina Utama Muda (IV/c)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Berlaku Mulai</label>
                                <input type="date" name="berlaku_mulai" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Berlaku Sampai</label>
                                <input type="date" name="berlaku_sampai" class="form-control">
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
    // Loading state for form
    const addForm = document.querySelector('#addModal form');
    const btnSimpan = addForm?.querySelector('button[type="submit"]');
    
    addForm?.addEventListener('submit', function(e) {
        if (btnSimpan) {
            btnSimpan.disabled = true;
            btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
        }
    });

    // Auto-fill from Pegawai
    const pegawaiSelect = document.getElementById('pegawaiAdd');
    const dosenSelect = document.getElementById('dosenAdd');
    const namaInput = document.getElementById('namaAdd');
    const nipInput = document.getElementById('nipAdd');
    const gelarDepanInput = document.getElementById('gelarDepanAdd');
    const gelarBelakangInput = document.getElementById('gelarBelakangAdd');
    const pangkatInput = document.getElementById('pangkatAdd');

    pegawaiSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            namaInput.value = selected.dataset.nama || '';
            nipInput.value = selected.dataset.nip || '';
            pangkatInput.value = selected.dataset.pangkat || '';
            dosenSelect.value = ''; // Clear dosen selection
        }
    });

    dosenSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            namaInput.value = selected.dataset.nama || '';
            nipInput.value = selected.dataset.nip || '';
            gelarDepanInput.value = selected.dataset.gelarDepan || '';
            gelarBelakangInput.value = selected.dataset.gelarBelakang || '';
            pegawaiSelect.value = ''; // Clear pegawai selection
        }
    });

    // Auto-fill jabatan from NamaJabatan
    const namaJabatanSelect = document.getElementById('namaJabatanAdd');
    const jabatanManualInput = document.getElementById('jabatanManualAdd');
    const kategoriSelect = document.getElementById('kategoriAdd');

    namaJabatanSelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            jabatanManualInput.value = selected.text;
            // Set kategori based on jabatan
            if (selected.dataset.kategori && kategoriSelect) {
                kategoriSelect.value = selected.dataset.kategori;
            }
        }
    });
});
</script>
@endpush
@endsection
