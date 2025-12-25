@extends('layouts.app')

@section('title', 'Cuti Akademik')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Manajemen Cuti Akademik</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Cuti Akademik</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('cuti.history') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>History Status
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Pending</small>
                        <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                    </div>
                    <i class="bi bi-hourglass-split" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Disetujui Kaprodi</small>
                        <h4 class="mb-0">{{ $stats['disetujui_kaprodi'] }}</h4>
                    </div>
                    <i class="bi bi-person-check" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Disetujui Dekan</small>
                        <h4 class="mb-0">{{ $stats['disetujui_dekan'] }}</h4>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Ditolak</small>
                        <h4 class="mb-0">{{ $stats['ditolak'] }}</h4>
                    </div>
                    <i class="bi bi-x-circle" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Disetujui Kaprodi" {{ request('status') == 'Disetujui Kaprodi' ? 'selected' : '' }}>Disetujui Kaprodi</option>
                    <option value="Disetujui Dekan" {{ request('status') == 'Disetujui Dekan' ? 'selected' : '' }}>Disetujui Dekan</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="alasan" class="form-select form-select-sm">
                    <option value="">-- Alasan --</option>
                    <option value="Keuangan" {{ request('alasan') == 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                    <option value="Kesehatan" {{ request('alasan') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                    <option value="Keluarga" {{ request('alasan') == 'Keluarga' ? 'selected' : '' }}>Keluarga</option>
                    <option value="Pekerjaan" {{ request('alasan') == 'Pekerjaan' ? 'selected' : '' }}>Pekerjaan</option>
                    <option value="Lainnya" {{ request('alasan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIM/nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">
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
                        <th>Program Studi</th>
                        <th>Alasan</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cutis as $cuti)
                    <tr>
                        <td>
                            <strong>{{ $cuti->mahasiswa->nama }}</strong>
                            <br><small class="text-muted">{{ $cuti->mahasiswa->nim }}</small>
                        </td>
                        <td>{{ $cuti->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td><span class="badge bg-{{ $cuti->alasan_badge }}">{{ $cuti->alasan }}</span></td>
                        <td>
                            {{ $cuti->tanggal_mulai->format('d/m/Y') }} - {{ $cuti->tanggal_selesai->format('d/m/Y') }}
                            <br><small class="text-muted">{{ $cuti->jumlah_semester }} Semester</small>
                        </td>
                        <td><span class="badge bg-{{ $cuti->status_badge }}">{{ $cuti->status }}</span></td>
                        <td>
                            <a href="{{ route('cuti.show', $cuti) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($cuti->status == 'Disetujui Dekan')
                            <a href="{{ route('cuti.print-surat', $cuti) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Tidak ada pengajuan cuti</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($cutis->hasPages())
    <div class="card-footer">
        {{ $cutis->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
