@extends('layouts.app')

@section('title', 'Sertifikasi Dosen')

@section('content')
<div class="page-title">
    <h4>Sertifikasi Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Sertifikasi Dosen</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Sertifikasi</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-award fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Serdos Aktif</h6>
                        <h3 class="mb-0">{{ $stats['serdos_aktif'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-patch-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Akan Expired</h6>
                        <h3 class="mb-0">{{ $stats['akan_expired'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Sertifikasi Lain</h6>
                        <h3 class="mb-0">{{ $stats['lainnya'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-file-earmark-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions & Filters -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Sertifikasi
                </button>
            </div>
            <div class="col-md-6">
                <form action="{{ route('kepegawaian.sertifikasi.index') }}" method="GET" class="d-flex gap-2 justify-content-end">
                    <select name="jenis" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="serdos" {{ request('jenis') == 'serdos' ? 'selected' : '' }}>Serdos</option>
                        <option value="kompetensi" {{ request('jenis') == 'kompetensi' ? 'selected' : '' }}>Kompetensi</option>
                        <option value="profesi" {{ request('jenis') == 'profesi' ? 'selected' : '' }}>Profesi</option>
                        <option value="lainnya" {{ request('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                    <input type="text" name="search" class="form-control form-control-sm" style="width: 200px;" placeholder="Cari nama/nomor..." value="{{ request('search') }}">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Dosen</th>
                        <th>Jenis Sertifikasi</th>
                        <th>Nama Sertifikasi</th>
                        <th>Nomor</th>
                        <th>Tanggal Terbit</th>
                        <th>Tanggal Expired</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sertifikasiList as $i => $sertifikasi)
                        <tr>
                            <td>{{ $sertifikasiList->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $sertifikasi->dosen->nama_lengkap ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $sertifikasi->dosen->nidn ?? '' }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ $sertifikasi->jenis_label }}</span></td>
                            <td>{{ $sertifikasi->nama_sertifikasi }}</td>
                            <td>{{ $sertifikasi->nomor_sertifikat }}</td>
                            <td>{{ $sertifikasi->tanggal_terbit->format('d/m/Y') }}</td>
                            <td>
                                @if($sertifikasi->tanggal_expired)
                                    {{ $sertifikasi->tanggal_expired->format('d/m/Y') }}
                                    @if($sertifikasi->isExpiringSoon())
                                        <br><span class="badge bg-warning">Akan expired</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $sertifikasi->status_color }}">{{ $sertifikasi->status_label }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('kepegawaian.sertifikasi.show', $sertifikasi) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('kepegawaian.sertifikasi.edit', $sertifikasi) }}" class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.sertifikasi.destroy', $sertifikasi) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Hapus" onclick="return confirm('Yakin hapus data ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada data sertifikasi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $sertifikasiList->links() }}
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.sertifikasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sertifikasi Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Dosen <span class="text-danger">*</span></label>
                            <select name="dosen_id" class="form-select" required>
                                <option value="">Pilih Dosen</option>
                                @foreach($dosenList as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }} - {{ $dosen->nidn }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Sertifikasi <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="serdos">Sertifikasi Dosen (Serdos)</option>
                                <option value="kompetensi">Sertifikasi Kompetensi</option>
                                <option value="profesi">Sertifikasi Profesi</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Sertifikasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_sertifikasi" class="form-control" required placeholder="e.g. Serdos Bidang Informatika">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Sertifikat <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_sertifikat" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penerbit <span class="text-danger">*</span></label>
                            <input type="text" name="penerbit" class="form-control" required placeholder="e.g. Kemendikbudristek">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bidang Studi</label>
                            <input type="text" name="bidang_studi" class="form-control" placeholder="e.g. Teknik Informatika">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Terbit <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_terbit" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Expired</label>
                            <input type="date" name="tanggal_expired" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak expired</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">File Sertifikat (PDF)</label>
                            <input type="file" name="file_sertifikat" class="form-control" accept=".pdf">
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
