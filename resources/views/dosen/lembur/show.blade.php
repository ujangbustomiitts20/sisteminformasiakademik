@extends('layouts.app')

@section('title', 'Detail Pengajuan Lembur')

@section('content')
<div class="page-title">
    <h4>Detail Pengajuan Lembur</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.lembur.index') }}">Lembur</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informasi Lembur</h5>
                <span class="badge bg-{{ $lembur->status_color }} fs-6">{{ $lembur->full_status_label ?? $lembur->status_label }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal</label>
                        <p class="mb-0 fw-bold">{{ $lembur->tanggal->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jam</label>
                        <p class="mb-0">
                            {{ \Carbon\Carbon::parse($lembur->jam_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($lembur->jam_selesai)->format('H:i') }}
                            <span class="badge bg-info">{{ number_format($lembur->durasi_jam, 1) }} jam</span>
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">Alasan Lembur</label>
                        <p class="mb-0">{{ $lembur->alasan }}</p>
                    </div>
                    @if($lembur->pekerjaan_yang_dilakukan)
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">Pekerjaan yang Dilakukan</label>
                        <p class="mb-0">{{ $lembur->pekerjaan_yang_dilakukan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Perhitungan Upah (jika sudah disetujui) -->
        @if($lembur->status === 'disetujui')
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Perhitungan Upah Lembur</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <td>Durasi Lembur</td>
                            <td class="text-end">{{ number_format($lembur->durasi_jam, 1) }} jam</td>
                        </tr>
                        <tr>
                            <td>Tarif per Jam</td>
                            <td class="text-end">{{ format_rupiah($lembur->tarif_per_jam ?? 0) }}</td>
                        </tr>
                        <tr class="table-success">
                            <th>Total Upah Lembur</th>
                            <th class="text-end fs-5 text-success">{{ format_rupiah($lembur->total_bayar ?? 0) }}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endif
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
                        @if($lembur->status_kaprodi === 'disetujui')
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        @elseif($lembur->status_kaprodi === 'ditolak')
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
                        @if($lembur->status_kaprodi === 'pending')
                            <small class="text-warning">Menunggu persetujuan</small>
                        @elseif($lembur->status_kaprodi === 'disetujui')
                            <small class="text-success">Disetujui</small>
                            @if($lembur->tanggal_approval_kaprodi)
                            <br><small class="text-muted">{{ $lembur->tanggal_approval_kaprodi->format('d/m/Y H:i') }}</small>
                            @endif
                        @elseif($lembur->status_kaprodi === 'ditolak')
                            <small class="text-danger">Ditolak</small>
                        @endif
                        @if($lembur->catatan_kaprodi)
                        <div class="alert alert-light mt-2 mb-0 py-1 px-2 small">
                            {{ $lembur->catatan_kaprodi }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Step 2: Admin -->
                <div class="d-flex">
                    <div class="me-3">
                        @if(in_array($lembur->status, ['disetujui', 'selesai']))
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        @elseif($lembur->status === 'ditolak' && $lembur->status_kaprodi !== 'ditolak')
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-x-lg"></i>
                            </div>
                        @elseif($lembur->status === 'menunggu_admin')
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
                        @if($lembur->status === 'menunggu_admin')
                            <small class="text-warning">Menunggu persetujuan</small>
                        @elseif(in_array($lembur->status, ['disetujui', 'selesai']))
                            <small class="text-success">Disetujui</small>
                            @if($lembur->tanggal_disetujui)
                            <br><small class="text-muted">{{ $lembur->tanggal_disetujui->format('d/m/Y H:i') }}</small>
                            @endif
                        @elseif($lembur->status === 'ditolak' && $lembur->status_kaprodi !== 'ditolak')
                            <small class="text-danger">Ditolak</small>
                        @else
                            <small class="text-muted">Menunggu Kaprodi</small>
                        @endif
                        @if($lembur->catatan_approval)
                        <div class="alert alert-light mt-2 mb-0 py-1 px-2 small">
                            {{ $lembur->catatan_approval }}
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
                    @if($lembur->status === 'diajukan')
                    <form action="{{ route('dosen.lembur.destroy', $lembur) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Batalkan pengajuan ini?')">
                            <i class="bi bi-x-lg me-1"></i> Batalkan Pengajuan
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('dosen.lembur.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div class="card mt-3">
            <div class="card-body">
                <small class="text-muted">Diajukan pada:</small>
                <p class="mb-0">{{ $lembur->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
