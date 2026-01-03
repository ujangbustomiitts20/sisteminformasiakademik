@extends('layouts.app')

@section('title', 'Presensi Pegawai')

@section('content')
<div class="page-title">
    <h4>Presensi Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Presensi</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Hadir Hari Ini</h6>
                        <h3 class="mb-0">{{ $stats['hadir'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Terlambat</h6>
                        <h3 class="mb-0">{{ $stats['terlambat'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-clock fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Izin/Sakit</h6>
                        <h3 class="mb-0">{{ $stats['izin'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-file-medical fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Alpha</h6>
                        <h3 class="mb-0">{{ $stats['alpha'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-x-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-check me-2"></i>Data Presensi Pegawai</span>
        <div>
            <a href="{{ route('kepegawaian.presensi.setting') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-gear me-1"></i>Setting Jam Kerja
            </a>
            <a href="{{ route('kepegawaian.presensi.rekap') }}" class="btn btn-outline-info btn-sm me-2">
                <i class="bi bi-file-earmark-bar-graph me-1"></i>Rekap Bulanan
            </a>
            <a href="{{ route('kepegawaian.presensi.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Input Presensi
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.presensi.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIP atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Status --</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="dinas_luar" {{ request('status') == 'dinas_luar' ? 'selected' : '' }}>Dinas Luar</option>
                        <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
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
                    <a href="{{ route('kepegawaian.presensi.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Pegawai</th>
                        <th width="100">Tanggal</th>
                        <th width="80" class="text-center">Masuk</th>
                        <th width="80" class="text-center">Keluar</th>
                        <th width="80" class="text-center">Jam Kerja</th>
                        <th width="90">Status</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensi as $index => $p)
                    <tr>
                        <td>{{ $presensi->firstItem() + $index }}</td>
                        <td>
                            @if($p->dosen)
                            <strong>{{ $p->dosen->nama_lengkap }}</strong>
                            <br><small class="text-muted">{{ $p->dosen->nidn ?? $p->dosen->nip }} (Dosen)</small>
                            @elseif($p->pegawai)
                            <strong>{{ $p->pegawai->nama }}</strong>
                            <br><small class="text-muted">{{ $p->pegawai->nip }} (Tendik)</small>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($p->jam_masuk)
                            <span class="{{ $p->isTerlambat() ? 'text-danger' : 'text-success' }}">
                                {{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p->jam_keluar)
                            {{ \Carbon\Carbon::parse($p->jam_keluar)->format('H:i') }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p->jam_masuk && $p->jam_keluar)
                            <span class="badge bg-light text-dark">{{ $p->hitungJamKerja() }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{!! $p->status_badge !!}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.presensi.show', $p) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('kepegawaian.presensi.edit', $p) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kepegawaian.presensi.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data presensi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-calendar-check text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data presensi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($presensi->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $presensi->firstItem() }} - {{ $presensi->lastItem() }} dari {{ $presensi->total() }} data
            </div>
            {{ $presensi->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
