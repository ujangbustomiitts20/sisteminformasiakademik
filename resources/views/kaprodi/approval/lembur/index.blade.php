@extends('layouts.app')

@section('title', 'Approval Lembur')

@section('content')
<div class="page-title">
    <h4>Approval Lembur</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.approval.dashboard') }}">Approval</a></li>
            <li class="breadcrumb-item active">Lembur</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h6>Menunggu Approval</h6>
                <h3>{{ $stats['pending'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h6>Disetujui</h6>
                <h3>{{ $stats['disetujui'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h6>Ditolak</h6>
                <h3>{{ $stats['ditolak'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('kaprodi.approval.lembur.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Approval</option>
                    <option value="disetujui_kaprodi" {{ request('status') == 'disetujui_kaprodi' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak_kaprodi" {{ request('status') == 'ditolak_kaprodi' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-4">
                @if(request('status'))
                <a href="{{ route('kaprodi.approval.lembur.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i> Reset
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Pengajuan Lembur Dosen</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Dosen</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Durasi</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lemburList as $i => $lembur)
                        <tr>
                            <td>{{ $lemburList->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $lembur->dosen?->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $lembur->dosen?->nidn ?? '-' }}</small>
                            </td>
                            <td>{{ $lembur->tanggal?->format('d/m/Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($lembur->jam_mulai)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($lembur->jam_selesai)->format('H:i') }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ number_format($lembur->durasi_jam, 1) }} jam</span>
                            </td>
                            <td>
                                <span title="{{ $lembur->alasan }}">{{ Str::limit($lembur->alasan, 30) }}</span>
                            </td>
                            <td>
                                @if($lembur->status_kaprodi === 'pending')
                                    <span class="badge bg-warning">Menunggu Approval</span>
                                @elseif($lembur->status_kaprodi === 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($lembur->status_kaprodi === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-{{ $lembur->status_color }}">{{ $lembur->status_label }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($lembur->status_kaprodi === 'pending')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $lembur->id }}" title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $lembur->id }}" title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('kaprodi.approval.lembur.show', $lembur) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal{{ $lembur->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('kaprodi.approval.lembur.approve', $lembur) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">Setujui Pengajuan Lembur</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Setujui pengajuan lembur untuk <strong>{{ $lembur->dosen?->nama }}</strong>?</p>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <td>: {{ $lembur->tanggal?->format('d/m/Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Durasi</th>
                                                    <td>: {{ number_format($lembur->durasi_jam, 1) }} jam</td>
                                                </tr>
                                                <tr>
                                                    <th>Alasan</th>
                                                    <td>: {{ $lembur->alasan }}</td>
                                                </tr>
                                            </table>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan (opsional)</label>
                                                <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                            </div>
                                            <div class="alert alert-info mb-0">
                                                <small><i class="bi bi-info-circle me-1"></i>Setelah Anda setujui, pengajuan akan diteruskan ke Admin untuk penentuan tarif dan approval final.</small>
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

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $lembur->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('kaprodi.approval.lembur.reject', $lembur) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Tolak Pengajuan Lembur</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Tolak pengajuan lembur untuk <strong>{{ $lembur->dosen?->nama }}</strong>?</p>
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                <textarea name="catatan" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
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
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada data pengajuan lembur
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $lemburList->links() }}
        </div>
    </div>
</div>
@endsection
