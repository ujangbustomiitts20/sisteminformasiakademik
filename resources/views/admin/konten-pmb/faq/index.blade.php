@extends('layouts.app')

@section('title', 'Kelola FAQ')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Kelola FAQ</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalFaq">
                <i class="fas fa-plus me-1"></i>Tambah FAQ
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Pertanyaan</th>
                            <th width="120">Kategori</th>
                            <th width="80">Urutan</th>
                            <th width="80">Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faq as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ Str::limit($item->pertanyaan, 80) }}</strong>
                                <br><small class="text-muted">{{ Str::limit($item->jawaban, 100) }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ $item->kategori ?? 'Umum' }}</span></td>
                            <td>{{ $item->urutan }}</td>
                            <td>
                                @if($item->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalFaq" 
                                    data-id="{{ $item->id }}"
                                    data-pertanyaan="{{ $item->pertanyaan }}"
                                    data-jawaban="{{ $item->jawaban }}"
                                    data-kategori="{{ $item->kategori }}"
                                    data-urutan="{{ $item->urutan }}"
                                    data-is_active="{{ $item->is_active }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('pmb.konten-pmb.faq.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada FAQ</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal FAQ -->
<div class="modal fade" id="modalFaq" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formFaq" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                        <input type="text" name="pertanyaan" id="pertanyaan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jawaban <span class="text-danger">*</span></label>
                        <textarea name="jawaban" id="jawaban" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <input type="text" name="kategori" id="kategori" class="form-control" placeholder="Umum">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" id="urutan" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
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
    const modal = document.getElementById('modalFaq');
    const form = document.getElementById('formFaq');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.getElementById('modalTitle');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        
        if (id) {
            // Edit mode
            modalTitle.textContent = 'Edit FAQ';
            form.action = "{{ url('admin/pmb/konten-portal/faq') }}/" + id;
            methodField.innerHTML = '@method("PUT")';
            
            document.getElementById('pertanyaan').value = button.dataset.pertanyaan;
            document.getElementById('jawaban').value = button.dataset.jawaban;
            document.getElementById('kategori').value = button.dataset.kategori || '';
            document.getElementById('urutan').value = button.dataset.urutan || 0;
            document.getElementById('is_active').value = button.dataset.is_active;
        } else {
            // Add mode
            modalTitle.textContent = 'Tambah FAQ';
            form.action = "{{ route('pmb.konten-pmb.faq.store') }}";
            methodField.innerHTML = '';
            form.reset();
        }
    });
});
</script>
@endpush
