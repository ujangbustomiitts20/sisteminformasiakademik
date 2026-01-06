@extends('layouts.app')

@section('title', 'Detail Lembur')

@section('content')
<div class="page-title">
    <h4>Detail Pengajuan Lembur</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.lembur.index') }}">Lembur</a></li>
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
                        <label class="form-label text-muted small">Nama Pegawai</label>
                        <p class="mb-0 fw-bold">{{ $lembur->nama_pegawai }}</p>
                        <small class="text-muted">{{ $lembur->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal</label>
                        <p class="mb-0">{{ $lembur->tanggal->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jam Mulai</label>
                        <p class="mb-0">{{ $lembur->jam_mulai }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jam Selesai</label>
                        <p class="mb-0">{{ $lembur->jam_selesai }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Total Jam</label>
                        <p class="mb-0"><span class="badge bg-info fs-6">{{ number_format($lembur->total_jam, 1) }} jam</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jenis Hari</label>
                        <p class="mb-0"><span class="badge bg-{{ $lembur->jenis_hari_color }}">{{ $lembur->jenis_hari_label }}</span></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">Alasan Lembur</label>
                        <p class="mb-0">{{ $lembur->alasan }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Perhitungan Upah</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <td>Tarif per Jam</td>
                            <td class="text-end">{{ format_rupiah($lembur->tarif_per_jam) }}</td>
                        </tr>
                        <tr>
                            <td>Total Jam</td>
                            <td class="text-end">{{ number_format($lembur->total_jam, 1) }} jam</td>
                        </tr>
                        <tr class="table-primary">
                            <th>Total Upah Lembur</th>
                            <th class="text-end fs-5 text-primary">{{ format_rupiah($lembur->total_bayar ?? 0) }}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <i class="bi bi-clock-history text-primary" style="font-size: 4rem;"></i>
                <h3 class="mt-3">{{ number_format($lembur->durasi_jam, 1) }} Jam</h3>
                <p class="text-muted">Total Lembur</p>
                <hr>
                <h4 class="text-primary">{{ format_rupiah($lembur->total_bayar ?? 0) }}</h4>
                <p class="text-muted">Upah Lembur</p>
            </div>
        </div>

        <!-- Status Approval Timeline -->
        @if($lembur->dosen_id)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Status Approval</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Kaprodi:</span>
                    @if($lembur->status_kaprodi === 'pending')
                        <span class="badge bg-warning">Menunggu</span>
                    @elseif($lembur->status_kaprodi === 'disetujui')
                        <span class="badge bg-success">Disetujui</span>
                    @elseif($lembur->status_kaprodi === 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @else
                        <span class="badge bg-secondary">-</span>
                    @endif
                </div>
                @if($lembur->catatan_kaprodi)
                <small class="text-muted">Catatan: {{ $lembur->catatan_kaprodi }}</small>
                @endif
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <span>Admin:</span>
                    @if(in_array($lembur->status, ['diajukan', 'menunggu_admin']))
                        <span class="badge bg-warning">Menunggu</span>
                    @elseif($lembur->status === 'disetujui' || $lembur->status === 'selesai')
                        <span class="badge bg-success">Disetujui</span>
                    @elseif($lembur->status === 'ditolak' && $lembur->status_kaprodi !== 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @else
                        <span class="badge bg-secondary">-</span>
                    @endif
                </div>
                @if($lembur->catatan_approval)
                <small class="text-muted">Catatan: {{ $lembur->catatan_approval }}</small>
                @endif
            </div>
        </div>
        @endif

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(in_array($lembur->status, ['diajukan', 'menunggu_admin']))
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-lg me-1"></i> Setujui
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-1"></i> Tolak
                    </button>
                    @endif
                    @if($lembur->status == 'disetujui')
                    <form action="{{ route('kepegawaian.lembur.selesai', $lembur) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Tandai selesai?')">
                            <i class="bi bi-check-all me-1"></i> Selesaikan
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('kepegawaian.lembur.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Info Pengajuan</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">Diajukan pada:</small>
                <p class="mb-2">{{ $lembur->created_at->format('d/m/Y H:i') }}</p>
                @if($lembur->approved_by)
                <small class="text-muted">Disetujui oleh:</small>
                <p class="mb-2">{{ $lembur->approvedBy->name ?? '-' }}</p>
                <small class="text-muted">Tanggal persetujuan:</small>
                <p class="mb-0">{{ $lembur->approved_at ? $lembur->approved_at->format('d/m/Y H:i') : '-' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Approve -->
@if(in_array($lembur->status, ['diajukan', 'menunggu_admin']))
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.lembur.approve', $lembur) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Setujui Lembur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Setujui lembur untuk <strong>{{ $lembur->nama_pegawai }}</strong>?</p>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Tanggal</th>
                            <td>: {{ $lembur->tanggal->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <td>: {{ number_format($lembur->durasi_jam, 1) }} jam</td>
                        </tr>
                    </table>
                    <div class="mb-3">
                        <label class="form-label">Tarif Per Jam <span class="text-danger">*</span></label>
                        <input type="number" name="tarif_per_jam" class="form-control" value="{{ $lembur->tarif_per_jam ?? 50000 }}" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.lembur.reject', $lembur) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Tolak Lembur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tolak lembur untuk <strong>{{ $lembur->nama_pegawai }}</strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg me-1"></i> Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
