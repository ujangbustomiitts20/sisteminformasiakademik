@extends('layouts.app')

@section('title', 'Kenaikan Gaji Berkala')

@section('content')
<div class="page-title">
    <h4>Kenaikan Gaji Berkala (KGB)</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">KGB</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Akan Jatuh Tempo</h6>
                        <h3 class="mb-0">{{ $stats['akan_jatuh_tempo'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-bell fs-1 opacity-50"></i>
                </div>
                <small class="opacity-75">3 bulan ke depan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Diajukan</h6>
                        <h3 class="mb-0">{{ $stats['diajukan'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Diproses</h6>
                        <h3 class="mb-0">{{ $stats['diproses'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Total KGB</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-stack me-2"></i>Daftar Kenaikan Gaji Berkala</span>
        <a href="{{ route('kepegawaian.kgb.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.kgb.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIP atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Status --</option>
                        <option value="belum_diajukan" {{ request('status') == 'belum_diajukan' ? 'selected' : '' }}>Belum Diajukan</option>
                        <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-select">
                        <option value="">-- Tahun --</option>
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
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
                    <a href="{{ route('kepegawaian.kgb.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Pegawai</th>
                        <th width="90">Gol/Ruang</th>
                        <th width="100">TMT KGB</th>
                        <th>Gaji Lama → Baru</th>
                        <th width="100">TMT Berikutnya</th>
                        <th width="90">Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kgb as $index => $k)
                    <tr>
                        <td>{{ $kgb->firstItem() + $index }}</td>
                        <td>
                            @if($k->dosen)
                            <strong>{{ $k->dosen->nama_lengkap }}</strong>
                            <br><small class="text-muted">{{ $k->dosen->nidn ?? $k->dosen->nip }} (Dosen)</small>
                            @elseif($k->pegawai)
                            <strong>{{ $k->pegawai->nama }}</strong>
                            <br><small class="text-muted">{{ $k->pegawai->nip }} (Tendik)</small>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ $k->golongan_ruang }}</span></td>
                        <td>{{ $k->tmt_kgb ? \Carbon\Carbon::parse($k->tmt_kgb)->format('d/m/Y') : '-' }}</td>
                        <td>
                            <small>
                                Rp {{ number_format($k->gaji_pokok_lama ?? 0, 0, ',', '.') }}
                                <i class="bi bi-arrow-right mx-1 text-success"></i>
                                <strong class="text-success">Rp {{ number_format($k->gaji_pokok_baru ?? 0, 0, ',', '.') }}</strong>
                            </small>
                        </td>
                        <td>
                            @if($k->tmt_kgb_berikutnya)
                            @php
                                $tglBerikutnya = \Carbon\Carbon::parse($k->tmt_kgb_berikutnya);
                                $selisih = now()->diffInDays($tglBerikutnya, false);
                            @endphp
                            <span class="{{ $selisih <= 90 && $selisih > 0 ? 'text-warning fw-bold' : '' }}">
                                {{ $tglBerikutnya->format('d/m/Y') }}
                            </span>
                            @if($selisih <= 90 && $selisih > 0)
                            <br><small class="text-warning">{{ $selisih }} hari lagi</small>
                            @elseif($selisih <= 0)
                            <br><small class="text-danger">Sudah jatuh tempo!</small>
                            @endif
                            @else
                            -
                            @endif
                        </td>
                        <td>{!! $k->status_badge !!}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.kgb.show', $k) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($k->status, ['belum_diajukan', 'diajukan']))
                                <a href="{{ route('kepegawaian.kgb.edit', $k) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if($k->status == 'diajukan')
                                <form action="{{ route('kepegawaian.kgb.proses', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Proses KGB ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Proses">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-cash-stack text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data KGB</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kgb->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $kgb->firstItem() }} - {{ $kgb->lastItem() }} dari {{ $kgb->total() }} data
            </div>
            {{ $kgb->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
