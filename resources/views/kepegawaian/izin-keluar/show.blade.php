@extends('layouts.app')

@section('title', 'Detail Izin Keluar')

@section('content')
<div class="page-title">
    <h4>Detail Izin Keluar</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.izin-keluar.index') }}">Izin Keluar</a></li>
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
                        <label class="form-label text-muted small">Nama Pegawai</label>
                        <p class="mb-0 fw-bold">{{ $izinKeluar->nama_pegawai }}</p>
                        <small class="text-muted">{{ $izinKeluar->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal</label>
                        <p class="mb-0">{{ $izinKeluar->tanggal->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jam Keluar</label>
                        <p class="mb-0 fw-bold">{{ $izinKeluar->jam_keluar }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Estimasi Jam Kembali</label>
                        <p class="mb-0">{{ $izinKeluar->jam_kembali }}</p>
                    </div>
                    @if($izinKeluar->jam_kembali_aktual)
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Jam Kembali Aktual</label>
                        <p class="mb-0 fw-bold text-success">{{ $izinKeluar->jam_kembali_aktual }}</p>
                    </div>
                    @endif
                    @if($izinKeluar->tujuan)
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tujuan</label>
                        <p class="mb-0">{{ $izinKeluar->tujuan }}</p>
                    </div>
                    @endif
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small">Keperluan</label>
                        <p class="mb-0">{{ $izinKeluar->keperluan }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                @if($izinKeluar->status == 'diajukan' && $izinKeluar->status_kaprodi == 'pending')
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Menunggu Kaprodi</h5>
                    <p class="text-muted">Pengajuan sedang menunggu approval dari Kaprodi</p>
                @elseif($izinKeluar->status == 'menunggu_admin')
                    <i class="bi bi-hourglass-split text-info" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Menunggu Admin</h5>
                    <p class="text-success"><i class="bi bi-check-circle"></i> Disetujui Kaprodi</p>
                @elseif($izinKeluar->status == 'disetujui')
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Disetujui</h5>
                    <p class="text-muted">Pegawai sedang keluar</p>
                @elseif($izinKeluar->status == 'ditolak')
                    <i class="bi bi-x-circle text-danger" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Ditolak</h5>
                    @if($izinKeluar->status_kaprodi == 'ditolak')
                    <p class="text-danger">Ditolak oleh Kaprodi</p>
                    @endif
                @elseif($izinKeluar->status == 'selesai')
                    <i class="bi bi-check-all text-primary" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Selesai</h5>
                    <p class="text-muted">Kembali: {{ $izinKeluar->jam_kembali_aktual }}</p>
                @endif
            </div>
        </div>

        <!-- Status Approval Timeline -->
        @if($izinKeluar->dosen_id)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Status Approval</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Kaprodi:</span>
                    @if($izinKeluar->status_kaprodi === 'pending')
                        <span class="badge bg-warning">Menunggu</span>
                    @elseif($izinKeluar->status_kaprodi === 'disetujui')
                        <span class="badge bg-success">Disetujui</span>
                    @elseif($izinKeluar->status_kaprodi === 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @else
                        <span class="badge bg-secondary">-</span>
                    @endif
                </div>
                @if($izinKeluar->catatan_kaprodi)
                <small class="text-muted">Catatan: {{ $izinKeluar->catatan_kaprodi }}</small>
                @endif
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <span>Admin:</span>
                    @if(in_array($izinKeluar->status, ['diajukan', 'menunggu_admin']))
                        <span class="badge bg-warning">Menunggu</span>
                    @elseif($izinKeluar->status === 'disetujui' || $izinKeluar->status === 'selesai')
                        <span class="badge bg-success">Disetujui</span>
                    @elseif($izinKeluar->status === 'ditolak' && $izinKeluar->status_kaprodi !== 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @else
                        <span class="badge bg-secondary">-</span>
                    @endif
                </div>
                @if($izinKeluar->catatan_approval)
                <small class="text-muted">Catatan: {{ $izinKeluar->catatan_approval }}</small>
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
                    @if(in_array($izinKeluar->status, ['diajukan', 'menunggu_admin']))
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-lg me-1"></i> Setujui
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-1"></i> Tolak
                    </button>
                    @endif
                    @if($izinKeluar->status == 'disetujui')
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSelesai">
                        <i class="bi bi-clock-history me-1"></i> Selesaikan
                    </button>
                    @endif
                    <a href="{{ route('kepegawaian.izin-keluar.index') }}" class="btn btn-outline-secondary">
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
                <p class="mb-2">{{ $izinKeluar->created_at->format('d/m/Y H:i') }}</p>
                @if($izinKeluar->approved_by)
                <small class="text-muted">Diproses oleh:</small>
                <p class="mb-0">{{ $izinKeluar->approvedBy->name ?? '-' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Selesaikan -->
@if($izinKeluar->status == 'disetujui')
<div class="modal fade" id="modalSelesai" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.izin-keluar.selesai', $izinKeluar) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Izin Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jam Kembali Aktual <span class="text-danger">*</span></label>
                        <input type="time" name="jam_kembali_aktual" class="form-control" required value="{{ date('H:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Approve -->
@if(in_array($izinKeluar->status, ['diajukan', 'menunggu_admin']))
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.izin-keluar.approve', $izinKeluar) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Setujui Izin Keluar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Setujui izin keluar untuk <strong>{{ $izinKeluar->nama_pegawai }}</strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3"></textarea>
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
            <form action="{{ route('kepegawaian.izin-keluar.reject', $izinKeluar) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Tolak Izin Keluar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tolak izin keluar untuk <strong>{{ $izinKeluar->nama_pegawai }}</strong>?</p>
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
