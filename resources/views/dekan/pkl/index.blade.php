@extends('layouts.app')

@section('title', 'Monitoring PKL/Magang')

@section('content')
<div class="page-title">
    <h4>Monitoring PKL/Magang - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">PKL/Magang</li>
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
        <div class="card border-info">
            <div class="card-body text-center">
                <h3 class="text-info">{{ $stats['berlangsung'] ?? 0 }}</h3>
                <small class="text-muted">Berlangsung</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <h3 class="text-success">{{ $stats['selesai'] ?? 0 }}</h3>
                <small class="text-muted">Selesai</small>
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
                    <option value="berlangsung" {{ request('status') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
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
        <h6 class="mb-0">Daftar PKL/Magang</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Jenis Kegiatan</th>
                        <th>Mitra/Lokasi</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftarans as $pendaftaran)
                    <tr>
                        <td>
                            <strong>{{ $pendaftaran->mahasiswa->nama ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $pendaftaran->mahasiswa->nim ?? '-' }}</small>
                        </td>
                        <td>{{ $pendaftaran->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td>{{ $pendaftaran->periode->jenisKegiatan->nama ?? '-' }}</td>
                        <td>{{ $pendaftaran->mitraDiterima->nama ?? '-' }}</td>
                        <td>{{ $pendaftaran->periode->nama ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $pendaftaran->status == 'selesai' ? 'success' : ($pendaftaran->status == 'berlangsung' ? 'info' : 'warning') }}">
                                {{ ucfirst($pendaftaran->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('dekan.pkl.show', $pendaftaran) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Tidak ada data PKL/Magang</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pendaftarans->withQueryString()->links() }}
    </div>
</div>
@endsection
