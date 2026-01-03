@extends('layouts.app')

@section('title', 'Kelola Kontak')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-address-book me-2"></i>Kelola Kontak</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKontak">
                <i class="fas fa-plus me-1"></i>Tambah Kontak
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
                            <th width="60">Icon</th>
                            <th>Tipe</th>
                            <th>Label</th>
                            <th>Value</th>
                            <th>Link</th>
                            <th width="80">Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kontak as $item)
                        <tr>
                            <td><i class="bi bi-{{ $item->icon ?? 'circle' }} fs-4 text-primary"></i></td>
                            <td><span class="badge bg-secondary">{{ ucfirst($item->type) }}</span></td>
                            <td>{{ $item->label }}</td>
                            <td>{{ Str::limit($item->value, 40) }}</td>
                            <td>
                                @if($item->link)
                                <a href="{{ $item->link }}" target="_blank" class="text-primary">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if($item->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalKontak"
                                    data-id="{{ $item->id }}"
                                    data-type="{{ $item->type }}"
                                    data-label="{{ $item->label }}"
                                    data-value="{{ $item->value }}"
                                    data-icon="{{ $item->icon }}"
                                    data-link="{{ $item->link }}"
                                    data-urutan="{{ $item->urutan }}"
                                    data-is_active="{{ $item->is_active }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('pmb.konten-pmb.kontak.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data kontak</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kontak -->
<div class="modal fade" id="modalKontak" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formKontak" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kontak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="phone">Telepon</option>
                                <option value="email">Email</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="address">Alamat</option>
                                <option value="facebook">Facebook</option>
                                <option value="instagram">Instagram</option>
                                <option value="youtube">YouTube</option>
                                <option value="twitter">Twitter</option>
                                <option value="tiktok">TikTok</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="jam_operasional">Jam Operasional</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" id="label" class="form-control" required placeholder="Telepon, WhatsApp, dll">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Value <span class="text-danger">*</span></label>
                            <input type="text" name="value" id="value" class="form-control" required placeholder="081234567890, @username, dll">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Icon (Bootstrap Icons)</label>
                            <input type="text" name="icon" id="icon" class="form-control" placeholder="telephone, envelope, whatsapp">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Link</label>
                            <input type="url" name="link" id="link" class="form-control" placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Urutan</label>
                            <input type="number" name="urutan" id="urutan" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" id="is_active" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
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
    const modal = document.getElementById('modalKontak');
    const form = document.getElementById('formKontak');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.getElementById('modalTitle');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        
        if (id) {
            modalTitle.textContent = 'Edit Kontak';
            form.action = "{{ url('admin/pmb/konten-portal/kontak') }}/" + id;
            methodField.innerHTML = '@method("PUT")';
            
            document.getElementById('type').value = button.dataset.type;
            document.getElementById('label').value = button.dataset.label;
            document.getElementById('value').value = button.dataset.value;
            document.getElementById('icon').value = button.dataset.icon || '';
            document.getElementById('link').value = button.dataset.link || '';
            document.getElementById('urutan').value = button.dataset.urutan || 0;
            document.getElementById('is_active').value = button.dataset.is_active;
        } else {
            modalTitle.textContent = 'Tambah Kontak';
            form.action = "{{ route('pmb.konten-pmb.kontak.store') }}";
            methodField.innerHTML = '';
            form.reset();
        }
    });
});
</script>
@endpush
