@extends('layouts.app')

@section('title', 'Mahasiswa Fakultas')

@section('content')
<div class="page-title">
    <h4>Mahasiswa Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Mahasiswa</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Program Studi</label>
                <select name="prodi" class="form-select">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="DO" {{ request('status') == 'DO' ? 'selected' : '' }}>DO</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Angkatan</label>
                <select name="angkatan" class="form-select">
                    <option value="">Semua</option>
                    @foreach($angkatans as $angkatan)
                        <option value="{{ $angkatan }}" {{ request('angkatan') == $angkatan ? 'selected' : '' }}>
                            {{ $angkatan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="NIM / Nama" value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('dekan.mahasiswa.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Daftar Mahasiswa</h6>
        <a href="{{ route('dekan.mahasiswa.bermasalah') }}" class="btn btn-sm btn-warning">
            <i class="bi bi-exclamation-triangle me-1"></i> Mahasiswa Bermasalah
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>Status</th>
                        <th>Dosen PA</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $mhs)
                    <tr>
                        <td>{{ $mhs->nim }}</td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td>
                            <span class="badge bg-{{ $mhs->status == 'Aktif' ? 'success' : ($mhs->status == 'Cuti' ? 'warning' : 'secondary') }}">
                                {{ $mhs->status }}
                            </span>
                        </td>
                        <td>{{ $mhs->dosenWali->nama ?? '-' }}</td>
                        <td>
                            <a href="{{ route('dekan.mahasiswa.show', $mhs) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Tidak ada data mahasiswa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $mahasiswas->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
