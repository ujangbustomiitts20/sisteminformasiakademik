@extends('layouts.app')

@section('title', 'History Status Mahasiswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">History Status Mahasiswa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cuti.index') }}">Cuti Akademik</a></li>
                <li class="breadcrumb-item active">History</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('cuti.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIM/nama mahasiswa..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Mahasiswa</th>
                        <th>Status Lama</th>
                        <th></th>
                        <th>Status Baru</th>
                        <th>Keterangan</th>
                        <th>Diubah Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                    <tr>
                        <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <strong>{{ $history->mahasiswa->nama }}</strong>
                            <br><small class="text-muted">{{ $history->mahasiswa->nim }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $history->status_lama == 'Aktif' ? 'success' : ($history->status_lama == 'Cuti' ? 'warning' : 'secondary') }}">
                                {{ $history->status_lama }}
                            </span>
                        </td>
                        <td><i class="bi bi-arrow-right"></i></td>
                        <td>
                            <span class="badge bg-{{ $history->status_baru == 'Aktif' ? 'success' : ($history->status_baru == 'Cuti' ? 'warning' : 'secondary') }}">
                                {{ $history->status_baru }}
                            </span>
                        </td>
                        <td>{{ Str::limit($history->keterangan, 50) ?? '-' }}</td>
                        <td>{{ $history->diubahOleh->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Tidak ada history perubahan status</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($histories->hasPages())
    <div class="card-footer">
        {{ $histories->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
