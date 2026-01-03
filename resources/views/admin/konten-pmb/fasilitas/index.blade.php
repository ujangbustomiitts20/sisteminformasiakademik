@extends('layouts.app')

@section('title', 'Kelola Fasilitas')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-building me-2"></i>Kelola Fasilitas</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalFasilitas">
                <i class="fas fa-plus me-1"></i>Tambah Fasilitas
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
                @forelse($fasilitas as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            @if($item->gambar)
                            <img src="{{ $item->gambar_url }}" alt="{{ $item->nama }}" class="mb-3" style="max-height: 60px;">
                            @else
                            <i class="bi bi-{{ $item->icon ?? 'building' }} text-primary mb-3" style="font-size: 2.5rem;"></i>
                            @endif
                            <h6 class="card-title fw-bold mb-1">{{ $item->nama }}</h6>
                            <small class="text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-2">
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalFasilitas"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama }}"
                                data-deskripsi="{{ $item->deskripsi }}"
                                data-icon="{{ $item->icon }}"
                                data-urutan="{{ $item->urutan }}"
                                data-is_active="{{ $item->is_active }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('pmb.konten-pmb.fasilitas.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                        <i class="fas fa-building fa-4x mb-3 opacity-25"></i>
                        <p>Belum ada data fasilitas</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Fasilitas -->
<div class="modal fade" id="modalFasilitas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formFasilitas" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Fasilitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gambar</label>
                                <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Icon (jika tidak ada gambar)</label>
                                <input type="text" name="icon" id="icon" class="form-control" placeholder="building, wifi, book">
                            </div>
                        </div>
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
    const modal = document.getElementById('modalFasilitas');
    const form = document.getElementById('formFasilitas');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.getElementById('modalTitle');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        
        if (id) {
            modalTitle.textContent = 'Edit Fasilitas';
            form.action = "{{ url('admin/pmb/konten-portal/fasilitas') }}/" + id;
            methodField.innerHTML = '@method("PUT")';
            
            document.getElementById('nama').value = button.dataset.nama;
            document.getElementById('deskripsi').value = button.dataset.deskripsi || '';
            document.getElementById('icon').value = button.dataset.icon || '';
            document.getElementById('urutan').value = button.dataset.urutan || 0;
            document.getElementById('is_active').value = button.dataset.is_active;
        } else {
            modalTitle.textContent = 'Tambah Fasilitas';
            form.action = "{{ route('pmb.konten-pmb.fasilitas.store') }}";
            methodField.innerHTML = '';
            form.reset();
        }
    });
});
</script>
@endpush
