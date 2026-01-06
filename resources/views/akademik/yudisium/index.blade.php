@extends('layouts.app')

@section('title', 'Yudisium')

@section('content')
<div class="page-title">
    <h4>Yudisium</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Yudisium</li>
        </ol>
    </nav>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Total Yudisium</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <i class="bi bi-journal-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-dark-50">Menunggu Persetujuan</h6>
                        <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Disetujui</h6>
                        <h3 class="mb-0">{{ $stats['disetujui'] }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Cum Laude</h6>
                        <h3 class="mb-0">{{ $stats['cum_laude'] }}</h3>
                    </div>
                    <i class="bi bi-award fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-journal-check me-2"></i>Daftar Yudisium</h5>
                <a href="{{ route('yudisium.candidates') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Proses Yudisium Baru
                </a>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-2">
                        <select name="tahun_lulus" class="form-select">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunLulusList as $tahun)
                            <option value="{{ $tahun }}" {{ request('tahun_lulus') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="predikat" class="form-select">
                            <option value="">Semua Predikat</option>
                            <option value="Cum Laude" {{ request('predikat') == 'Cum Laude' ? 'selected' : '' }}>Cum Laude</option>
                            <option value="Sangat Memuaskan" {{ request('predikat') == 'Sangat Memuaskan' ? 'selected' : '' }}>Sangat Memuaskan</option>
                            <option value="Memuaskan" {{ request('predikat') == 'Memuaskan' ? 'selected' : '' }}>Memuaskan</option>
                            <option value="Cukup" {{ request('predikat') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Cari NIM/Nama..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                    </div>
                </form>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form id="bulkForm" method="POST" action="{{ route('yudisium.bulk-approve') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="checkAll" class="form-check-input">
                                    </th>
                                    <th>No. Yudisium</th>
                                    <th>Mahasiswa</th>
                                    <th>Program Studi</th>
                                    <th>IPK</th>
                                    <th>SKS</th>
                                    <th>Predikat</th>
                                    <th>Masa Studi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($yudisium as $y)
                                <tr>
                                    <td>
                                        @if($y->status === 'Pending')
                                        <input type="checkbox" name="yudisium_ids[]" value="{{ $y->id }}" class="form-check-input check-item">
                                        @endif
                                    </td>
                                    <td><code>{{ $y->no_yudisium }}</code></td>
                                    <td>
                                        <strong>{{ $y->mahasiswa->nama ?? '-' }}</strong>
                                        <br><small class="text-muted">{{ $y->mahasiswa->nim ?? '' }}</small>
                                    </td>
                                    <td>{{ $y->mahasiswa->programStudi->nama ?? '-' }}</td>
                                    <td><strong>{{ number_format($y->ipk_akhir, 2) }}</strong></td>
                                    <td>{{ $y->total_sks_lulus }}</td>
                                    <td>{!! $y->predikat_badge !!}</td>
                                    <td>{{ $y->masa_studi_format }}</td>
                                    <td>{!! $y->status_badge !!}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('yudisium.show', $y) }}" class="btn btn-outline-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($y->status === 'Pending')
                                            <button type="button" class="btn btn-outline-success" title="Setujui"
                                                    onclick="approveYudisium('{{ $y->hashid }}')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Tolak"
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal" 
                                                    data-id="{{ $y->hashid }}">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                            @elseif($y->status === 'Disetujui')
                                            <a href="{{ route('yudisium.print', $y) }}" class="btn btn-outline-primary" title="Cetak">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mb-0 mt-2">Belum ada data yudisium</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($yudisium->where('status', 'Pending')->count() > 0)
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success" id="bulkApproveBtn" disabled>
                            <i class="bi bi-check-all me-1"></i>Setujui Terpilih (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                    @endif
                </form>
            </div>
            @if($yudisium->hasPages())
            <div class="card-footer">
                {{ $yudisium->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Yudisium</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_tolak" class="form-control" rows="3" required 
                                  placeholder="Masukkan alasan penolakan..."></textarea>
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

<!-- Hidden Form -->
<form id="approveForm" method="POST" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
document.getElementById('rejectModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    document.getElementById('rejectForm').action = `/yudisium/${id}/reject`;
});

function approveYudisium(id) {
    if (confirm('Setujui yudisium ini? Status mahasiswa akan diubah menjadi LULUS.')) {
        const form = document.getElementById('approveForm');
        form.action = `/yudisium/${id}/approve`;
        form.submit();
    }
}

// Bulk select
document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.check-item').forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

document.querySelectorAll('.check-item').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const count = document.querySelectorAll('.check-item:checked').length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkApproveBtn').disabled = count === 0;
}
</script>
@endpush
@endsection
