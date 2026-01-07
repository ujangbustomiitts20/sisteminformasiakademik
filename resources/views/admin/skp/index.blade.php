@extends('layouts.app')

@section('title', 'SKP Pegawai')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">SKP Pegawai</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">SKP Pegawai</li>
            </ol>
        </nav>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSKP">
        <i class="bi bi-plus-lg me-1"></i>Tambah SKP
    </button>
</div>

<!-- Statistik -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                        <div class="small">Total SKP</div>
                    </div>
                    <i class="bi bi-file-earmark-text fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-secondary text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['draft'] }}</div>
                        <div class="small">Draft</div>
                    </div>
                    <i class="bi bi-pencil-square fs-1 opacity-50"></i>
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
                        <div class="small">Diajukan</div>
                    </div>
                    <i class="bi bi-send fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['final'] }}</div>
                        <div class="small">Final</div>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
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
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Cari nama/no SKP..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\SkpPegawai::STATUS as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="predikat" class="form-select form-select-sm">
                    <option value="">Semua Predikat</option>
                    @foreach(\App\Models\SkpPegawai::PREDIKAT as $key => $label)
                        <option value="{{ $key }}" {{ request('predikat') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('kepegawaian.skp.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Daftar SKP -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar SKP Pegawai</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. SKP</th>
                        <th>Pegawai</th>
                        <th>Periode</th>
                        <th class="text-center">Nilai SKP</th>
                        <th class="text-center">Nilai Perilaku</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Predikat</th>
                        <th class="text-center">Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skpList as $skp)
                    <tr>
                        <td>
                            <strong>{{ $skp->no_skp }}</strong>
                            @if($skp->tanggal_skp)
                            <br><small class="text-muted">{{ $skp->tanggal_skp->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $skp->nama_pegawai }}</strong>
                            @if($skp->dosen)
                            <br><small class="text-muted">{{ $skp->dosen->nidn ?? '-' }}</small>
                            @endif
                        </td>
                        <td>{{ $skp->periode_format }}</td>
                        <td class="text-center">
                            @if($skp->nilai_skp)
                                <span class="fw-semibold">{{ number_format($skp->nilai_skp, 2) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($skp->nilai_perilaku)
                                <span class="fw-semibold">{{ number_format($skp->nilai_perilaku, 2) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($skp->nilai_akhir)
                                <span class="fw-bold text-primary">{{ number_format($skp->nilai_akhir, 2) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($skp->predikat)
                                <span class="badge bg-{{ $skp->predikat_badge }}">{{ $skp->predikat_label }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $skp->status_badge }}">
                                {{ \App\Models\SkpPegawai::STATUS[$skp->status] ?? $skp->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.skp.show', $skp) }}" class="btn btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('kepegawaian.skp.edit', $skp) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($skp->status === 'final')
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="confirmDeleteFinal('{{ route('kepegawaian.skp.destroy', $skp) }}', '{{ $skp->no_skp }}', '{{ $skp->nama_pegawai }}')" title="Hapus SKP Final">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @else
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="confirmDelete('{{ route('kepegawaian.skp.destroy', $skp) }}')" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data SKP
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($skpList->hasPages())
    <div class="card-footer bg-white">
        {{ $skpList->links() }}
    </div>
    @endif
</div>

<!-- Modal Tambah SKP -->
<div class="modal fade" id="modalTambahSKP" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.skp.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Tambah SKP Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Dosen/Pegawai <span class="text-danger">*</span></label>
                        <select name="dosen_id" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosenList as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->nama }} ({{ $dosen->nidn ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                <select name="tahun" class="form-select" required>
                                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Periode</label>
                                <select name="periode" class="form-select">
                                    <option value="">-- Pilih Periode --</option>
                                    <option value="Semester 1">Semester 1 (Januari - Juni)</option>
                                    <option value="Semester 2">Semester 2 (Juli - Desember)</option>
                                    <option value="Tahunan">Tahunan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal SKP</label>
                        <input type="date" name="tanggal_skp" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Confirmation -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus data SKP ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Final SKP Confirmation -->
<div class="modal fade" id="modalDeleteFinal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteFinalForm" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="force" value="true">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Hapus SKP Final</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-3">
                        <i class="bi bi-exclamation-octagon me-2"></i>
                        <strong>PERINGATAN!</strong> Anda akan menghapus SKP yang sudah berstatus <strong>FINAL</strong>.
                    </div>
                    <div class="bg-light p-3 rounded mb-3">
                        <p class="mb-2"><strong>Data yang akan dihapus:</strong></p>
                        <ul class="mb-0 small">
                            <li>No. SKP: <strong id="delete_no_skp"></strong></li>
                            <li>Pegawai: <strong id="delete_nama_pegawai"></strong></li>
                        </ul>
                    </div>
                    <p class="text-danger mb-0 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Tindakan ini tidak dapat dibatalkan. Data SKP beserta semua target dan realisasi akan dihapus permanen.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Ya, Hapus SKP Final
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(url) {
    document.getElementById('deleteForm').action = url;
    new bootstrap.Modal(document.getElementById('modalDelete')).show();
}

function confirmDeleteFinal(url, noSkp, namaPegawai) {
    document.getElementById('deleteFinalForm').action = url;
    document.getElementById('delete_no_skp').textContent = noSkp;
    document.getElementById('delete_nama_pegawai').textContent = namaPegawai;
    new bootstrap.Modal(document.getElementById('modalDeleteFinal')).show();
}
</script>
@endpush
