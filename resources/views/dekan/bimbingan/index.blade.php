@extends('layouts.app')

@section('title', 'Bimbingan Akademik')

@section('content')
<div class="page-title">
    <h4>Monitoring Bimbingan Akademik - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Bimbingan Akademik</li>
        </ol>
    </nav>
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
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Dijadwalkan" {{ request('status') == 'Dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
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
        <h6 class="mb-0">Daftar Bimbingan Akademik</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Dosen PA</th>
                        <th>Topik</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bimbingans as $bimbingan)
                    <tr>
                        <td>
                            <strong>{{ $bimbingan->mahasiswa->nama ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $bimbingan->mahasiswa->nim ?? '-' }}</small>
                        </td>
                        <td>{{ $bimbingan->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td>{{ $bimbingan->dosen->nama ?? '-' }}</td>
                        <td>{{ Str::limit($bimbingan->topik ?? '-', 50) }}</td>
                        <td>{{ $bimbingan->tanggal ? \Carbon\Carbon::parse($bimbingan->tanggal)->format('d/m/Y') : '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $bimbingan->status == 'Selesai' ? 'success' : ($bimbingan->status == 'Pending' ? 'warning' : 'info') }}">
                                {{ $bimbingan->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('dekan.bimbingan.show', $bimbingan) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Tidak ada data bimbingan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $bimbingans->withQueryString()->links() }}
    </div>
</div>
@endsection
