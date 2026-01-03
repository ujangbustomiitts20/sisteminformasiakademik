@extends('layouts.app')

@section('title', 'Monitoring IPK')

@section('content')
<div class="page-title">
    <h4>Monitoring IPK Mahasiswa</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Monitoring IPK</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Monitoring IPK - {{ $prodi->nama }}</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Angkatan</label>
                <select name="angkatan" class="form-select">
                    <option value="">Semua Angkatan</option>
                    @foreach($angkatans as $angkatan)
                    <option value="{{ $angkatan }}" {{ request('angkatan') == $angkatan ? 'selected' : '' }}>
                        {{ $angkatan }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Range IPK</label>
                <select name="range" class="form-select">
                    <option value="">Semua</option>
                    <option value="cumlaude" {{ request('range') == 'cumlaude' ? 'selected' : '' }}>Cumlaude (≥3.50)</option>
                    <option value="sangat_memuaskan" {{ request('range') == 'sangat_memuaskan' ? 'selected' : '' }}>Sangat Memuaskan (3.00-3.49)</option>
                    <option value="memuaskan" {{ request('range') == 'memuaskan' ? 'selected' : '' }}>Memuaskan (2.50-2.99)</option>
                    <option value="cukup" {{ request('range') == 'cukup' ? 'selected' : '' }}>Cukup (2.00-2.49)</option>
                    <option value="rendah" {{ request('range') == 'rendah' ? 'selected' : '' }}>Di Bawah Standar (<2.00)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="NIM / Nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card border-success">
                    <div class="card-body text-center py-2">
                        <h4 class="text-success mb-0">{{ $summary['cumlaude'] ?? 0 }}</h4>
                        <small class="text-muted">Cumlaude<br>(≥3.50)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-primary">
                    <div class="card-body text-center py-2">
                        <h4 class="text-primary mb-0">{{ $summary['sangat_memuaskan'] ?? 0 }}</h4>
                        <small class="text-muted">Sangat Memuaskan<br>(3.00-3.49)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-info">
                    <div class="card-body text-center py-2">
                        <h4 class="text-info mb-0">{{ $summary['memuaskan'] ?? 0 }}</h4>
                        <small class="text-muted">Memuaskan<br>(2.50-2.99)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-warning">
                    <div class="card-body text-center py-2">
                        <h4 class="text-warning mb-0">{{ $summary['cukup'] ?? 0 }}</h4>
                        <small class="text-muted">Cukup<br>(2.00-2.49)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-danger">
                    <div class="card-body text-center py-2">
                        <h4 class="text-danger mb-0">{{ $summary['rendah'] ?? 0 }}</h4>
                        <small class="text-muted">Rendah<br>(<2.00)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ number_format($summary['rata_rata'] ?? 0, 2) }}</h4>
                        <small>Rata-rata IPK<br>Prodi</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Angkatan</th>
                        <th class="text-center">Total SKS</th>
                        <th class="text-center">IPK</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $mhs)
                    @php
                        $ipk = $mhs->ipk ?? 0;
                        if ($ipk >= 3.50) {
                            $predikat = 'Cumlaude';
                            $class = 'success';
                        } elseif ($ipk >= 3.00) {
                            $predikat = 'Sangat Memuaskan';
                            $class = 'primary';
                        } elseif ($ipk >= 2.50) {
                            $predikat = 'Memuaskan';
                            $class = 'info';
                        } elseif ($ipk >= 2.00) {
                            $predikat = 'Cukup';
                            $class = 'warning';
                        } else {
                            $predikat = 'Rendah';
                            $class = 'danger';
                        }
                    @endphp
                    <tr>
                        <td>{{ $mhs->nim }}</td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td class="text-center">{{ $mhs->total_sks ?? 0 }}</td>
                        <td class="text-center">
                            <strong class="text-{{ $class }}">{{ number_format($ipk, 2) }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $class }}">{{ $predikat }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data mahasiswa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $mahasiswas->links() }}
    </div>
</div>
@endsection
