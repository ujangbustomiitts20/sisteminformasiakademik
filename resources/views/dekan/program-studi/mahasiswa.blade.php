@extends('layouts.app')

@section('title', 'Mahasiswa Angkatan ' . $angkatan . ' - ' . $programStudi->nama)

@section('content')
<div class="page-title">
    <h4>Mahasiswa Angkatan {{ $angkatan }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.program-studi.index') }}">Program Studi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.program-studi.show', $programStudi->hashid) }}">{{ $programStudi->nama }}</a></li>
            <li class="breadcrumb-item active">Angkatan {{ $angkatan }}</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $totalMahasiswa }}</h3>
                <small>Total Mahasiswa</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $statusCount['aktif'] ?? 0 }}</h3>
                <small>Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $statusCount['cuti'] ?? 0 }}</h3>
                <small>Cuti</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $statusCount['lulus'] ?? 0 }}</h3>
                <small>Lulus</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-people me-2"></i>Daftar Mahasiswa - {{ $programStudi->nama }} (Angkatan {{ $angkatan }})
        </h6>
        <div>
            <form action="" method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="DO" {{ request('status') == 'DO' ? 'selected' : '' }}>DO</option>
                </select>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIM/Nama..." value="{{ request('search') }}" style="width: 200px;">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search') || request('status'))
                <a href="{{ route('dekan.program-studi.mahasiswa', ['programStudi' => $programStudi->hashid, 'angkatan' => $angkatan]) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Dosen Wali</th>
                        <th>Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $index => $mhs)
                    <tr>
                        <td>{{ $mahasiswas->firstItem() + $index }}</td>
                        <td><strong>{{ $mhs->nim }}</strong></td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->jenis_kelamin == 'L' ? 'Laki-laki' : ($mhs->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                        <td>{{ $mhs->dosenWali->nama ?? '-' }}</td>
                        <td>
                            @php
                                $statusColor = match(strtolower($mhs->status)) {
                                    'aktif' => 'success',
                                    'cuti' => 'warning',
                                    'lulus' => 'info',
                                    'do', 'tidak aktif' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusColor }}">{{ $mhs->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('dekan.mahasiswa.show', $mhs->hashid) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data mahasiswa
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($mahasiswas->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $mahasiswas->firstItem() }} - {{ $mahasiswas->lastItem() }} dari {{ $mahasiswas->total() }} mahasiswa
            </small>
            {{ $mahasiswas->withQueryString()->links() }}
        </div>
    </div>
    @endif
</div>

<div class="mt-3">
    <a href="{{ route('dekan.program-studi.show', $programStudi->hashid) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Detail Program Studi
    </a>
</div>
@endsection
