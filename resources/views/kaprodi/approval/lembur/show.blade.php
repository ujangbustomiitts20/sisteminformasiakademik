@extends('layouts.app')

@section('title', 'Detail Pengajuan Lembur')

@section('content')
<div class="page-title">
    <h4>Detail Pengajuan Lembur</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.approval.dashboard') }}">Approval</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.approval.lembur.index') }}">Lembur</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Detail Pengajuan -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informasi Pengajuan Lembur</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="text-muted" width="40%">Nama Dosen</th>
                                <td>: <strong>{{ $lembur->dosen?->nama ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">NIDN</th>
                                <td>: {{ $lembur->dosen?->nidn ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Program Studi</th>
                                <td>: {{ $lembur->dosen?->programStudi?->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tanggal</th>
                                <td>: {{ $lembur->tanggal?->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Jam</th>
                                <td>: {{ \Carbon\Carbon::parse($lembur->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($lembur->jam_selesai)->format('H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="text-muted" width="40%">Durasi</th>
                                <td>: <span class="badge bg-info">{{ number_format($lembur->durasi_jam, 1) }} jam</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tarif</th>
                                <td>: {{ $lembur->tarifLembur?->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tarif Per Jam</th>
                                <td>: Rp {{ number_format($lembur->tarifLembur?->tarif_per_jam ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Total Nominal</th>
                                <td>: <strong class="text-success">Rp {{ number_format($lembur->total_nominal ?? 0, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tanggal Pengajuan</th>
                                <td>: {{ $lembur->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted">Alasan Lembur</h6>
                    <p class="mb-0">{{ $lembur->alasan }}</p>
                </div>

                @if($lembur->keterangan)
                <div class="mb-3">
                    <h6 class="text-muted">Keterangan Tambahan</h6>
                    <p class="mb-0">{{ $lembur->keterangan }}</p>
                </div>
                @endif

                @if($lembur->lampiran)
                <div class="mb-3">
                    <h6 class="text-muted">Lampiran</h6>
                    <a href="{{ Storage::url($lembur->lampiran) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="bi bi-file-earmark-text me-1"></i> Lihat Lampiran
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status Pengajuan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6><i class="bi bi-person-badge me-1"></i> Status Kaprodi</h6>
                            @if($lembur->status_kaprodi === 'pending')
                                <span class="badge bg-warning">Menunggu Approval</span>
                            @elseif($lembur->status_kaprodi === 'disetujui')
                                <span class="badge bg-success">Disetujui</span>
                                <br><small class="text-muted">{{ $lembur->tanggal_approval_kaprodi?->format('d/m/Y H:i') }}</small>
                            @elseif($lembur->status_kaprodi === 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                                <br><small class="text-muted">{{ $lembur->tanggal_approval_kaprodi?->format('d/m/Y H:i') }}</small>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                            
                            @if($lembur->catatan_kaprodi)
                                <div class="alert alert-light mt-2 mb-0 small">
                                    <strong>Catatan:</strong> {{ $lembur->catatan_kaprodi }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6><i class="bi bi-building me-1"></i> Status Admin</h6>
                            @if($lembur->status === 'diajukan' || $lembur->status === 'menunggu_admin')
                                <span class="badge bg-warning">Menunggu Approval</span>
                            @elseif($lembur->status === 'disetujui')
                                <span class="badge bg-success">Disetujui</span>
                                <br><small class="text-muted">{{ $lembur->tanggal_approval?->format('d/m/Y H:i') }}</small>
                            @elseif($lembur->status === 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                                <br><small class="text-muted">{{ $lembur->tanggal_approval?->format('d/m/Y H:i') }}</small>
                            @endif
                            
                            @if($lembur->catatan_admin)
                                <div class="alert alert-light mt-2 mb-0 small">
                                    <strong>Catatan:</strong> {{ $lembur->catatan_admin }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Panel -->
    <div class="col-lg-4">
        @if($lembur->status_kaprodi === 'pending')
            <!-- Approve Form -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-check-lg me-1"></i> Setujui Pengajuan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('kaprodi.approval.lembur.approve', $lembur) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
                        </div>
                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle me-1"></i>
                            Pengajuan akan diteruskan ke Admin untuk penentuan tarif dan approval final.
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-lg me-1"></i> Setujui Pengajuan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Reject Form -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="bi bi-x-lg me-1"></i> Tolak Pengajuan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('kaprodi.approval.lembur.reject', $lembur) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center">
                    @if($lembur->status_kaprodi === 'disetujui')
                        <div class="text-success mb-3">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                        <h5>Pengajuan Telah Disetujui</h5>
                        <p class="text-muted">Pengajuan ini telah Anda setujui dan diteruskan ke Admin.</p>
                    @elseif($lembur->status_kaprodi === 'ditolak')
                        <div class="text-danger mb-3">
                            <i class="bi bi-x-circle fs-1"></i>
                        </div>
                        <h5>Pengajuan Telah Ditolak</h5>
                        <p class="text-muted">Pengajuan ini telah Anda tolak.</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-3">
            <a href="{{ route('kaprodi.approval.lembur.index') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
