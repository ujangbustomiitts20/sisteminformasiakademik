@extends('layouts.app')

@section('title', 'Monitoring Yudisium')

@section('content')
<div class="page-title">
    <h4>Monitoring Yudisium - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Yudisium</li>
        </ol>
    </nav>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h3 class="text-warning">{{ $stats['pending'] ?? 0 }}</h3>
                <small class="text-muted">Pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <h3 class="text-success">{{ $stats['lulus'] ?? 0 }}</h3>
                <small class="text-muted">Lulus</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h3 class="text-danger">{{ $stats['tidak_lulus'] ?? 0 }}</h3>
                <small class="text-muted">Tidak Lulus</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="prodi" class="form-select">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="tidak_lulus" {{ request('status') == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Daftar Yudisium</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th class="text-center">IPK</th>
                        <th class="text-center">SKS</th>
                        <th>Predikat</th>
                        <th>Status</th>
                        <th>Periode Wisuda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($yudisiums as $yudisium)
                    <tr>
                        <td>{{ $yudisium->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $yudisium->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $yudisium->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td class="text-center">{{ number_format($yudisium->ipk ?? 0, 2) }}</td>
                        <td class="text-center">{{ $yudisium->total_sks ?? 0 }}</td>
                        <td>{{ $yudisium->predikat ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $yudisium->status == 'lulus' ? 'success' : ($yudisium->status == 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($yudisium->status) }}
                            </span>
                        </td>
                        <td>{{ $yudisium->pendaftaranWisuda->periodeWisuda->nama ?? '-' }}</td>
                        <td>
                            <a href="{{ route('dekan.yudisium.show', $yudisium) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">Tidak ada data yudisium</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $yudisiums->withQueryString()->links() }}
    </div>
</div>
@endsection
