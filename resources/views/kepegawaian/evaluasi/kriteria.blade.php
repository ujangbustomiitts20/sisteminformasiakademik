@extends('layouts.app')

@section('title', 'Kelola Kriteria Evaluasi')

@section('content')
<div class="page-title">
    <h4>Kelola Kriteria Evaluasi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.evaluasi.index') }}">Evaluasi Kinerja</a></li>
            <li class="breadcrumb-item active">Kriteria</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Kriteria Evaluasi</h5>
                <span class="badge bg-primary">Total Bobot: {{ $kriteriaList->sum('bobot') }}%</span>
            </div>
            <div class="card-body">
                @if($kriteriaList->sum('bobot') != 100)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Total bobot harus 100%. Saat ini: {{ $kriteriaList->sum('bobot') }}%
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Urutan</th>
                                <th>Nama Kriteria</th>
                                <th>Bobot</th>
                                <th>Nilai Max</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kriteriaList as $kriteria)
                                <tr>
                                    <td>{{ $kriteria->urutan }}</td>
                                    <td>
                                        <strong>{{ $kriteria->nama }}</strong>
                                        @if($kriteria->deskripsi)
                                            <br><small class="text-muted">{{ Str::limit($kriteria->deskripsi, 50) }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info">{{ $kriteria->bobot }}%</span></td>
                                    <td>{{ $kriteria->nilai_maksimal }}</td>
                                    <td>
                                        @if($kriteria->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-warning" onclick="editKriteria({{ json_encode($kriteria) }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('kepegawaian.evaluasi.kriteria.destroy', $kriteria) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus kriteria ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Belum ada kriteria evaluasi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0" id="formTitle">Tambah Kriteria Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.evaluasi.kriteria.store') }}" method="POST" id="formKriteria">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="kriteria_id" id="kriteriaId">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control" required placeholder="e.g. Kinerja Utama">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bobot (%) <span class="text-danger">*</span></label>
                            <input type="number" name="bobot" id="bobot" class="form-control" required min="1" max="100" value="10">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nilai Maksimal <span class="text-danger">*</span></label>
                            <input type="number" name="nilai_maksimal" id="nilaiMaksimal" class="form-control" required min="1" value="100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan <span class="text-danger">*</span></label>
                        <input type="number" name="urutan" id="urutan" class="form-control" required min="1" value="{{ $kriteriaList->count() + 1 }}">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="isActive" class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="isActive">Aktifkan kriteria ini</label>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="bi bi-x-lg me-1"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editKriteria(kriteria) {
    document.getElementById('formTitle').textContent = 'Edit Kriteria';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('kriteriaId').value = kriteria.id;
    document.getElementById('nama').value = kriteria.nama;
    document.getElementById('deskripsi').value = kriteria.deskripsi || '';
    document.getElementById('bobot').value = kriteria.bobot;
    document.getElementById('nilaiMaksimal').value = kriteria.nilai_maksimal;
    document.getElementById('urutan').value = kriteria.urutan;
    document.getElementById('isActive').checked = kriteria.is_active;
    document.getElementById('formKriteria').action = '{{ url("kepegawaian/evaluasi/kriteria") }}/' + kriteria.hashid;
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Tambah Kriteria Baru';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('kriteriaId').value = '';
    document.getElementById('formKriteria').reset();
    document.getElementById('isActive').checked = true;
    document.getElementById('formKriteria').action = '{{ route("kepegawaian.evaluasi.kriteria.store") }}';
}
</script>
@endpush
