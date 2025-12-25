@extends('layouts.app')

@section('title', 'Jadwal Pengganti')

@section('content')
<div class="page-title">
    <h4>Jadwal Pengganti</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Pengganti</li>
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
                        <h6 class="text-white-50">Total</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <i class="bi bi-calendar-event fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-dark-50">Pending</h6>
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
                        <h6 class="text-white-50">Akan Datang</h6>
                        <h3 class="mb-0">{{ $stats['upcoming'] }}</h3>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Daftar Jadwal Pengganti</h5>
        <a href="{{ route('jadwal-pengganti.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="bulan" class="form-select">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="tahun" class="form-select">
                    <option value="">Semua Tahun</option>
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
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

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Tanggal Asli</th>
                        <th>Tanggal Pengganti</th>
                        <th>Waktu</th>
                        <th>Ruangan</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalPengganti as $jp)
                    <tr>
                        <td>{{ $jadwalPengganti->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $jp->jadwalKuliah->mataKuliah->nama ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $jp->jadwalKuliah->mataKuliah->kode ?? '' }}</small>
                        </td>
                        <td>{{ $jp->jadwalKuliah->dosen->nama ?? '-' }}</td>
                        <td>{{ $jp->tanggal_asli->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $jp->tanggal_pengganti->format('d/m/Y') }}</strong>
                            <br><small class="text-muted">{{ $jp->tanggal_pengganti->translatedFormat('l') }}</small>
                        </td>
                        <td>{{ substr($jp->jam_mulai, 0, 5) }} - {{ substr($jp->jam_selesai, 0, 5) }}</td>
                        <td>{{ $jp->ruangan->nama ?? '-' }}</td>
                        <td><span class="badge bg-secondary">{{ $jp->alasan }}</span></td>
                        <td>{!! $jp->status_badge !!}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('jadwal-pengganti.show', $jp) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($jp->status === 'Pending')
                                <a href="{{ route('jadwal-pengganti.edit', $jp) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-success" title="Setujui" 
                                        onclick="approveJadwal('{{ $jp->hashid }}')">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Tolak"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal" 
                                        data-id="{{ $jp->hashid }}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @elseif($jp->status === 'Disetujui')
                                <button type="button" class="btn btn-outline-secondary" title="Selesai"
                                        onclick="completeJadwal('{{ $jp->hashid }}')">
                                    <i class="bi bi-check-all"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Belum ada data jadwal pengganti</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jadwalPengganti->hasPages())
    <div class="card-footer">
        {{ $jadwalPengganti->withQueryString()->links() }}
    </div>
    @endif
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Jadwal Pengganti</h5>
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

<!-- Hidden Forms -->
<form id="approveForm" method="POST" style="display: none;">
    @csrf
</form>
<form id="completeForm" method="POST" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
document.getElementById('rejectModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    document.getElementById('rejectForm').action = `/jadwal-pengganti/${id}/reject`;
});

function approveJadwal(id) {
    if (confirm('Setujui jadwal pengganti ini?')) {
        const form = document.getElementById('approveForm');
        form.action = `/jadwal-pengganti/${id}/approve`;
        form.submit();
    }
}

function completeJadwal(id) {
    if (confirm('Tandai jadwal pengganti ini sebagai selesai?')) {
        const form = document.getElementById('completeForm');
        form.action = `/jadwal-pengganti/${id}/complete`;
        form.submit();
    }
}
</script>
@endpush
@endsection
