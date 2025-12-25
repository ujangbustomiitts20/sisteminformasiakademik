@extends('layouts.app')

@section('title', 'Penerima Beasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Penerima Beasiswa</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penerima Beasiswa</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('beasiswa.penerima.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Penerima
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-white-50">Total Penerima Aktif</h6>
                            <h3 class="mb-0">{{ $stats['total_penerima'] }}</h3>
                        </div>
                        <i class="bi bi-people display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-muted">Menunggu Approval</h6>
                            <h3 class="mb-0">{{ $stats['menunggu_approval'] }}</h3>
                        </div>
                        <i class="bi bi-hourglass-split display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIM/Nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="beasiswa_id" class="form-select">
                        <option value="">Semua Beasiswa</option>
                        @foreach($beasiswa as $b)
                            <option value="{{ $b->id }}" {{ request('beasiswa_id') == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Diajukan" {{ request('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="Dicabut" {{ request('status') == 'Dicabut' ? 'selected' : '' }}>Dicabut</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun_akademik_id" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunAkademik as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Beasiswa</th>
                            <th>Tahun Akademik</th>
                            <th>Periode</th>
                            <th class="text-center">Status</th>
                            <th>Approval</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerima as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->mahasiswa->nama ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $item->mahasiswa->nim ?? '-' }} - {{ $item->mahasiswa->programStudi->nama ?? '-' }}</small>
                            </td>
                            <td>{{ $item->beasiswa->nama ?? '-' }}</td>
                            <td>{{ $item->tahunAkademik->nama ?? '-' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}
                                @if($item->tanggal_selesai)
                                    <br><small class="text-muted">s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td class="text-center">{!! $item->status_badge !!}</td>
                            <td>
                                @if($item->approver)
                                    {{ $item->approver->name }}
                                    <br><small class="text-muted">{{ $item->approved_at ? \Carbon\Carbon::parse($item->approved_at)->format('d/m/Y H:i') : '' }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->status === 'Diajukan')
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('beasiswa.penerima.approve', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success" title="Setujui" onclick="return confirm('Setujui beasiswa ini?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-outline-danger" title="Tolak" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                @elseif($item->status === 'Disetujui')
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Cabut" data-bs-toggle="modal" data-bs-target="#revokeModal{{ $item->id }}">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                                @endif

                                <!-- Reject Modal -->
                                @if($item->status === 'Diajukan')
                                <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('beasiswa.penerima.reject', $item) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tolak Pengajuan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                        <textarea name="catatan" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Revoke Modal -->
                                @if($item->status === 'Disetujui')
                                <div class="modal fade" id="revokeModal{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('beasiswa.penerima.revoke', $item) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cabut Beasiswa</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Pencabutan <span class="text-danger">*</span></label>
                                                        <textarea name="catatan" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Cabut</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada data penerima beasiswa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($penerima->hasPages())
        <div class="card-footer bg-white">
            {{ $penerima->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
