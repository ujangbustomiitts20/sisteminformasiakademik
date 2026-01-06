@extends('layouts.app')

@section('title', 'Kelola Jenis Pelanggaran')

@section('content')
<div class="page-title">
    <h4>Kelola Jenis Pelanggaran</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pelanggaran.index') }}">Pelanggaran</a></li>
            <li class="breadcrumb-item active">Jenis Pelanggaran</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Jenis Pelanggaran</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Tingkat Default</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jenisList as $jenis)
                                <tr>
                                    <td><code>{{ $jenis->kode }}</code></td>
                                    <td>
                                        <strong>{{ $jenis->nama }}</strong>
                                        @if($jenis->deskripsi)
                                            <br><small class="text-muted">{{ Str::limit($jenis->deskripsi, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $tingkatColors = ['ringan' => 'success', 'sedang' => 'warning', 'berat' => 'danger'];
                                            $tingkatLabels = ['ringan' => 'Ringan', 'sedang' => 'Sedang', 'berat' => 'Berat'];
                                        @endphp
                                        <span class="badge bg-{{ $tingkatColors[$jenis->tingkat_default] ?? 'secondary' }}">
                                            {{ $tingkatLabels[$jenis->tingkat_default] ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($jenis->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-warning" onclick="editJenis({{ json_encode($jenis) }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('kepegawaian.pelanggaran.jenis.destroy', $jenis) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus jenis pelanggaran ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">Belum ada jenis pelanggaran</td>
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
                <h5 class="mb-0" id="formTitle">Tambah Jenis Pelanggaran</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.pelanggaran.jenis.store') }}" method="POST" id="formJenis">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="mb-3">
                        <label class="form-label">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control" required placeholder="e.g. PL-001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control" required placeholder="e.g. Keterlambatan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tingkat Default <span class="text-danger">*</span></label>
                        <select name="tingkat_default" id="tingkatDefault" class="form-select" required>
                            <option value="">Pilih Tingkat</option>
                            <option value="ringan">Ringan</option>
                            <option value="sedang">Sedang</option>
                            <option value="berat">Berat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="isActive" class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="isActive">Aktifkan jenis pelanggaran ini</label>
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
function editJenis(jenis) {
    document.getElementById('formTitle').textContent = 'Edit Jenis Pelanggaran';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('kode').value = jenis.kode;
    document.getElementById('nama').value = jenis.nama;
    document.getElementById('tingkatDefault').value = jenis.tingkat_default;
    document.getElementById('deskripsi').value = jenis.deskripsi || '';
    document.getElementById('isActive').checked = jenis.is_active;
    document.getElementById('formJenis').action = '{{ url("kepegawaian/pelanggaran/jenis") }}/' + jenis.hashid;
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Tambah Jenis Pelanggaran';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formJenis').reset();
    document.getElementById('isActive').checked = true;
    document.getElementById('formJenis').action = '{{ route("kepegawaian.pelanggaran.jenis.store") }}';
}
</script>
@endpush
