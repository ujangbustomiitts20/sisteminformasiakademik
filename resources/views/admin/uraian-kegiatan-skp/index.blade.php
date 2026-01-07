@extends('layouts.app')

@section('title', 'Master Uraian Kegiatan SKP')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Master Uraian Kegiatan SKP</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Uraian Kegiatan SKP</li>
            </ol>
        </nav>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah Uraian
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
    <strong>Terjadi kesalahan:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Total</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-list-check text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Aktif</h6>
                        <h3 class="mb-0">{{ $stats['aktif'] }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Tri Dharma</h6>
                        <h3 class="mb-0">{{ $stats['tri_dharma'] }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-mortarboard text-info fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Penunjang</h6>
                        <h3 class="mb-0">{{ $stats['penunjang'] }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-gear text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Tipe Pegawai</label>
                <select name="tipe_pegawai" class="form-select">
                    <option value="">Semua Tipe</option>
                    @foreach(\App\Models\UraianKegiatanSkp::TIPE_PEGAWAI as $key => $label)
                        <option value="{{ $key }}" {{ request('tipe_pegawai') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach(\App\Models\UraianKegiatanSkp::KATEGORI as $key => $label)
                        <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sub Kategori</label>
                <select name="sub_kategori" class="form-select">
                    <option value="">Semua Sub Kategori</option>
                    @foreach(\App\Models\UraianKegiatanSkp::SUB_KATEGORI as $key => $label)
                        <option value="{{ $key }}" {{ request('sub_kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Pencarian</label>
                <input type="text" name="search" class="form-control" placeholder="Kode / Uraian..." value="{{ request('search') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">Kode</th>
                        <th width="100">Tipe</th>
                        <th width="150">Kategori</th>
                        <th>Uraian Kegiatan</th>
                        <th width="80">Satuan</th>
                        <th width="80">Target</th>
                        <th width="80">Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td><code>{{ $item->kode }}</code></td>
                        <td>
                            <span class="badge bg-{{ $item->tipe_pegawai == 'dosen' ? 'primary' : ($item->tipe_pegawai == 'tendik' ? 'success' : 'secondary') }}">
                                {{ \App\Models\UraianKegiatanSkp::TIPE_PEGAWAI[$item->tipe_pegawai] ?? $item->tipe_pegawai }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->kategori == 'tri_dharma' ? 'info' : ($item->kategori == 'tendik' ? 'warning' : ($item->kategori == 'penunjang' ? 'secondary' : 'dark')) }}">
                                {{ $item->kategori_label }}
                            </span>
                            @if($item->sub_kategori)
                            <br><small class="text-muted">{{ $item->sub_kategori_label }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $item->uraian_kegiatan }}
                            @if($item->keterangan)
                            <br><small class="text-muted">{{ Str::limit($item->keterangan, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $item->satuan ?? '-' }}</td>
                        <td>{{ $item->target_default ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status_badge }}">{{ $item->status_label }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-warning" 
                                        onclick="editItem({{ json_encode($item) }})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('kepegawaian.uraian-kegiatan-skp.toggle-status', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-{{ $item->is_active ? 'secondary' : 'success' }}" 
                                            title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $item->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="confirmDelete('{{ route('kepegawaian.uraian-kegiatan-skp.destroy', $item) }}')" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data uraian kegiatan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
    <div class="card-footer">
        {{ $items->links() }}
    </div>
    @endif
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.uraian-kegiatan-skp.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Tambah Uraian Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                                <select name="tipe_pegawai" class="form-select" required>
                                    <option value="">Pilih Tipe</option>
                                    @foreach(\App\Models\UraianKegiatanSkp::TIPE_PEGAWAI as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach(\App\Models\UraianKegiatanSkp::KATEGORI as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Sub Kategori</label>
                                <select name="sub_kategori" class="form-select">
                                    <option value="">Pilih Sub Kategori</option>
                                    @foreach(\App\Models\UraianKegiatanSkp::SUB_KATEGORI as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Uraian Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="uraian_kegiatan" class="form-control" rows="3" required 
                                  placeholder="Contoh: Melaksanakan perkuliahan sesuai jadwal"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Satuan</label>
                                <select name="satuan" class="form-select">
                                    <option value="">Pilih Satuan</option>
                                    @foreach(\App\Models\UraianKegiatanSkp::SATUAN as $key => $label)
                                        <option value="{{ $label }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Target Default</label>
                                <input type="number" name="target_default" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active_add" checked>
                        <label class="form-check-label" for="is_active_add">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Uraian Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input type="text" id="edit_kode" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                                <select name="tipe_pegawai" id="edit_tipe_pegawai" class="form-select" required>
                                    @foreach(\App\Models\UraianKegiatanSkp::TIPE_PEGAWAI as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" id="edit_kategori" class="form-select" required>
                                    @foreach(\App\Models\UraianKegiatanSkp::KATEGORI as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sub Kategori</label>
                                <select name="sub_kategori" id="edit_sub_kategori" class="form-select">
                                    <option value="">Pilih Sub Kategori</option>
                                    @foreach(\App\Models\UraianKegiatanSkp::SUB_KATEGORI as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Uraian Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="uraian_kegiatan" id="edit_uraian_kegiatan" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Satuan</label>
                                <select name="satuan" id="edit_satuan" class="form-select">
                                    <option value="">Pilih Satuan</option>
                                    @foreach(\App\Models\UraianKegiatanSkp::SATUAN as $key => $label)
                                        <option value="{{ $label }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Target Default</label>
                                <input type="number" name="target_default" id="edit_target_default" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" id="edit_urutan" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="edit_is_active">
                        <label class="form-check-label" for="edit_is_active">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formDelete" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus uraian kegiatan ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editItem(item) {
    document.getElementById('formEdit').action = '{{ url("kepegawaian/uraian-kegiatan-skp") }}/' + item.hashid;
    document.getElementById('edit_kode').value = item.kode;
    document.getElementById('edit_tipe_pegawai').value = item.tipe_pegawai || 'semua';
    document.getElementById('edit_kategori').value = item.kategori;
    document.getElementById('edit_sub_kategori').value = item.sub_kategori || '';
    document.getElementById('edit_uraian_kegiatan').value = item.uraian_kegiatan;
    document.getElementById('edit_satuan').value = item.satuan || '';
    document.getElementById('edit_target_default').value = item.target_default || '';
    document.getElementById('edit_urutan').value = item.urutan || 0;
    document.getElementById('edit_keterangan').value = item.keterangan || '';
    document.getElementById('edit_is_active').checked = item.is_active;
    
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function confirmDelete(url) {
    document.getElementById('formDelete').action = url;
    new bootstrap.Modal(document.getElementById('modalDelete')).show();
}
</script>
@endpush
