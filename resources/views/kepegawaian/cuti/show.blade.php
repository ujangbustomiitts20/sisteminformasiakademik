@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti')

@section('content')
<div class="page-title">
    <h4>Detail Pengajuan Cuti</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.cuti.index') }}">Cuti Pegawai</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-text me-2"></i>Data Pengajuan Cuti</span>
                {!! $cuti->status_badge !!}
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">No. Pengajuan</label>
                        <p class="mb-0"><code class="fs-5">{{ $cuti->no_pengajuan }}</code></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Tanggal Pengajuan</label>
                        <p class="mb-0">{{ $cuti->created_at->format('d F Y, H:i') }}</p>
                    </div>
                </div>

                <hr>

                <h6 class="mb-3"><i class="bi bi-person me-2"></i>Data Pemohon</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Nama Pegawai</label>
                        <p class="mb-0">
                            @if($cuti->dosen)
                            <strong>{{ $cuti->dosen->nama_lengkap }}</strong>
                            <span class="badge bg-info ms-1">Dosen</span>
                            @elseif($cuti->pegawai)
                            <strong>{{ $cuti->pegawai->nama }}</strong>
                            <span class="badge bg-secondary ms-1">Tendik</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">NIP/NIDN</label>
                        <p class="mb-0">
                            @if($cuti->dosen)
                            {{ $cuti->dosen->nidn ?? $cuti->dosen->nip }}
                            @elseif($cuti->pegawai)
                            {{ $cuti->pegawai->nip }}
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <h6 class="mb-3"><i class="bi bi-calendar-event me-2"></i>Detail Cuti</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Jenis Cuti</label>
                        <p class="mb-0"><span class="badge bg-primary">{{ $cuti->jenis_cuti_label }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Tanggal Mulai</label>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Tanggal Selesai</label>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d F Y') }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Jumlah Hari</label>
                        <p class="mb-0"><strong class="fs-5">{{ $cuti->jumlah_hari }} hari</strong></p>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label text-muted small">Atasan Langsung</label>
                        <p class="mb-0">{{ $cuti->atasanLangsung->nama_lengkap ?? '-' }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Alasan Cuti</label>
                    <div class="bg-light p-3 rounded">
                        {{ $cuti->alasan }}
                    </div>
                </div>

                @if($cuti->alamat_selama_cuti)
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label text-muted small">Alamat Selama Cuti</label>
                        <p class="mb-0">{{ $cuti->alamat_selama_cuti }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">No. Telepon</label>
                        <p class="mb-0">{{ $cuti->no_telepon ?? '-' }}</p>
                    </div>
                </div>
                @endif

                @if($cuti->dokumen_pendukung)
                <div class="mb-3">
                    <label class="form-label text-muted small">Dokumen Pendukung</label>
                    <div>
                        <a href="{{ Storage::url($cuti->dokumen_pendukung) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Lihat Dokumen
                        </a>
                    </div>
                </div>
                @endif

                @if($cuti->status == 'ditolak' && $cuti->catatan_admin)
                <div class="alert alert-danger">
                    <h6 class="alert-heading"><i class="bi bi-x-circle me-1"></i>Alasan Penolakan:</h6>
                    <p class="mb-0">{{ $cuti->catatan_admin }}</p>
                </div>
                @endif

                @if($cuti->status == 'disetujui' && $cuti->catatan_admin)
                <div class="alert alert-success">
                    <h6 class="alert-heading"><i class="bi bi-check-circle me-1"></i>Catatan Persetujuan:</h6>
                    <p class="mb-0">{{ $cuti->catatan_admin }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Riwayat Persetujuan
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <p class="mb-0 small text-muted">{{ $cuti->created_at->format('d/m/Y H:i') }}</p>
                            <strong>Pengajuan dibuat</strong>
                            <p class="small text-muted mb-0">Oleh: {{ $cuti->createdBy->name ?? 'System' }}</p>
                        </div>
                    </div>
                    
                    @if($cuti->disetujui_atasan_pada)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <p class="mb-0 small text-muted">{{ \Carbon\Carbon::parse($cuti->disetujui_atasan_pada)->format('d/m/Y H:i') }}</p>
                            <strong>Disetujui Atasan</strong>
                            <p class="small text-muted mb-0">Oleh: {{ $cuti->disetujuiAtasanOlehUser->name ?? '-' }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($cuti->tanggal_disetujui)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <p class="mb-0 small text-muted">{{ \Carbon\Carbon::parse($cuti->tanggal_disetujui)->format('d/m/Y H:i') }}</p>
                            <strong>Disetujui</strong>
                            <p class="small text-muted mb-0">Oleh: {{ $cuti->disetujuiOleh->name ?? '-' }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($cuti->status == 'ditolak')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <p class="mb-0 small text-muted">{{ $cuti->updated_at->format('d/m/Y H:i') }}</p>
                            <strong>Ditolak</strong>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($cuti->status == 'diajukan' || $cuti->status == 'disetujui_atasan')
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-hand-thumbs-up me-2"></i>Aksi
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-lg me-1"></i>Setujui Cuti
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-1"></i>Tolak Cuti
                    </button>
                </div>
            </div>
        </div>
        @endif

        <div class="mt-3">
            <a href="{{ route('kepegawaian.cuti.index') }}" class="btn btn-secondary w-100">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

@if($cuti->status == 'diajukan' || $cuti->status == 'disetujui_atasan')
<!-- Modal Approve -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.cuti.approve', $cuti) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Setujui Cuti</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menyetujui pengajuan cuti ini?</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan_admin" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.cuti.reject', $cuti) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Tolak Cuti</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menolak pengajuan cuti ini?</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_admin" class="form-control" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg me-1"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-item:before {
    content: '';
    position: absolute;
    left: -24px;
    top: 8px;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item:last-child:before {
    display: none;
}
.timeline-marker {
    position: absolute;
    left: -30px;
    top: 3px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
}
</style>
@endsection
