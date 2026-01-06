@extends('layouts.app')

@section('title', 'SKP Saya')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">SKP (Sasaran Kinerja Pegawai)</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">SKP</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('dosen.skp.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Buat SKP Baru
    </a>
</div>

<!-- Info Alur -->
<div class="alert alert-info mb-4">
    <h6 class="alert-heading mb-2"><i class="bi bi-info-circle me-2"></i>Alur SKP</h6>
    <div class="d-flex flex-wrap gap-2 align-items-center small">
        <span class="badge bg-secondary">1. Draft</span>
        <i class="bi bi-arrow-right"></i>
        <span class="badge bg-warning text-dark">2. Diajukan</span>
        <i class="bi bi-arrow-right"></i>
        <span class="badge bg-primary">3. Disetujui</span>
        <i class="bi bi-arrow-right"></i>
        <span class="badge bg-info">4. Input Realisasi</span>
        <i class="bi bi-arrow-right"></i>
        <span class="badge bg-success">5. Dinilai/Final</span>
    </div>
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
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\SkpPegawai::STATUS as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-sm me-2">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('dosen.skp.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Daftar SKP -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar SKP Saya</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. SKP</th>
                        <th>Tahun</th>
                        <th>Periode</th>
                        <th class="text-center">Target</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Predikat</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skpList as $skp)
                    <tr>
                        <td>
                            <a href="{{ route('dosen.skp.show', $skp) }}" class="text-decoration-none fw-semibold">
                                {{ $skp->no_skp }}
                            </a>
                        </td>
                        <td>{{ $skp->tahun }}</td>
                        <td>{{ $skp->periode ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark">{{ $skp->targetSkp->count() }} target</span>
                        </td>
                        <td class="text-center">
                            @if($skp->nilai_akhir)
                                <span class="fw-bold">{{ number_format($skp->nilai_akhir, 2) }}</span>
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
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('dosen.skp.show', $skp) }}" class="btn btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($skp->status, ['draft', 'revisi']))
                                <a href="{{ route('dosen.skp.edit', $skp) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if($skp->status === 'final')
                                <a href="{{ route('dosen.skp.cetak', $skp) }}" class="btn btn-outline-secondary" title="Cetak" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data SKP. <a href="{{ route('dosen.skp.create') }}">Buat SKP baru</a>
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
@endsection
