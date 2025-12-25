@extends('layouts.app')

@section('title', 'Periode Wisuda')

@section('content')
<div class="page-title">
    <h4>Manajemen Wisuda</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Wisuda</li>
        </ol>
    </nav>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Total Periode</h6>
                        <h3 class="mb-0">{{ $stats['total_periode'] }}</h3>
                    </div>
                    <i class="bi bi-mortarboard fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Periode Aktif</h6>
                        <h3 class="mb-0">{{ $stats['periode_aktif'] }}</h3>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Total Pendaftar</h6>
                        <h3 class="mb-0">{{ $stats['total_pendaftar'] }}</h3>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-dark-50">Lulus Wisuda</h6>
                        <h3 class="mb-0">{{ $stats['lulus_wisuda'] }}</h3>
                    </div>
                    <i class="bi bi-award fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Daftar Periode Wisuda</h5>
        <a href="{{ route('wisuda.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Periode
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="tahun_akademik_id" class="form-select">
                    <option value="">Semua Tahun Akademik</option>
                    @foreach($tahunAkademik as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                        {{ $ta->tahun }} {{ $ta->semester }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Dibuka" {{ request('status') == 'Dibuka' ? 'selected' : '' }}>Dibuka</option>
                    <option value="Ditutup" {{ request('status') == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
        </form>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Periode</th>
                        <th>Tahun Akademik</th>
                        <th>Tanggal Wisuda</th>
                        <th>Pendaftaran</th>
                        <th>Pendaftar</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periodeWisuda as $pw)
                    <tr>
                        <td>{{ $periodeWisuda->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $pw->nama }}</strong>
                            @if($pw->lokasi)
                            <br><small class="text-muted">{{ $pw->lokasi }}</small>
                            @endif
                        </td>
                        <td>{{ $pw->tahunAkademik->tahun ?? '-' }} {{ $pw->tahunAkademik->semester ?? '' }}</td>
                        <td>{{ $pw->tanggal_wisuda->format('d M Y') }}</td>
                        <td>
                            <small>
                                {{ $pw->tanggal_buka_pendaftaran->format('d/m/Y') }} - 
                                {{ $pw->tanggal_tutup_pendaftaran->format('d/m/Y') }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $pw->pendaftaran_count }}</span>
                            @if($pw->kuota)
                            / {{ $pw->kuota }}
                            @endif
                        </td>
                        <td>Rp {{ number_format($pw->biaya_wisuda, 0, ',', '.') }}</td>
                        <td>{!! $pw->status_badge !!}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('wisuda.show', $pw) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('wisuda.edit', $pw) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                        data-bs-toggle="dropdown" title="Status">
                                    <i class="bi bi-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><h6 class="dropdown-header">Ubah Status</h6></li>
                                    @foreach(['Draft', 'Dibuka', 'Ditutup', 'Selesai'] as $status)
                                    <li>
                                        <form action="{{ route('wisuda.toggle-status', $pw) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button type="submit" class="dropdown-item {{ $pw->status == $status ? 'active' : '' }}">
                                                {{ $status }}
                                            </button>
                                        </form>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Belum ada data periode wisuda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($periodeWisuda->hasPages())
    <div class="card-footer">
        {{ $periodeWisuda->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
