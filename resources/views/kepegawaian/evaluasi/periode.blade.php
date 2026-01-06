@extends('layouts.app')

@section('title', 'Kelola Periode Evaluasi')

@section('content')
<div class="page-title">
    <h4>Kelola Periode Evaluasi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.evaluasi.index') }}">Evaluasi Kinerja</a></li>
            <li class="breadcrumb-item active">Periode</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Periode Evaluasi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Periode</th>
                                <th>Tahun</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Akhir</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periodeList as $periode)
                                <tr>
                                    <td>{{ $periode->nama }}</td>
                                    <td>{{ $periode->tahun }}</td>
                                    <td>{{ $periode->tanggal_mulai->format('d/m/Y') }}</td>
                                    <td>{{ $periode->tanggal_akhir->format('d/m/Y') }}</td>
                                    <td>
                                        @if($periode->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-warning" onclick="editPeriode({{ json_encode($periode) }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('kepegawaian.evaluasi.periode.destroy', $periode) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus periode ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Belum ada periode evaluasi</td>
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
                <h5 class="mb-0" id="formTitle">Tambah Periode Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.evaluasi.periode.store') }}" method="POST" id="formPeriode">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="periode_id" id="periodeId">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Periode <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control" required placeholder="e.g. Semester Ganjil">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" id="tahun" class="form-control" required value="{{ date('Y') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggalMulai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_akhir" id="tanggalAkhir" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="isActive" class="form-check-input" value="1">
                            <label class="form-check-label" for="isActive">Aktifkan periode ini</label>
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
function editPeriode(periode) {
    document.getElementById('formTitle').textContent = 'Edit Periode';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('periodeId').value = periode.id;
    document.getElementById('nama').value = periode.nama;
    document.getElementById('tahun').value = periode.tahun;
    document.getElementById('tanggalMulai').value = periode.tanggal_mulai.split('T')[0];
    document.getElementById('tanggalAkhir').value = periode.tanggal_akhir.split('T')[0];
    document.getElementById('isActive').checked = periode.is_active;
    document.getElementById('formPeriode').action = '{{ url("kepegawaian/evaluasi/periode") }}/' + periode.hashid;
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Tambah Periode Baru';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('periodeId').value = '';
    document.getElementById('formPeriode').reset();
    document.getElementById('formPeriode').action = '{{ route("kepegawaian.evaluasi.periode.store") }}';
}
</script>
@endpush
