@extends('layouts.app')

@section('title', 'Manajemen Refund')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Refund</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Refund</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('keuangan.refund.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Ajukan Refund
            </a>
            <a href="{{ route('keuangan.refund.export', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-download me-1"></i>Export
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50 small">Total</h6>
                            <h4 class="mb-0">{{ number_format($stats['total']) }}</h4>
                        </div>
                        <i class="bi bi-arrow-return-left fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-muted small">Pending</h6>
                            <h4 class="mb-0">{{ number_format($stats['pending']) }}</h4>
                        </div>
                        <i class="bi bi-hourglass-split fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50 small">Diproses</h6>
                            <h4 class="mb-0">{{ number_format($stats['diproses']) }}</h4>
                        </div>
                        <i class="bi bi-gear fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50 small">Disetujui</h6>
                            <h4 class="mb-0">{{ number_format($stats['disetujui']) }}</h4>
                        </div>
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50 small">Selesai</h6>
                            <h4 class="mb-0">{{ number_format($stats['selesai']) }}</h4>
                        </div>
                        <i class="bi bi-check2-all fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50 small">Ditolak</h6>
                            <h4 class="mb-0">{{ number_format($stats['ditolak']) }}</h4>
                        </div>
                        <i class="bi bi-x-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Total Nominal Refund (Disetujui + Selesai)</h5>
                        </div>
                        <h3 class="mb-0 text-success">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIM/Nama/No Refund" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\Refund::STATUS_LIST as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach(\App\Models\Refund::JENIS_LIST as $key => $label)
                            <option value="{{ $key }}" {{ request('jenis') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal_mulai" class="form-control" placeholder="Dari Tanggal" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal_selesai" class="form-control" placeholder="Sampai Tanggal" value="{{ request('tanggal_selesai') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Refund</th>
                            <th>Mahasiswa</th>
                            <th>Jenis</th>
                            <th class="text-end">Jumlah Pengajuan</th>
                            <th class="text-end">Jumlah Disetujui</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                        <tr>
                            <td>
                                <a href="{{ route('keuangan.refund.show', $refund) }}" class="fw-semibold text-decoration-none">
                                    {{ $refund->nomor_refund }}
                                </a>
                            </td>
                            <td>
                                @if($refund->mahasiswa)
                                    <div>{{ $refund->mahasiswa->nama }}</div>
                                    <small class="text-muted">{{ $refund->mahasiswa->nim }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $refund->jenis_label }}</td>
                            <td class="text-end">Rp {{ number_format($refund->jumlah_pengajuan, 0, ',', '.') }}</td>
                            <td class="text-end">
                                @if($refund->jumlah_disetujui)
                                    Rp {{ number_format($refund->jumlah_disetujui, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $refund->metode_label }}</td>
                            <td>{!! $refund->status_badge !!}</td>
                            <td>{{ $refund->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('keuangan.refund.show', $refund) }}" class="btn btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($refund->status === \App\Models\Refund::STATUS_PENDING)
                                        <a href="{{ route('keuangan.refund.edit', $refund) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-info btn-process" data-id="{{ $refund->id }}" title="Proses">
                                            <i class="bi bi-gear"></i>
                                        </button>
                                    @endif
                                    @if($refund->status === \App\Models\Refund::STATUS_DIPROSES)
                                        <button type="button" class="btn btn-outline-success btn-approve" data-id="{{ $refund->id }}" data-jumlah="{{ $refund->jumlah_pengajuan }}" title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-reject" data-id="{{ $refund->id }}" title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                    @if($refund->status === \App\Models\Refund::STATUS_DISETUJUI)
                                        <button type="button" class="btn btn-outline-success btn-complete" data-id="{{ $refund->id }}" title="Selesaikan">
                                            <i class="bi bi-check2-all"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Tidak ada data refund
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($refunds->hasPages())
        <div class="card-footer bg-white">
            {{ $refunds->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Process -->
<div class="modal fade" id="processModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="processForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin memproses pengajuan refund ini?</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Approve -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setujui Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Disetujui <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_disetujui" id="jumlahDisetujui" class="form-control" required min="0" step="0.01">
                        <small class="text-muted">Maksimal: <span id="maxJumlah">0</span></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_penolakan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Complete -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selesaikan Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="completeForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bukti Transfer/Refund</label>
                        <input type="file" name="bukti_refund" class="form-control" accept="image/*,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF. Maks: 2MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Referensi Transfer</label>
                        <input type="text" name="nomor_referensi_refund" class="form-control">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Process modal
    document.querySelectorAll('.btn-process').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('processForm').action = `/refund/${id}/process`;
            new bootstrap.Modal(document.getElementById('processModal')).show();
        });
    });

    // Approve modal
    document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const jumlah = this.dataset.jumlah;
            document.getElementById('approveForm').action = `/refund/${id}/approve`;
            document.getElementById('jumlahDisetujui').value = jumlah;
            document.getElementById('jumlahDisetujui').max = jumlah;
            document.getElementById('maxJumlah').textContent = new Intl.NumberFormat('id-ID').format(jumlah);
            new bootstrap.Modal(document.getElementById('approveModal')).show();
        });
    });

    // Reject modal
    document.querySelectorAll('.btn-reject').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('rejectForm').action = `/refund/${id}/reject`;
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        });
    });

    // Complete modal
    document.querySelectorAll('.btn-complete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('completeForm').action = `/refund/${id}/complete`;
            new bootstrap.Modal(document.getElementById('completeModal')).show();
        });
    });
});
</script>
@endpush
