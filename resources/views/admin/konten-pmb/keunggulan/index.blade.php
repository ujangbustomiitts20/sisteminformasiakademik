@extends('layouts.app')

@section('title', 'Kelola Keunggulan')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-star me-2"></i>Kelola Keunggulan</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKeunggulan">
                <i class="fas fa-plus me-1"></i>Tambah Keunggulan
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            <div class="row g-4">
                @forelse($keunggulan as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-{{ $item->icon ?? 'star' }} text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title fw-bold">{{ $item->judul }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($item->deskripsi, 100) }}</p>
                            <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalKeunggulan"
                                data-id="{{ $item->id }}"
                                data-judul="{{ $item->judul }}"
                                data-deskripsi="{{ $item->deskripsi }}"
                                data-icon="{{ $item->icon }}"
                                data-urutan="{{ $item->urutan }}"
                                data-is_active="{{ $item->is_active }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('pmb.konten-pmb.keunggulan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-star fa-4x mb-3 opacity-25"></i>
                        <p>Belum ada data keunggulan</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Keunggulan -->
<div class="modal fade" id="modalKeunggulan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formKeunggulan" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Keunggulan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (Bootstrap Icons)</label>
                        <input type="text" name="icon" id="icon" class="form-control" placeholder="star-fill, award, building">
                        <small class="text-muted">Lihat daftar icon di <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" id="urutan" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="is_active" id="is_active" class="form-select">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalKeunggulan');
    const form = document.getElementById('formKeunggulan');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.getElementById('modalTitle');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        
        if (id) {
            modalTitle.textContent = 'Edit Keunggulan';
            form.action = "{{ url('admin/pmb/konten-portal/keunggulan') }}/" + id;
            methodField.innerHTML = '@method("PUT")';
            
            document.getElementById('judul').value = button.dataset.judul;
            document.getElementById('deskripsi').value = button.dataset.deskripsi;
            document.getElementById('icon').value = button.dataset.icon || '';
            document.getElementById('urutan').value = button.dataset.urutan || 0;
            document.getElementById('is_active').value = button.dataset.is_active;
        } else {
            modalTitle.textContent = 'Tambah Keunggulan';
            form.action = "{{ route('pmb.konten-pmb.keunggulan.store') }}";
            methodField.innerHTML = '';
            form.reset();
        }
    });
});
</script>
@endpush
