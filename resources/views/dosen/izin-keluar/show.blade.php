@extends('layouts.app')

@section('title', 'Detail Izin Keluar')

@section('content')
<div class="page-title">
    <h4>Detail Izin Keluar</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.izin-keluar.index') }}">Izin Keluar</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informasi Izin Keluar</h5>
                <span class="badge bg-{{ $izinKeluar->status_color }} fs-6">{{ $izinKeluar->full_status_label }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal</label>
                        <p class="mb-0 fw-bold">{{ $izinKeluar->tanggal->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jam</label>
                        <p class="mb-0">
                            {{ \Carbon\Carbon::parse($izinKeluar->jam_keluar)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($izinKeluar->jam_kembali)->format('H:i') }}
                            @if($izinKeluar->durasi)
                            <span class="badge bg-info">{{ $izinKeluar->durasi }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Keperluan</label>
                        <p class="mb-0">{{ $izinKeluar->keperluan_label }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tujuan</label>
                        <p class="mb-0">{{ $izinKeluar->tujuan ?: '-' }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">Keterangan</label>
                        <p class="mb-0">{{ $izinKeluar->keterangan }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Status Approval Timeline -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Status Approval</h6>
            </div>
            <div class="card-body">
                <!-- Step 1: Kaprodi -->
                <div class="d-flex mb-3">
                    <div class="me-3">
                        @if($izinKeluar->status_kaprodi === 'disetujui')
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        @elseif($izinKeluar->status_kaprodi === 'ditolak')
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-x-lg"></i>
                            </div>
                        @else
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h6 class="mb-0">Kaprodi</h6>
                        @if($izinKeluar->status_kaprodi === 'pending')
                            <small class="text-warning">Menunggu persetujuan</small>
                        @elseif($izinKeluar->status_kaprodi === 'disetujui')
                            <small class="text-success">Disetujui</small>
                            @if($izinKeluar->tanggal_approval_kaprodi)
                            <br><small class="text-muted">{{ $izinKeluar->tanggal_approval_kaprodi->format('d/m/Y H:i') }}</small>
                            @endif
                        @elseif($izinKeluar->status_kaprodi === 'ditolak')
                            <small class="text-danger">Ditolak</small>
                        @endif
                        @if($izinKeluar->catatan_kaprodi)
                        <div class="alert alert-light mt-2 mb-0 py-1 px-2 small">
                            {{ $izinKeluar->catatan_kaprodi }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Step 2: Admin -->
                <div class="d-flex">
                    <div class="me-3">
                        @if(in_array($izinKeluar->status, ['disetujui', 'selesai']))
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        @elseif($izinKeluar->status === 'ditolak' && $izinKeluar->status_kaprodi !== 'ditolak')
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-x-lg"></i>
                            </div>
                        @elseif($izinKeluar->status === 'menunggu_admin')
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        @else
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-dash"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h6 class="mb-0">Admin</h6>
                        @if($izinKeluar->status === 'menunggu_admin')
                            <small class="text-warning">Menunggu persetujuan</small>
                        @elseif(in_array($izinKeluar->status, ['disetujui', 'selesai']))
                            <small class="text-success">Disetujui</small>
                            @if($izinKeluar->tanggal_disetujui)
                            <br><small class="text-muted">{{ $izinKeluar->tanggal_disetujui->format('d/m/Y H:i') }}</small>
                            @endif
                        @elseif($izinKeluar->status === 'ditolak' && $izinKeluar->status_kaprodi !== 'ditolak')
                            <small class="text-danger">Ditolak</small>
                        @else
                            <small class="text-muted">Menunggu Kaprodi</small>
                        @endif
                        @if($izinKeluar->catatan_approval)
                        <div class="alert alert-light mt-2 mb-0 py-1 px-2 small">
                            {{ $izinKeluar->catatan_approval }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($izinKeluar->status === 'diajukan')
                    <form action="{{ route('dosen.izin-keluar.destroy', $izinKeluar) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Batalkan pengajuan ini?')">
                            <i class="bi bi-x-lg me-1"></i> Batalkan Pengajuan
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('dosen.izin-keluar.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div class="card mt-3">
            <div class="card-body">
                <small class="text-muted">Diajukan pada:</small>
                <p class="mb-0">{{ $izinKeluar->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
