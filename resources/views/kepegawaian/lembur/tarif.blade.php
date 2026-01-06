@extends('layouts.app')

@section('title', 'Kelola Tarif Lembur')

@section('content')
<div class="page-title">
    <h4>Kelola Tarif Lembur</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.lembur.index') }}">Lembur</a></li>
            <li class="breadcrumb-item active">Tarif</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Tarif Lembur</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Jenis Hari</th>
                                <th>Jenis Jam</th>
                                <th>Persentase</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tarifList as $tarif)
                                <tr>
                                    <td><code>{{ $tarif->kode }}</code></td>
                                    <td>
                                        <strong>{{ $tarif->nama }}</strong>
                                        @if($tarif->deskripsi)
                                            <br><small class="text-muted">{{ Str::limit($tarif->deskripsi, 40) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tarif->jenis_hari_color }}">{{ $tarif->jenis_hari_label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $tarif->jenis_jam_label }}</span>
                                    </td>
                                    <td class="fw-bold text-primary">{{ $tarif->persentase }}%</td>
                                    <td>
                                        @if($tarif->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-warning" onclick="editTarif({{ json_encode(array_merge($tarif->toArray(), ['hashid' => $tarif->hashid])) }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('kepegawaian.lembur.tarif.destroy', $tarif) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus tarif ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">Belum ada tarif lembur</td>
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
                <h5 class="mb-0" id="formTitle">Tambah Tarif Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.lembur.tarif.store') }}" method="POST" id="formTarif">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" name="kode" id="kode" class="form-control" placeholder="Auto generate jika kosong">
                        <small class="text-muted">Kosongkan untuk generate otomatis</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control" required placeholder="e.g. Lembur Hari Biasa">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2" placeholder="Keterangan tarif"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Hari <span class="text-danger">*</span></label>
                        <select name="jenis_hari" id="jenisHari" class="form-select" required>
                            <option value="">Pilih Jenis</option>
                            <option value="kerja">Hari Kerja</option>
                            <option value="libur">Hari Libur</option>
                            <option value="libur_nasional">Libur Nasional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Jam <span class="text-danger">*</span></label>
                        <select name="jenis_jam" id="jenisJam" class="form-select" required>
                            <option value="">Pilih Jenis</option>
                            <option value="jam_pertama">Jam Pertama</option>
                            <option value="jam_kedua_dst">Jam Kedua dst</option>
                            <option value="semua">Semua Jam</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Persentase dari Gaji <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="persentase" id="persentase" class="form-control" required step="0.01" value="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nominal Tetap (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="nominal_tetap" id="nominalTetap" class="form-control" value="0">
                        </div>
                        <small class="text-muted">Jika diisi, akan digunakan sebagai pengganti persentase</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="isActive" class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="isActive">Aktifkan tarif ini</label>
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
function editTarif(tarif) {
    document.getElementById('formTitle').textContent = 'Edit Tarif';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('kode').value = tarif.kode || '';
    document.getElementById('kode').readOnly = true; // Kode tidak bisa diubah saat edit
    document.getElementById('nama').value = tarif.nama;
    document.getElementById('deskripsi').value = tarif.deskripsi || '';
    document.getElementById('jenisHari').value = tarif.jenis_hari;
    document.getElementById('jenisJam').value = tarif.jenis_jam;
    document.getElementById('persentase').value = tarif.persentase;
    document.getElementById('nominalTetap').value = tarif.nominal_tetap || 0;
    document.getElementById('isActive').checked = tarif.is_active == 1 || tarif.is_active === true;
    document.getElementById('formTarif').action = '{{ url("kepegawaian/lembur/tarif") }}/' + tarif.hashid;
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Tambah Tarif Baru';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('kode').value = '';
    document.getElementById('kode').readOnly = false;
    document.getElementById('nama').value = '';
    document.getElementById('deskripsi').value = '';
    document.getElementById('jenisHari').value = '';
    document.getElementById('jenisJam').value = '';
    document.getElementById('persentase').value = 100;
    document.getElementById('nominalTetap').value = 0;
    document.getElementById('isActive').checked = true;
    document.getElementById('formTarif').action = '{{ route("kepegawaian.lembur.tarif.store") }}';
}
</script>
@endpush
