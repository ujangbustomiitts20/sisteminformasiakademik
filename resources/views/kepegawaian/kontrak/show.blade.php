@extends('layouts.app')

@section('title', 'Detail Kontrak Kerja')

@section('content')
<div class="page-title">
    <h4>Detail Kontrak Kerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.kontrak.index') }}">Kontrak Kerja</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informasi Kontrak</h5>
                <span class="badge bg-{{ $kontrak->status_color }} fs-6">{{ $kontrak->status_label }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nomor Kontrak</label>
                        <p class="mb-0 fw-bold">{{ $kontrak->nomor_kontrak }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jenis Kontrak</label>
                        <p class="mb-0">{{ $kontrak->jenis_kontrak_label }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nama Pegawai</label>
                        <p class="mb-0 fw-bold">{{ $kontrak->nama_pegawai }}</p>
                        <small class="text-muted">{{ $kontrak->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Gaji Pokok</label>
                        <p class="mb-0 fw-bold text-primary">{{ format_rupiah($kontrak->gaji_pokok) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Mulai</label>
                        <p class="mb-0">{{ $kontrak->tanggal_mulai->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Berakhir</label>
                        @if($kontrak->tanggal_berakhir)
                            <p class="mb-0">{{ $kontrak->tanggal_berakhir->format('d F Y') }}</p>
                            @if($kontrak->isExpiringSoon())
                                <span class="badge bg-warning">Akan berakhir dalam {{ $kontrak->tanggal_berakhir->diffInDays(now()) }} hari</span>
                            @endif
                        @else
                            <p class="mb-0 text-muted">Tidak Terbatas</p>
                        @endif
                    </div>
                    @if($kontrak->keterangan)
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">Keterangan</label>
                        <p class="mb-0">{{ $kontrak->keterangan }}</p>
                    </div>
                    @endif
                </div>

                @if($kontrak->file_kontrak)
                <hr>
                <div class="mb-3">
                    <label class="form-label text-muted small">File Kontrak</label>
                    <br>
                    <a href="{{ Storage::url($kontrak->file_kontrak) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-pdf me-1"></i> Lihat File Kontrak
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('kepegawaian.kontrak.edit', $kontrak) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit Kontrak
                    </a>
                    @if($kontrak->status == 'draft')
                    <form action="{{ route('kepegawaian.kontrak.aktivasi', $kontrak) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Aktifkan kontrak ini?')">
                            <i class="bi bi-check-lg me-1"></i> Aktifkan Kontrak
                        </button>
                    </form>
                    @endif
                    @if($kontrak->status == 'aktif' && $kontrak->tanggal_berakhir)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPerpanjang">
                        <i class="bi bi-arrow-repeat me-1"></i> Perpanjang Kontrak
                    </button>
                    @endif
                    <a href="{{ route('kepegawaian.kontrak.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Info Pembuatan</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">Dibuat oleh:</small>
                <p class="mb-2">{{ $kontrak->createdBy->name ?? '-' }}</p>
                <small class="text-muted">Tanggal dibuat:</small>
                <p class="mb-0">{{ $kontrak->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Perpanjang -->
@if($kontrak->status == 'aktif' && $kontrak->tanggal_berakhir)
<div class="modal fade" id="modalPerpanjang" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.kontrak.perpanjang', $kontrak) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Perpanjang Kontrak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Kontrak saat ini berakhir: <strong>{{ $kontrak->tanggal_berakhir->format('d F Y') }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Berakhir Baru <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_berakhir_baru" class="form-control" required min="{{ $kontrak->tanggal_berakhir->addDay()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perpanjang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
