@extends('layouts.app')

@section('title', 'Mahasiswa Prodi')

@section('content')
<div class="page-title">
    <h4>Mahasiswa Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Mahasiswa</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ $prodi->nama }}</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="do" {{ request('status') == 'do' ? 'selected' : '' }}>Drop Out</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="angkatan" class="form-select">
                    <option value="">Semua Angkatan</option>
                    @foreach($angkatans as $angkatan)
                    <option value="{{ $angkatan }}" {{ request('angkatan') == $angkatan ? 'selected' : '' }}>{{ $angkatan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NIM atau Nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Angkatan</th>
                        <th>Semester</th>
                        <th>IPK</th>
                        <th>Dosen Wali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $mhs)
                    <tr>
                        <td>{{ $mhs->nim }}</td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td>{{ $mhs->semester ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $mhs->ipk >= 3.0 ? 'success' : ($mhs->ipk >= 2.0 ? 'warning' : 'danger') }}">
                                {{ number_format($mhs->ipk, 2) }}
                            </span>
                        </td>
                        <td>{{ $mhs->dosenWali->nama ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $mhs->status == 'aktif' ? 'success' : ($mhs->status == 'cuti' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($mhs->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('kaprodi.mahasiswa.show', $mhs) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Tidak ada data mahasiswa
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $mahasiswas->links() }}
    </div>
</div>
@endsection
