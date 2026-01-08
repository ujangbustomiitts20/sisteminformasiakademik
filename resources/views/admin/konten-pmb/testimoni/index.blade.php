@extends('layouts.app')

@section('title', 'Kelola Testimoni')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-chat-quote me-2"></i>Kelola Testimoni</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTestimoni">
                <i class="bi bi-plus-lg me-1"></i>Tambah Testimoni
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">Foto</th>
                            <th>Nama</th>
                            <th>Prodi/Angkatan</th>
                            <th>Testimoni</th>
                            <th width="80">Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($testimoni as $item)
                        <tr>
                            <td>
                                <img src="{{ $item->foto_url }}" alt="" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <strong>{{ $item->nama }}</strong>
                                @if($item->pekerjaan)
                                <br><small class="text-muted">{{ $item->pekerjaan }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $item->program_studi ?? '-' }}
                                @if($item->angkatan)
                                <br><small class="text-muted">Angkatan {{ $item->angkatan }}</small>
                                @endif
                            </td>
                            <td>{{ Str::limit($item->testimoni, 80) }}</td>
                            <td>
                                @if($item->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalTestimoni"
                                    data-id="{{ $item->hashid }}"
                                    data-nama="{{ $item->nama }}"
                                    data-angkatan="{{ $item->angkatan }}"
                                    data-program_studi="{{ $item->program_studi }}"
                                    data-testimoni="{{ $item->testimoni }}"
                                    data-pekerjaan="{{ $item->pekerjaan }}"
                                    data-urutan="{{ $item->urutan }}"
                                    data-is_active="{{ $item->is_active }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('pmb.konten-pmb.testimoni.destroy', $item->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada testimoni</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Testimoni -->
<div class="modal fade" id="modalTestimoni" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formTestimoni" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Testimoni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="nama" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Angkatan</label>
                            <input type="text" name="angkatan" id="angkatan" class="form-control" placeholder="2023">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program Studi</label>
                            <input type="text" name="program_studi" id="program_studi" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pekerjaan (untuk Alumni)</label>
                            <input type="text" name="pekerjaan" id="pekerjaan" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Testimoni <span class="text-danger">*</span></label>
                            <textarea name="testimoni" id="testimoni" class="form-control" rows="4" required></textarea>
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
    const modal = document.getElementById('modalTestimoni');
    const form = document.getElementById('formTestimoni');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.getElementById('modalTitle');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        
        if (id) {
            modalTitle.textContent = 'Edit Testimoni';
            form.action = "{{ url('pmb/konten-portal/testimoni') }}/" + id;
            methodField.innerHTML = '@method("PUT")';
            
            document.getElementById('nama').value = button.dataset.nama;
            document.getElementById('angkatan').value = button.dataset.angkatan || '';
            document.getElementById('program_studi').value = button.dataset.program_studi || '';
            document.getElementById('testimoni').value = button.dataset.testimoni;
            document.getElementById('pekerjaan').value = button.dataset.pekerjaan || '';
            document.getElementById('urutan').value = button.dataset.urutan || 0;
            document.getElementById('is_active').value = button.dataset.is_active;
        } else {
            modalTitle.textContent = 'Tambah Testimoni';
            form.action = "{{ route('pmb.konten-pmb.testimoni.store') }}";
            methodField.innerHTML = '';
            form.reset();
        }
    });
});
</script>
@endpush
