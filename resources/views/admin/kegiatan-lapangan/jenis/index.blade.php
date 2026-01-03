@extends('layouts.app')

@section('title', 'Kegiatan Lapangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manajemen PKL/Magang/KKN</h1>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.kegiatan-lapangan.jenis.index') }}">Jenis Kegiatan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kegiatan-lapangan.mitra.index') }}">Mitra Kegiatan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kegiatan-lapangan.periode.index') }}">Periode</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kegiatan-lapangan.pendaftaran.index') }}">Pendaftaran</a>
        </li>
    </ul>

    <!-- Card Jenis Kegiatan -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Jenis Kegiatan Lapangan</h6>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addJenisModal">
                <i class="bi bi-plus-circle"></i> Tambah Jenis
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Durasi</th>
                            <th>SKS</th>
                            <th>Semester Min</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenis as $item)
                        <tr>
                            <td>{{ $item->kode }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                            <td>{{ $item->durasi_minggu }} minggu</td>
                            <td>{{ $item->sks }}</td>
                            <td>Semester {{ $item->semester_minimal }}</td>
                            <td>
                                @if($item->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editJenisModal{{ $item->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.kegiatan-lapangan.jenis.destroy', $item->hashid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada jenis kegiatan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Jenis -->
<div class="modal fade" id="addJenisModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.kegiatan-lapangan.jenis.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" name="kode" class="form-control" required maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Durasi (minggu)</label>
                            <input type="number" name="durasi_minggu" class="form-control" required min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">SKS</label>
                            <input type="number" name="sks" class="form-control" required min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Semester Min</label>
                            <input type="number" name="semester_minimal" class="form-control" required min="1" max="8">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKS Minimal</label>
                        <input type="number" name="sks_minimal" class="form-control" required min="0" value="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                            <label class="form-check-label">Aktif</label>
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

<!-- Edit Modals -->
@foreach($jenis as $item)
<div class="modal fade" id="editJenisModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.kegiatan-lapangan.jenis.update', $item->hashid) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jenis Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" name="kode" class="form-control" value="{{ $item->kode }}" required maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ $item->nama }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3">{{ $item->deskripsi }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Durasi (minggu)</label>
                            <input type="number" name="durasi_minggu" class="form-control" value="{{ $item->durasi_minggu }}" required min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">SKS</label>
                            <input type="number" name="sks" class="form-control" value="{{ $item->sks }}" required min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Semester Min</label>
                            <input type="number" name="semester_minimal" class="form-control" value="{{ $item->semester_minimal }}" required min="1" max="8">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKS Minimal</label>
                        <input type="number" name="sks_minimal" class="form-control" value="{{ $item->sks_minimal }}" required min="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $item->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Aktif</label>
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
@endforeach
@endsection
