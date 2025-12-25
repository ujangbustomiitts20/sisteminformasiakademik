@extends('layouts.app')

@section('title', 'Bimbingan Akademik')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Manajemen Bimbingan Akademik</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Bimbingan Akademik</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="dosen" class="form-select form-select-sm">
                    <option value="">-- Semua Dosen --</option>
                    @foreach($dosens as $dosen)
                    <option value="{{ $dosen->id }}" {{ request('dosen') == $dosen->id ? 'selected' : '' }}>
                        {{ $dosen->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Status --</option>
                    <option value="Dijadwalkan" {{ request('status') == 'Dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">-- Jenis --</option>
                    <option value="KRS" {{ request('jenis') == 'KRS' ? 'selected' : '' }}>KRS</option>
                    <option value="Akademik" {{ request('jenis') == 'Akademik' ? 'selected' : '' }}>Akademik</option>
                    <option value="Pribadi" {{ request('jenis') == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                    <option value="Karir" {{ request('jenis') == 'Karir' ? 'selected' : '' }}>Karir</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari mahasiswa..." value="{{ request('search') }}">
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
                        <th>Tanggal</th>
                        <th>Mahasiswa</th>
                        <th>Dosen</th>
                        <th>Jenis</th>
                        <th>Topik</th>
                        <th>Status</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bimbingans as $bimbingan)
                    <tr>
                        <td>{{ $bimbingan->tanggal_bimbingan->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $bimbingan->mahasiswa->nama }}</strong>
                            <br><small class="text-muted">{{ $bimbingan->mahasiswa->nim }}</small>
                        </td>
                        <td>{{ $bimbingan->dosen->nama }}</td>
                        <td><span class="badge bg-{{ $bimbingan->jenis_badge }}">{{ $bimbingan->jenis }}</span></td>
                        <td>{{ Str::limit($bimbingan->topik, 40) }}</td>
                        <td><span class="badge bg-{{ $bimbingan->status_badge }}">{{ $bimbingan->status }}</span></td>
                        <td>
                            <a href="{{ route('bimbingan.show', $bimbingan) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Belum ada data bimbingan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bimbingans->hasPages())
    <div class="card-footer">
        {{ $bimbingans->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
