@extends('layouts.app')

@section('title', 'Detail Cuti')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Detail Pengajuan Cuti</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dosen.cuti.index') }}">Pengajuan Cuti</a></li>
                <li class="breadcrumb-item active">{{ $cuti->no_pengajuan }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('dosen.cuti.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Pengajuan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">No. Pengajuan</label>
                        <p class="fw-semibold mb-0">{{ $cuti->no_pengajuan }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Status</label>
                        <p class="mb-0">
                            @php
                                $badgeClass = match($cuti->status) {
                                    'draft' => 'secondary',
                                    'diajukan' => 'warning',
                                    'disetujui_atasan' => 'info',
                                    'disetujui' => 'success',
                                    'ditolak' => 'danger',
                                    'dibatalkan' => 'dark',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }} fs-6">
                                {{ \App\Models\CutiPegawai::STATUS[$cuti->status] ?? $cuti->status }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Jenis Cuti</label>
                        <p class="fw-semibold mb-0">{{ \App\Models\CutiPegawai::JENIS_CUTI[$cuti->jenis_cuti] ?? $cuti->jenis_cuti }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Jumlah Hari</label>
                        <p class="fw-semibold mb-0">{{ $cuti->jumlah_hari }} hari</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Tanggal Mulai</label>
                        <p class="fw-semibold mb-0">{{ $cuti->tanggal_mulai->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Tanggal Selesai</label>
                        <p class="fw-semibold mb-0">{{ $cuti->tanggal_selesai->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small">Alasan</label>
                        <p class="mb-0">{{ $cuti->alasan }}</p>
                    </div>
                    @if($cuti->alamat_selama_cuti)
                    <div class="col-md-8">
                        <label class="form-label text-muted small">Alamat Selama Cuti</label>
                        <p class="mb-0">{{ $cuti->alamat_selama_cuti }}</p>
                    </div>
                    @endif
                    @if($cuti->no_telepon_selama_cuti)
                    <div class="col-md-4">
                        <label class="form-label text-muted small">No. Telepon</label>
                        <p class="mb-0">{{ $cuti->no_telepon_selama_cuti }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($cuti->dokumen_pendukung)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-paperclip me-2"></i>Dokumen Pendukung</h6>
            </div>
            <div class="card-body">
                <a href="{{ asset('storage/' . $cuti->dokumen_pendukung) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i>Lihat Dokumen
                </a>
            </div>
        </div>
        @endif

        @if($cuti->catatan_atasan || $cuti->catatan_admin)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Catatan</h6>
            </div>
            <div class="card-body">
                @if($cuti->catatan_atasan)
                <div class="mb-3">
                    <label class="form-label text-muted small">Catatan Atasan</label>
                    <p class="mb-0">{{ $cuti->catatan_atasan }}</p>
                </div>
                @endif
                @if($cuti->catatan_admin)
                <div>
                    <label class="form-label text-muted small">Catatan Admin</label>
                    <p class="mb-0">{{ $cuti->catatan_admin }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Saldo Cuti</h6>
            </div>
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Sisa Cuti Sebelum Pengajuan</p>
                <h3 class="mb-3">{{ $cuti->sisa_cuti_sebelum ?? '-' }} hari</h3>
                @if($cuti->status === 'disetujui')
                <p class="text-muted small mb-1">Sisa Cuti Sesudah Pengajuan</p>
                <h3 class="text-success mb-0">{{ $cuti->sisa_cuti_sesudah ?? ($cuti->sisa_cuti_sebelum - $cuti->jumlah_hari) }} hari</h3>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="badge bg-primary rounded-circle p-2">
                                    <i class="bi bi-send"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">Diajukan</p>
                                <small class="text-muted">{{ $cuti->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </li>
                    @if($cuti->tanggal_persetujuan_atasan)
                    <li class="mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="badge bg-info rounded-circle p-2">
                                    <i class="bi bi-person-check"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">Disetujui Atasan</p>
                                <small class="text-muted">{{ $cuti->tanggal_persetujuan_atasan->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </li>
                    @endif
                    @if($cuti->tanggal_disetujui)
                    <li>
                        <div class="d-flex">
                            <div class="me-3">
                                <span class="badge bg-success rounded-circle p-2">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">{{ $cuti->status === 'disetujui' ? 'Disetujui' : 'Ditolak' }}</p>
                                <small class="text-muted">{{ $cuti->tanggal_disetujui->format('d/m/Y') }}</small>
                                @if($cuti->disetujuiOleh)
                                <br><small class="text-muted">Oleh: {{ $cuti->disetujuiOleh->name }}</small>
                                @endif
                            </div>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

        @if($cuti->status === 'diajukan')
        <div class="card shadow-sm border-danger">
            <div class="card-body">
                <form action="{{ route('dosen.cuti.destroy', $cuti) }}" method="POST" 
                      onsubmit="return confirm('Yakin ingin membatalkan pengajuan cuti ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-x-circle me-1"></i>Batalkan Pengajuan
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
