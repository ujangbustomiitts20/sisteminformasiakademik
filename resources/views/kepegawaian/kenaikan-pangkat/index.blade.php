@extends('layouts.app')

@section('title', 'Kenaikan Pangkat')

@section('content')
<div class="page-title">
    <h4>Kenaikan Pangkat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Kenaikan Pangkat</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Diusulkan</h6>
                        <h3 class="mb-0">{{ $stats['diusulkan'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-send fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Diverifikasi</h6>
                        <h3 class="mb-0">{{ $stats['diverifikasi'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-clipboard-check fs-1 opacity-50"></i>
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
                        <h6 class="text-white-50">Total</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-graph-up-arrow fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-graph-up-arrow me-2"></i>Daftar Kenaikan Pangkat</span>
        <a href="{{ route('kepegawaian.kenaikan-pangkat.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.kenaikan-pangkat.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIP atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="jenis" class="form-select">
                        <option value="">-- Jenis --</option>
                        <option value="reguler" {{ request('jenis') == 'reguler' ? 'selected' : '' }}>Reguler</option>
                        <option value="pilihan" {{ request('jenis') == 'pilihan' ? 'selected' : '' }}>Pilihan</option>
                        <option value="struktural" {{ request('jenis') == 'struktural' ? 'selected' : '' }}>Struktural</option>
                        <option value="fungsional" {{ request('jenis') == 'fungsional' ? 'selected' : '' }}>Fungsional</option>
                        <option value="pengabdian" {{ request('jenis') == 'pengabdian' ? 'selected' : '' }}>Pengabdian</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="periode" class="form-select">
                        <option value="">-- Periode --</option>
                        <option value="april" {{ request('periode') == 'april' ? 'selected' : '' }}>April</option>
                        <option value="oktober" {{ request('periode') == 'oktober' ? 'selected' : '' }}>Oktober</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Status --</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diusulkan" {{ request('status') == 'diusulkan' ? 'selected' : '' }}>Diusulkan</option>
                        <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('kepegawaian.kenaikan-pangkat.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Pegawai</th>
                        <th>Pangkat/Gol</th>
                        <th width="100">Periode</th>
                        <th width="100">TMT</th>
                        <th width="80">Jenis</th>
                        <th width="90">Status</th>
                        <th width="140" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kenaikanPangkat as $index => $k)
                    <tr>
                        <td>{{ $kenaikanPangkat->firstItem() + $index }}</td>
                        <td>
                            @if($k->dosen)
                            <strong>{{ $k->dosen->nama_lengkap }}</strong>
                            <br><small class="text-muted">{{ $k->dosen->nidn ?? $k->dosen->nip }} (Dosen)</small>
                            @elseif($k->pegawai)
                            <strong>{{ $k->pegawai->nama }}</strong>
                            <br><small class="text-muted">{{ $k->pegawai->nip }} (Tendik)</small>
                            @endif
                        </td>
                        <td>
                            <small>
                                {{ $k->pangkat_lama }} ({{ $k->golongan_lama }})
                                <i class="bi bi-arrow-right mx-1 text-success"></i>
                                <strong class="text-success">{{ $k->pangkat_baru }} ({{ $k->golongan_baru }})</strong>
                            </small>
                        </td>
                        <td><span class="badge bg-primary">{{ ucfirst($k->periode) }} {{ $k->tahun }}</span></td>
                        <td>{{ $k->tmt ? \Carbon\Carbon::parse($k->tmt)->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge bg-secondary">{{ $k->jenis_label }}</span></td>
                        <td>{!! $k->status_badge !!}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.kenaikan-pangkat.show', $k) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($k->status, ['draft']))
                                <a href="{{ route('kepegawaian.kenaikan-pangkat.edit', $k) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kepegawaian.kenaikan-pangkat.usulkan', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Usulkan kenaikan pangkat ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary" title="Usulkan">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </form>
                                @endif
                                @if($k->status == 'diusulkan')
                                <form action="{{ route('kepegawaian.kenaikan-pangkat.verifikasi', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Verifikasi kenaikan pangkat ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-info" title="Verifikasi">
                                        <i class="bi bi-clipboard-check"></i>
                                    </button>
                                </form>
                                @endif
                                @if($k->status == 'diverifikasi')
                                <form action="{{ route('kepegawaian.kenaikan-pangkat.setujui', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui kenaikan pangkat ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Setujui">
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
                            <i class="bi bi-graph-up-arrow text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data kenaikan pangkat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kenaikanPangkat->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $kenaikanPangkat->firstItem() }} - {{ $kenaikanPangkat->lastItem() }} dari {{ $kenaikanPangkat->total() }} data
            </div>
            {{ $kenaikanPangkat->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
