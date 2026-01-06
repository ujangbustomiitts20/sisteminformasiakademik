@extends('layouts.app')

@section('title', 'Detail Pelanggaran')

@section('content')
<div class="page-title">
    <h4>Detail Pelanggaran</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pelanggaran.index') }}">Pelanggaran</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informasi Pelanggaran</h5>
                <span class="badge bg-{{ $pelanggaran->status_color }}">{{ $pelanggaran->status_label }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nama Pegawai</label>
                        <p class="mb-0 fw-bold">{{ $pelanggaran->nama_pegawai }}</p>
                        <small class="text-muted">{{ $pelanggaran->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jenis Pelanggaran</label>
                        <p class="mb-0">{{ $pelanggaran->jenisPelanggaran->nama ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Kejadian</label>
                        <p class="mb-0">{{ $pelanggaran->tanggal_kejadian->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tingkat Pelanggaran</label>
                        <p class="mb-0"><span class="badge bg-{{ $pelanggaran->tingkat_color }}">{{ $pelanggaran->tingkat_label }}</span></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">Deskripsi</label>
                        <p class="mb-0">{{ $pelanggaran->deskripsi }}</p>
                    </div>
                    @if($pelanggaran->file_bukti)
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">File Bukti</label>
                        <br>
                        <a href="{{ Storage::url($pelanggaran->file_bukti) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark me-1"></i> Lihat Bukti
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($pelanggaran->sanksi)
        <div class="card mt-3">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-shield-exclamation me-2"></i>Sanksi yang Diberikan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jenis Sanksi</label>
                        <p class="mb-0 fw-bold">{{ $pelanggaran->sanksi->jenis_sanksi_label }}</p>
                    </div>
                    @if($pelanggaran->sanksi->nomor_sk)
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nomor SK</label>
                        <p class="mb-0">{{ $pelanggaran->sanksi->nomor_sk }}</p>
                    </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Mulai</label>
                        <p class="mb-0">{{ $pelanggaran->sanksi->tanggal_mulai->format('d F Y') }}</p>
                    </div>
                    @if($pelanggaran->sanksi->tanggal_berakhir)
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Berakhir</label>
                        <p class="mb-0">{{ $pelanggaran->sanksi->tanggal_berakhir->format('d F Y') }}</p>
                    </div>
                    @endif
                    @if($pelanggaran->sanksi->keterangan)
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">Keterangan</label>
                        <p class="mb-0">{{ $pelanggaran->sanksi->keterangan }}</p>
                    </div>
                    @endif
                    @if($pelanggaran->sanksi->file_sk)
                    <div class="col-12">
                        <a href="{{ Storage::url($pelanggaran->sanksi->file_sk) }}" target="_blank" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-file-pdf me-1"></i> Lihat SK Sanksi
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Update Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.pelanggaran.status', $pelanggaran) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="dilaporkan" {{ $pelanggaran->status == 'dilaporkan' ? 'selected' : '' }}>Dilaporkan</option>
                            <option value="investigasi" {{ $pelanggaran->status == 'investigasi' ? 'selected' : '' }}>Investigasi</option>
                            <option value="terbukti" {{ $pelanggaran->status == 'terbukti' ? 'selected' : '' }}>Terbukti</option>
                            <option value="tidak_terbukti" {{ $pelanggaran->status == 'tidak_terbukti' ? 'selected' : '' }}>Tidak Terbukti</option>
                            <option value="selesai" {{ $pelanggaran->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($pelanggaran->status == 'terbukti' && !$pelanggaran->sanksi)
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSanksi">
                        <i class="bi bi-shield-exclamation me-1"></i> Berikan Sanksi
                    </button>
                    @endif
                    <a href="{{ route('kepegawaian.pelanggaran.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Berikan Sanksi -->
@if($pelanggaran->status == 'terbukti' && !$pelanggaran->sanksi)
<div class="modal fade" id="modalSanksi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.pelanggaran.sanksi', $pelanggaran) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Berikan Sanksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Sanksi <span class="text-danger">*</span></label>
                        <select name="jenis_sanksi" class="form-select" required>
                            <option value="">Pilih Jenis</option>
                            <option value="teguran_lisan">Teguran Lisan</option>
                            <option value="teguran_tertulis">Teguran Tertulis</option>
                            <option value="sp1">Surat Peringatan 1</option>
                            <option value="sp2">Surat Peringatan 2</option>
                            <option value="sp3">Surat Peringatan 3</option>
                            <option value="demosi">Demosi</option>
                            <option value="mutasi">Mutasi</option>
                            <option value="skorsing">Skorsing</option>
                            <option value="phk">PHK</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor SK</label>
                        <input type="text" name="nomor_sk" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File SK (PDF)</label>
                        <input type="file" name="file_sk" class="form-control" accept=".pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Berikan Sanksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
