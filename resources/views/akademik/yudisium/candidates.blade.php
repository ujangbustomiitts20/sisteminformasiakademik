@extends('layouts.app')

@section('title', 'Kandidat Yudisium')

@section('content')
<div class="page-title">
    <h4>Kandidat Yudisium</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('yudisium.index') }}">Yudisium</a></li>
            <li class="breadcrumb-item active">Kandidat</li>
        </ol>
    </nav>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Daftar mahasiswa yang sudah lolos verifikasi wisuda dan siap untuk diproses yudisiumnya.
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Daftar Kandidat</h5>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="periode_wisuda_id" class="form-select">
                    <option value="">Semua Periode Wisuda</option>
                    @foreach($periodeWisuda as $pw)
                    <option value="{{ $pw->id }}" {{ request('periode_wisuda_id') == $pw->id ? 'selected' : '' }}>
                        {{ $pw->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Pendaftaran</th>
                        <th>Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Periode Wisuda</th>
                        <th>IPK</th>
                        <th>SKS</th>
                        <th>Tanggal Lolos</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $c)
                    <tr>
                        <td>{{ $candidates->firstItem() + $loop->index }}</td>
                        <td><code>{{ $c->no_pendaftaran }}</code></td>
                        <td>
                            <strong>{{ $c->mahasiswa->nama ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $c->mahasiswa->nim ?? '' }}</small>
                        </td>
                        <td>{{ $c->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td>{{ $c->periodeWisuda->nama ?? '-' }}</td>
                        <td><strong>{{ number_format($c->ipk, 2) }}</strong></td>
                        <td>{{ $c->total_sks }}</td>
                        <td>{{ $c->tanggal_verifikasi?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('yudisium.create', $c) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-journal-plus me-1"></i>Proses Yudisium
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Tidak ada kandidat yudisium</p>
                            <small>Pastikan ada pendaftaran wisuda dengan status "Lolos Yudisium"</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($candidates->hasPages())
    <div class="card-footer">
        {{ $candidates->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
