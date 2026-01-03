@extends('layouts.app')

@section('title', 'Penugasan & Mutasi')

@section('content')
<div class="page-title">
    <h4>Penugasan & Mutasi Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Penugasan & Mutasi</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Total Data</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-arrow-left-right fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Penugasan</h6>
                        <h3 class="mb-0">{{ $stats['penugasan'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-briefcase fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Mutasi</h6>
                        <h3 class="mb-0">{{ $stats['mutasi'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-shuffle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Promosi</h6>
                        <h3 class="mb-0">{{ $stats['promosi'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-graph-up-arrow fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-arrow-left-right me-2"></i>Daftar Penugasan & Mutasi</span>
        <a href="{{ route('kepegawaian.penugasan.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.penugasan.index') }}" class="mb-4">
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
                        <option value="penugasan" {{ request('jenis') == 'penugasan' ? 'selected' : '' }}>Penugasan</option>
                        <option value="mutasi" {{ request('jenis') == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                        <option value="promosi" {{ request('jenis') == 'promosi' ? 'selected' : '' }}>Promosi</option>
                        <option value="demosi" {{ request('jenis') == 'demosi' ? 'selected' : '' }}>Demosi</option>
                        <option value="rotasi" {{ request('jenis') == 'rotasi' ? 'selected' : '' }}>Rotasi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Status --</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="month" name="bulan" class="form-control" value="{{ request('bulan') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('kepegawaian.penugasan.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th width="120">No. SK</th>
                        <th>Pegawai</th>
                        <th>Jenis</th>
                        <th>Unit Asal → Tujuan</th>
                        <th width="100">TMT</th>
                        <th width="90">Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penugasan as $index => $p)
                    <tr>
                        <td>{{ $penugasan->firstItem() + $index }}</td>
                        <td><code>{{ $p->no_sk ?? '-' }}</code></td>
                        <td>
                            @if($p->dosen)
                            <strong>{{ $p->dosen->nama_lengkap }}</strong>
                            <br><small class="text-muted">{{ $p->dosen->nidn ?? $p->dosen->nip }} (Dosen)</small>
                            @elseif($p->pegawai)
                            <strong>{{ $p->pegawai->nama }}</strong>
                            <br><small class="text-muted">{{ $p->pegawai->nip }} (Tendik)</small>
                            @endif
                        </td>
                        <td><span class="badge {{ $p->jenis == 'promosi' ? 'bg-success' : ($p->jenis == 'demosi' ? 'bg-danger' : 'bg-secondary') }}">{{ $p->jenis_label }}</span></td>
                        <td>
                            <small>
                                {{ $p->unitKerjaAsal->nama ?? '-' }}
                                <i class="bi bi-arrow-right mx-1"></i>
                                <strong>{{ $p->unitKerjaTujuan->nama ?? '-' }}</strong>
                            </small>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->tmt)->format('d/m/Y') }}</td>
                        <td>{!! $p->status_badge !!}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.penugasan.show', $p) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($p->status, ['draft', 'diajukan']))
                                <a href="{{ route('kepegawaian.penugasan.edit', $p) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if($p->status == 'draft')
                                <form action="{{ route('kepegawaian.penugasan.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-arrow-left-right text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data penugasan/mutasi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($penugasan->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $penugasan->firstItem() }} - {{ $penugasan->lastItem() }} dari {{ $penugasan->total() }} data
            </div>
            {{ $penugasan->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
