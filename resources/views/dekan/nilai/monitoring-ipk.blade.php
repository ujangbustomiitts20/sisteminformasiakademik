@extends('layouts.app')

@section('title', 'Monitoring IPK Fakultas')

@section('content')
<div class="page-title">
    <h4>Monitoring IPK Fakultas</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Monitoring IPK</li>
        </ol>
    </nav>
</div>

<!-- Statistik Distribusi IPK -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Distribusi IPK Mahasiswa Aktif - {{ $fakultas->nama }}</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <div class="card bg-success text-white">
                            <div class="card-body py-3">
                                <h3 class="mb-0">{{ $distribusiIpk['cumlaude'] }}</h3>
                                <small>Cum Laude (≥3.50)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-primary text-white">
                            <div class="card-body py-3">
                                <h3 class="mb-0">{{ $distribusiIpk['sangat_memuaskan'] }}</h3>
                                <small>Sangat Memuaskan (3.00-3.49)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-info text-white">
                            <div class="card-body py-3">
                                <h3 class="mb-0">{{ $distribusiIpk['memuaskan'] }}</h3>
                                <small>Memuaskan (2.50-2.99)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-warning text-dark">
                            <div class="card-body py-3">
                                <h3 class="mb-0">{{ $distribusiIpk['cukup'] }}</h3>
                                <small>Cukup (2.00-2.49)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger text-white">
                            <div class="card-body py-3">
                                <h3 class="mb-0">{{ $distribusiIpk['kurang'] }}</h3>
                                <small>Kurang (&lt;2.00)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Daftar Mahasiswa -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Daftar IPK Mahasiswa</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="prodi" class="form-select">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="angkatan" class="form-select">
                    <option value="">Semua Angkatan</option>
                    @foreach($angkatans as $angkatan)
                    <option value="{{ $angkatan }}" {{ request('angkatan') == $angkatan ? 'selected' : '' }}>{{ $angkatan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    <option value="cumlaude" {{ request('kategori') == 'cumlaude' ? 'selected' : '' }}>Cum Laude</option>
                    <option value="sangat_memuaskan" {{ request('kategori') == 'sangat_memuaskan' ? 'selected' : '' }}>Sangat Memuaskan</option>
                    <option value="memuaskan" {{ request('kategori') == 'memuaskan' ? 'selected' : '' }}>Memuaskan</option>
                    <option value="cukup" {{ request('kategori') == 'cukup' ? 'selected' : '' }}>Cukup</option>
                    <option value="kurang" {{ request('kategori') == 'kurang' ? 'selected' : '' }}>Kurang</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="ipk_desc" {{ request('sort', 'ipk_desc') == 'ipk_desc' ? 'selected' : '' }}>IPK Tertinggi</option>
                    <option value="ipk_asc" {{ request('sort') == 'ipk_asc' ? 'selected' : '' }}>IPK Terendah</option>
                    <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama A-Z</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('dekan.nilai.monitoring-ipk') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabel -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th class="text-center">SKS</th>
                        <th class="text-center">IPK</th>
                        <th>Predikat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $index => $mhs)
                    @php
                        $predikat = '-';
                        $predikatClass = 'secondary';
                        if ($mhs->total_sks > 0) {
                            if ($mhs->ipk >= 3.50) { $predikat = 'Cum Laude'; $predikatClass = 'success'; }
                            elseif ($mhs->ipk >= 3.00) { $predikat = 'Sangat Memuaskan'; $predikatClass = 'primary'; }
                            elseif ($mhs->ipk >= 2.50) { $predikat = 'Memuaskan'; $predikatClass = 'info'; }
                            elseif ($mhs->ipk >= 2.00) { $predikat = 'Cukup'; $predikatClass = 'warning'; }
                            else { $predikat = 'Kurang'; $predikatClass = 'danger'; }
                        }
                    @endphp
                    <tr>
                        <td>{{ $mahasiswas->firstItem() + $index }}</td>
                        <td>{{ $mhs->nim }}</td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td class="text-center">{{ $mhs->total_sks }}</td>
                        <td class="text-center"><strong>{{ $mhs->ipk }}</strong></td>
                        <td><span class="badge bg-{{ $predikatClass }}">{{ $predikat }}</span></td>
                        <td>
                            <a href="{{ route('dekan.mahasiswa.show', $mhs) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Tidak ada data mahasiswa
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $mahasiswas->appends(request()->query())->links() }}
    </div>
</div>
@endsection
