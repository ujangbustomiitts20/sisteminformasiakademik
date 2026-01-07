@extends('layouts.app')

@section('title', 'Aktivitas Harian Pegawai')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Aktivitas Harian Pegawai</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Aktivitas Harian</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('kepegawaian.aktivitas-harian.rekap') }}" class="btn btn-outline-primary">
        <i class="bi bi-bar-chart me-1"></i>Rekap Per Pegawai
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                        <div class="small">Total Aktivitas</div>
                    </div>
                    <i class="bi bi-list-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-warning text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['diajukan'] }}</div>
                        <div class="small">Menunggu Approval</div>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['disetujui'] }}</div>
                        <div class="small">Disetujui</div>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-danger text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['ditolak'] }}</div>
                        <div class="small">Ditolak</div>
                    </div>
                    <i class="bi bi-x-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="dosen_id" class="form-select form-select-sm">
                    <option value="">Semua Pegawai</option>
                    @foreach($dosenList as $dosen)
                        <option value="{{ $dosen->id }}" {{ request('dosen_id') == $dosen->id ? 'selected' : '' }}>
                            {{ $dosen->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="bulan" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (request('bulan', now()->month) == $m) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ (request('tahun', now()->year) == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\AktivitasHarian::STATUS as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Cari..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('kepegawaian.aktivitas-harian.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Action for Diajukan -->
@if($stats['diajukan'] > 0)
<form action="{{ route('kepegawaian.aktivitas-harian.bulk-approve') }}" method="POST" id="formBulkApprove">
    @csrf
@endif

<!-- Daftar Aktivitas -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Aktivitas Harian</h6>
        @if($stats['diajukan'] > 0)
        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui semua aktivitas yang dipilih?')">
            <i class="bi bi-check-all me-1"></i>Setujui Terpilih
        </button>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        @if($stats['diajukan'] > 0)
                        <th width="40">
                            <input type="checkbox" class="form-check-input" id="checkAll">
                        </th>
                        @endif
                        <th width="110">Tanggal</th>
                        <th>Pegawai</th>
                        <th>Uraian Kegiatan</th>
                        <th class="text-center" width="90">Volume</th>
                        <th class="text-center" width="100">Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aktivitas as $item)
                    <tr>
                        @if($stats['diajukan'] > 0)
                        <td>
                            @if($item->status === 'diajukan')
                            <input type="checkbox" name="aktivitas_ids[]" value="{{ $item->id }}" class="form-check-input check-item">
                            @endif
                        </td>
                        @endif
                        <td>
                            <strong>{{ $item->tanggal->format('d/m/Y') }}</strong>
                            <br><small class="text-muted">{{ $item->tanggal->translatedFormat('l') }}</small>
                        </td>
                        <td>
                            <strong>{{ $item->nama_pegawai }}</strong>
                            <br><small class="text-muted">{{ $item->nidn_nip }}</small>
                        </td>
                        <td>
                            <div>{{ Str::limit($item->uraian_kegiatan, 50) }}</div>
                            @if($item->output_hasil)
                                <small class="text-muted">→ {{ Str::limit($item->output_hasil, 30) }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->volume)
                                {{ number_format($item->volume, 0) }} {{ $item->satuan }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $item->status_badge }}">
                                {{ \App\Models\AktivitasHarian::STATUS[$item->status] }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.aktivitas-harian.show', $item) }}" class="btn btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($item->status === 'diajukan')
                                <form action="{{ route('kepegawaian.aktivitas-harian.approve', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Setujui" onclick="return confirm('Setujui aktivitas ini?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger" title="Tolak" 
                                        onclick="showRejectModal('{{ route('kepegawaian.aktivitas-harian.reject', $item) }}')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $stats['diajukan'] > 0 ? 7 : 6 }}" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data aktivitas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($aktivitas->hasPages())
    <div class="card-footer bg-white">
        {{ $aktivitas->links() }}
    </div>
    @endif
</div>

@if($stats['diajukan'] > 0)
</form>
@endif

<!-- Modal Reject -->
<div class="modal fade" id="modalReject" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formReject" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Tolak Aktivitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan/Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_atasan" class="form-control" rows="3" required 
                                  placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg me-1"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Check all
document.getElementById('checkAll')?.addEventListener('change', function() {
    document.querySelectorAll('.check-item').forEach(cb => cb.checked = this.checked);
});

function showRejectModal(url) {
    document.getElementById('formReject').action = url;
    new bootstrap.Modal(document.getElementById('modalReject')).show();
}
</script>
@endpush
