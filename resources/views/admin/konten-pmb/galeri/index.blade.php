@extends('layouts.app')

@section('title', 'Kelola Galeri')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-images me-2"></i>Kelola Galeri</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalGaleri">
                <i class="fas fa-plus me-1"></i>Tambah Foto
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
                @forelse($galeri as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <img src="{{ $item->gambar_url }}" class="card-img-top" alt="{{ $item->judul }}" style="height: 180px; object-fit: cover;">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-1">{{ Str::limit($item->judul, 30) }}</h6>
                            <small class="text-muted">{{ $item->kategori ?? 'Umum' }}</small>
                            <div class="mt-2">
                                @if($item->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-3 pt-0">
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalGaleri"
                                data-id="{{ $item->id }}"
                                data-judul="{{ $item->judul }}"
                                data-deskripsi="{{ $item->deskripsi }}"
                                data-kategori="{{ $item->kategori }}"
                                data-urutan="{{ $item->urutan }}"
                                data-is_active="{{ $item->is_active }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('pmb.konten-pmb.galeri.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                        <i class="fas fa-images fa-4x mb-3 opacity-25"></i>
                        <p>Belum ada foto di galeri</p>
                    </div>
                </div>
                @endforelse
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $galeri->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Galeri -->
<div class="modal fade" id="modalGaleri" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formGaleri" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar <span id="gambarRequired" class="text-danger">*</span></label>
                        <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG. Maks: 2MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <input type="text" name="kategori" id="kategori" class="form-control" placeholder="Kegiatan">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" id="urutan" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
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
    const modal = document.getElementById('modalGaleri');
    const form = document.getElementById('formGaleri');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.getElementById('modalTitle');
    const gambarRequired = document.getElementById('gambarRequired');
    const gambarInput = document.getElementById('gambar');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        
        if (id) {
            // Edit mode
            modalTitle.textContent = 'Edit Foto';
            form.action = "{{ url('admin/pmb/konten-portal/galeri') }}/" + id;
            methodField.innerHTML = '@method("PUT")';
            gambarRequired.textContent = '';
            gambarInput.removeAttribute('required');
            
            document.getElementById('judul').value = button.dataset.judul;
            document.getElementById('deskripsi').value = button.dataset.deskripsi || '';
            document.getElementById('kategori').value = button.dataset.kategori || '';
            document.getElementById('urutan').value = button.dataset.urutan || 0;
            document.getElementById('is_active').value = button.dataset.is_active;
        } else {
            // Add mode
            modalTitle.textContent = 'Tambah Foto';
            form.action = "{{ route('pmb.konten-pmb.galeri.store') }}";
            methodField.innerHTML = '';
            gambarRequired.textContent = '*';
            gambarInput.setAttribute('required', 'required');
            form.reset();
        }
    });
});
</script>
@endpush
