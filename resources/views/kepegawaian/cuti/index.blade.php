@extends('layouts.app')

@section('title', 'Manajemen Cuti Pegawai')

@section('content')
<div class="page-title">
    <h4>Manajemen Cuti Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Cuti Pegawai</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Pengajuan Baru</h6>
                        <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Disetujui Atasan</h6>
                        <h3 class="mb-0">{{ $stats['disetujui_atasan'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Disetujui</h6>
                        <h3 class="mb-0">{{ $stats['disetujui'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-check-all fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Ditolak</h6>
                        <h3 class="mb-0">{{ $stats['ditolak'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-x-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-minus me-2"></i>Daftar Pengajuan Cuti</span>
        <div>
            <a href="{{ route('kepegawaian.cuti.saldo') }}" class="btn btn-outline-info btn-sm me-2">
                <i class="bi bi-wallet2 me-1"></i>Saldo Cuti
            </a>
            <a href="{{ route('kepegawaian.cuti.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Ajukan Cuti
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.cuti.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIP atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="jenis_cuti" class="form-select">
                        <option value="">-- Jenis Cuti --</option>
                        <option value="tahunan" {{ request('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                        <option value="sakit" {{ request('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Cuti Sakit</option>
                        <option value="melahirkan" {{ request('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
                        <option value="besar" {{ request('jenis_cuti') == 'besar' ? 'selected' : '' }}>Cuti Besar</option>
                        <option value="alasan_penting" {{ request('jenis_cuti') == 'alasan_penting' ? 'selected' : '' }}>Cuti Alasan Penting</option>
                        <option value="diluar_tanggungan" {{ request('jenis_cuti') == 'diluar_tanggungan' ? 'selected' : '' }}>Cuti Di Luar Tanggungan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Status --</option>
                        <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="disetujui_atasan" {{ request('status') == 'disetujui_atasan' ? 'selected' : '' }}>Disetujui Atasan</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="month" name="bulan" class="form-control" value="{{ request('bulan') }}" placeholder="Bulan">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('kepegawaian.cuti.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th width="120">No. Pengajuan</th>
                        <th>Pegawai</th>
                        <th>Jenis Cuti</th>
                        <th>Tanggal</th>
                        <th width="80">Lama</th>
                        <th width="100">Status</th>
                        <th width="140" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cutiPegawai as $index => $cuti)
                    <tr>
                        <td>{{ $cutiPegawai->firstItem() + $index }}</td>
                        <td><code>{{ $cuti->no_pengajuan }}</code></td>
                        <td>
                            @if($cuti->dosen)
                            <strong>{{ $cuti->dosen->nama_lengkap }}</strong>
                            <br><small class="text-muted">{{ $cuti->dosen->nidn ?? $cuti->dosen->nip }} (Dosen)</small>
                            @elseif($cuti->pegawai)
                            <strong>{{ $cuti->pegawai->nama }}</strong>
                            <br><small class="text-muted">{{ $cuti->pegawai->nip }} (Tendik)</small>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ $cuti->jenis_cuti_label }}</span></td>
                        <td>
                            {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} - 
                            {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark">{{ $cuti->jumlah_hari }} hari</span>
                        </td>
                        <td>{!! $cuti->status_badge !!}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.cuti.show', $cuti) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($cuti->status == 'diajukan')
                                <a href="{{ route('kepegawaian.cuti.edit', $cuti) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-success" title="Approve" onclick="showApproveModal('{{ $cuti->hashid }}')">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Tolak" onclick="showRejectModal('{{ $cuti->hashid }}')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @elseif($cuti->status == 'disetujui_atasan')
                                <button type="button" class="btn btn-outline-success" title="Approve Final" onclick="showApproveModal('{{ $cuti->hashid }}')">
                                    <i class="bi bi-check-all"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Tolak" onclick="showRejectModal('{{ $cuti->hashid }}')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-calendar-minus text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada pengajuan cuti</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($cutiPegawai->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $cutiPegawai->firstItem() }} - {{ $cutiPegawai->lastItem() }} dari {{ $cutiPegawai->total() }} data
            </div>
            {{ $cutiPegawai->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Approve -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Setujui Cuti</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan Persetujuan</label>
                        <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Tolak Cuti</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
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
function showApproveModal(id) {
    document.getElementById('approveForm').action = '{{ url("kepegawaian/cuti") }}/' + id + '/approve';
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function showRejectModal(id) {
    document.getElementById('rejectForm').action = '{{ url("kepegawaian/cuti") }}/' + id + '/reject';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
