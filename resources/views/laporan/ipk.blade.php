@extends('layouts.app')

@section('title', 'Laporan Distribusi IPK')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Distribusi IPK Mahasiswa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">Distribusi IPK</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('laporan.ipk', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.ipk') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Program Studi</label>
                <select name="program_studi_id" class="form-select">
                    <option value="">Semua Program Studi</option>
                    @foreach($programStudi as $prodi)
                    <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                        {{ $prodi->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Angkatan</label>
                <select name="angkatan" class="form-select">
                    <option value="">Semua Angkatan</option>
                    @for($y = date('Y'); $y >= date('Y') - 10; $y--)
                    <option value="{{ $y }}" {{ request('angkatan') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('laporan.ipk') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Statistik IPK -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $distribusi['cumlaude'] ?? 0 }}</h3>
                <small>Cum Laude (≥3.51)</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $distribusi['sangat_memuaskan'] ?? 0 }}</h3>
                <small>Sangat Memuaskan (3.01-3.50)</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $distribusi['memuaskan'] ?? 0 }}</h3>
                <small>Memuaskan (2.76-3.00)</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>{{ $distribusi['cukup'] ?? 0 }}</h3>
                <small>Cukup (2.00-2.75)</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="display-4 text-primary">{{ number_format($summary['ipk_rata'] ?? 0, 2) }}</h1>
                <p class="text-muted">Rata-rata IPK</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="display-4 text-success">{{ number_format($summary['ipk_tertinggi'] ?? 0, 2) }}</h1>
                <p class="text-muted">IPK Tertinggi</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="display-4 text-danger">{{ number_format($summary['ipk_terendah'] ?? 0, 2) }}</h1>
                <p class="text-muted">IPK Terendah</p>
            </div>
        </div>
    </div>
</div>

<!-- Hasil Detail -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ol me-2"></i>Detail IPK Mahasiswa ({{ $summary['total'] ?? 0 }} orang)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>IPK</th>
                        <th>Total SKS</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswa as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $data['mahasiswa']->nim }}</code></td>
                        <td>{{ $data['mahasiswa']->nama }}</td>
                        <td>{{ $data['mahasiswa']->programStudi->nama ?? '-' }}</td>
                        <td>{{ $data['mahasiswa']->angkatan }}</td>
                        <td><strong>{{ number_format($data['ipk'], 2) }}</strong></td>
                        <td>{{ $data['total_sks'] }} SKS</td>
                        <td>
                            <span class="badge bg-{{ $data['kategori'] == 'Cum Laude' ? 'success' : ($data['kategori'] == 'Sangat Memuaskan' ? 'primary' : ($data['kategori'] == 'Memuaskan' ? 'info' : 'warning')) }}">
                                {{ $data['kategori'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">Tidak ada data mahasiswa</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
