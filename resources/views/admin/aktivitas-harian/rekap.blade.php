@extends('layouts.app')

@section('title', 'Rekap Aktivitas Per Pegawai')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Rekap Aktivitas Per Pegawai</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kepegawaian.aktivitas-harian.index') }}">Aktivitas Harian</a></li>
                <li class="breadcrumb-item active">Rekap</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('kepegawaian.aktivitas-harian.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<!-- Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="bulan" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-bold">{{ $stats['total_pegawai'] }}</div>
                <div class="small">Pegawai Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-info text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-bold">{{ $stats['total_aktivitas'] }}</div>
                <div class="small">Total Aktivitas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-bold">{{ $stats['total_disetujui'] }}</div>
                <div class="small">Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-warning text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-bold">{{ $stats['menunggu_approval'] }}</div>
                <div class="small">Menunggu Approval</div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Rekap -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0">
            <i class="bi bi-list-ul me-2"></i>
            Rekap Bulan {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Pegawai</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Disetujui</th>
                        <th class="text-center">Menunggu</th>
                        <th class="text-center">Progress</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapDosen as $dosen)
                    <tr>
                        <td>
                            <strong>{{ $dosen->nama }}</strong>
                            <br><small class="text-muted">{{ $dosen->nidn ?? $dosen->nip ?? '-' }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $dosen->total_aktivitas }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $dosen->aktivitas_disetujui }}</span>
                        </td>
                        <td class="text-center">
                            @if($dosen->aktivitas_diajukan > 0)
                                <span class="badge bg-warning">{{ $dosen->aktivitas_diajukan }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $progress = $dosen->total_aktivitas > 0 
                                    ? round(($dosen->aktivitas_disetujui / $dosen->total_aktivitas) * 100) 
                                    : 0;
                            @endphp
                            <div class="progress" style="height: 20px; min-width: 100px;">
                                <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                                    {{ $progress }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('kepegawaian.aktivitas-harian.rekap-detail', ['dosen' => $dosen, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data aktivitas pada bulan ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($rekapDosen->hasPages())
    <div class="card-footer bg-white">
        {{ $rekapDosen->links() }}
    </div>
    @endif
</div>
@endsection
