@extends('layouts.app')

@section('title', 'Rekap Presensi Bulanan')

@section('content')
<div class="page-title">
    <h4>Rekap Presensi Bulanan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.presensi.index') }}">Presensi</a></li>
            <li class="breadcrumb-item active">Rekap Bulanan</li>
        </ol>
    </nav>
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

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-bar-graph me-2"></i>Rekap Presensi {{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}</span>
        <div>
            <form action="{{ route('kepegawaian.presensi.rekap.generate') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-repeat me-1"></i>Generate Rekap
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.presensi.rekap') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIP atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="bulan" class="form-select">
                        @foreach($namaBulan as $key => $val)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tipe" class="form-select">
                        <option value="">-- Tipe --</option>
                        <option value="dosen" {{ request('tipe') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="pegawai" {{ request('tipe') == 'pegawai' ? 'selected' : '' }}>Tendik</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('kepegawaian.presensi.rekap') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Pegawai</th>
                        <th width="80">Tipe</th>
                        <th width="60" class="text-center bg-success text-white">Hadir</th>
                        <th width="60" class="text-center bg-warning">Terlambat</th>
                        <th width="60" class="text-center bg-info text-white">Izin</th>
                        <th width="60" class="text-center bg-secondary text-white">Sakit</th>
                        <th width="60" class="text-center bg-primary text-white">Cuti</th>
                        <th width="60" class="text-center bg-danger text-white">Alpha</th>
                        <th width="60" class="text-center">%</th>
                        <th width="80" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapPresensi as $index => $rekap)
                    @php
                        $totalHariKerja = $rekap->total_hari_kerja ?? 22;
                        $totalHadir = ($rekap->hadir ?? 0) + ($rekap->terlambat ?? 0);
                        $persentase = $totalHariKerja > 0 ? round(($totalHadir / $totalHariKerja) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $rekapPresensi->firstItem() + $index }}</td>
                        <td>
                            @if($rekap->dosen)
                            <strong>{{ $rekap->dosen->nama_lengkap }}</strong>
                            <br><small class="text-muted">{{ $rekap->dosen->nidn ?? $rekap->dosen->nip }}</small>
                            @elseif($rekap->pegawai)
                            <strong>{{ $rekap->pegawai->nama }}</strong>
                            <br><small class="text-muted">{{ $rekap->pegawai->nip }}</small>
                            @endif
                        </td>
                        <td>
                            @if($rekap->dosen_id)
                            <span class="badge bg-info">Dosen</span>
                            @else
                            <span class="badge bg-secondary">Tendik</span>
                            @endif
                        </td>
                        <td class="text-center"><strong class="text-success">{{ $rekap->hadir ?? 0 }}</strong></td>
                        <td class="text-center"><strong class="text-warning">{{ $rekap->terlambat ?? 0 }}</strong></td>
                        <td class="text-center"><strong class="text-info">{{ $rekap->izin ?? 0 }}</strong></td>
                        <td class="text-center"><strong>{{ $rekap->sakit ?? 0 }}</strong></td>
                        <td class="text-center"><strong class="text-primary">{{ $rekap->cuti ?? 0 }}</strong></td>
                        <td class="text-center"><strong class="text-danger">{{ $rekap->alpha ?? 0 }}</strong></td>
                        <td class="text-center">
                            <span class="badge {{ $persentase >= 90 ? 'bg-success' : ($persentase >= 75 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $persentase }}%
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kepegawaian.presensi.rekap.show', $rekap) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-4">
                            <i class="bi bi-file-earmark-bar-graph text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data rekap presensi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($rekapPresensi->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $rekapPresensi->firstItem() }} - {{ $rekapPresensi->lastItem() }} dari {{ $rekapPresensi->total() }} data
            </div>
            {{ $rekapPresensi->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
