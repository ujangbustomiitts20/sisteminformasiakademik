@extends('layouts.app')

@section('title', 'Template Dokumen')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Template Dokumen</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Template Dokumen</h1>
            <p class="text-muted mb-0">Kelola template surat dan dokumen dengan field dinamis</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Template
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Template</div>
                            <div class="h4 mb-0">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-check-circle fs-4 text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Aktif</div>
                            <div class="h4 mb-0">{{ $stats['aktif'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-mortarboard fs-4 text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Akademik</div>
                            <div class="h4 mb-0">{{ $stats['akademik'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-cash-stack fs-4 text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Keuangan</div>
                            <div class="h4 mb-0">{{ $stats['keuangan'] }}</div>
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
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.template-dokumen.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Cari nama/kode..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="kategori">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $key => $label)
                            <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.template-dokumen.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 ps-4" style="width: 50px;">No</th>
                            <th class="border-0">Kode</th>
                            <th class="border-0">Nama Template</th>
                            <th class="border-0">Kategori</th>
                            <th class="border-0 text-center">Fields</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 text-end pe-4" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $index => $template)
                            <tr>
                                <td class="ps-4">{{ $templates->firstItem() + $index }}</td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $template->kode }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $template->nama }}</div>
                                    @if($template->deskripsi)
                                        <small class="text-muted">{{ Str::limit($template->deskripsi, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($template->kategori) {
                                            'akademik' => 'bg-info',
                                            'keuangan' => 'bg-warning text-dark',
                                            'kepegawaian' => 'bg-primary',
                                            'pmb' => 'bg-success',
                                            'umum' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $kategoris[$template->kategori] ?? $template->kategori }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $template->fields->count() }} field</span>
                                </td>
                                <td class="text-center">
                                    @if($template->aktif)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.template-dokumen.show', $template->hashid) }}" class="btn btn-sm btn-outline-info" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.template-dokumen.fields', $template->hashid) }}" class="btn btn-sm btn-outline-primary" title="Kelola Field">
                                            <i class="bi bi-list-check"></i>
                                        </a>
                                        <a href="{{ route('admin.template-dokumen.edit', $template->hashid) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $template->hashid }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal Hapus -->
                            <div class="modal fade" id="modalHapus{{ $template->hashid }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus template <strong>{{ $template->nama }}</strong>?</p>
                                            <p class="text-danger small mb-0">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Semua field dalam template ini juga akan dihapus.
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('admin.template-dokumen.destroy', $template->hashid) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-file-earmark-x fs-1 d-block mb-3"></i>
                                        Belum ada template dokumen.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($templates->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.template-dokumen.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Template Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kode Template <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode" required placeholder="surat_aktif">
                            <small class="text-muted">Gunakan snake_case, tanpa spasi</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nama Template <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" required placeholder="Surat Keterangan Aktif Kuliah">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="kategori" required>
                                @foreach($kategoris as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" name="deskripsi" placeholder="Deskripsi singkat...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Template Judul</label>
                            <input type="text" class="form-control" name="template_judul" placeholder="SURAT KETERANGAN AKTIF KULIAH">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Template Nomor Surat</label>
                            <input type="text" class="form-control" name="template_nomor" placeholder="{no_surat}/UN.XX/AK/{bulan_romawi}/{tahun}">
                            <small class="text-muted">Placeholder: {no_surat}, {bulan_romawi}, {tahun}, {tanggal_sekarang}</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Template Isi</label>
                            <textarea class="form-control" name="template_isi" rows="5" placeholder="Yang bertanda tangan di bawah ini..."></textarea>
                            <small class="text-muted">Gunakan placeholder: {nama}, {nim}, {program_studi}, dll.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Template Penutup</label>
                            <textarea class="form-control" name="template_penutup" rows="2" placeholder="Demikian surat keterangan ini dibuat..."></textarea>
                        </div>
                        
                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="mb-3">Pengaturan Pejabat Penandatangan</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pejabat 1</label>
                            <select class="form-select" name="pejabat_1_id">
                                <option value="">-- Pilih Pejabat --</option>
                                @foreach($pejabats as $pejabat)
                                    <option value="{{ $pejabat->id }}">{{ $pejabat->nama }} - {{ $pejabat->jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Label Pejabat 1</label>
                            <input type="text" class="form-control" name="pejabat_1_label" placeholder="Kepala Bagian Akademik">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pejabat 2 (Opsional)</label>
                            <select class="form-select" name="pejabat_2_id">
                                <option value="">-- Pilih Pejabat --</option>
                                @foreach($pejabats as $pejabat)
                                    <option value="{{ $pejabat->id }}">{{ $pejabat->nama }} - {{ $pejabat->jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Label Pejabat 2</label>
                            <input type="text" class="form-control" name="pejabat_2_label" placeholder="Dekan">
                        </div>
                        
                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="mb-3">Opsi Tampilan</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tampilkan_kop" id="tampilkan_kop" checked>
                                <label class="form-check-label" for="tampilkan_kop">Tampilkan Kop Surat</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tampilkan_logo" id="tampilkan_logo" checked>
                                <label class="form-check-label" for="tampilkan_logo">Tampilkan Logo</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tampilkan_ttd_digital" id="tampilkan_ttd_digital">
                                <label class="form-check-label" for="tampilkan_ttd_digital">Tampilkan Tanda Tangan Digital</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tampilkan_stempel" id="tampilkan_stempel">
                                <label class="form-check-label" for="tampilkan_stempel">Tampilkan Stempel</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aktif" id="aktif" checked>
                                <label class="form-check-label" for="aktif">Template Aktif</label>
                            </div>
                        </div>
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
@endsection
