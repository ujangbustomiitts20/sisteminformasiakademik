@extends('layouts.app')

@section('title', 'Bimbingan Akademik')

@section('content')
<div class="page-title">
    <h4>Bimbingan Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Bimbingan Akademik</li>
        </ol>
    </nav>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $summary['total'] ?? 0 }}</h3>
                <small>Total Bimbingan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $summary['pending'] ?? 0 }}</h3>
                <small>Menunggu</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $summary['selesai'] ?? 0 }}</h3>
                <small>Selesai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $summary['dosen_count'] ?? 0 }}</h3>
                <small>Dosen PA Aktif</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Daftar Bimbingan Akademik - {{ $prodi->nama }}</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="dosen_id" class="form-select">
                    <option value="">Semua Dosen PA</option>
                    @foreach($dosens as $dosen)
                    <option value="{{ $dosen->id }}" {{ request('dosen_id') == $dosen->id ? 'selected' : '' }}>
                        {{ $dosen->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="dijadwalkan" {{ request('status') == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NIM/Nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mahasiswa</th>
                        <th>Dosen PA</th>
                        <th>Topik</th>
                        <th class="text-center">Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bimbingans as $bimbingan)
                    <tr>
                        <td>{{ $bimbingan->tanggal?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <strong>{{ $bimbingan->mahasiswa->nim ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $bimbingan->mahasiswa->nama ?? '-' }}</small>
                        </td>
                        <td>{{ $bimbingan->dosen->nama ?? '-' }}</td>
                        <td>{{ Str::limit($bimbingan->topik ?? $bimbingan->catatan, 50) }}</td>
                        <td class="text-center">
                            @php
                                $status = $bimbingan->status ?? 'pending';
                                $statusClass = match($status) {
                                    'selesai' => 'success',
                                    'dijadwalkan' => 'info',
                                    'dibatalkan' => 'danger',
                                    default => 'warning'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('kaprodi.bimbingan.show', $bimbingan) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data bimbingan akademik</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $bimbingans->links() }}
    </div>
</div>
@endsection
