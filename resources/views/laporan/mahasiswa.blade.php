@extends('layouts.app')

@section('title', 'Laporan Mahasiswa')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Mahasiswa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">Mahasiswa</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('export.mahasiswa', request()->all()) }}" class="btn btn-success me-2">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
        </a>
        <a href="{{ route('laporan.mahasiswa', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.mahasiswa') }}" method="GET" class="row g-3">
            <div class="col-md-3">
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
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="DO" {{ request('status') == 'DO' ? 'selected' : '' }}>Drop Out</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Angkatan</label>
                <select name="angkatan" class="form-select">
                    <option value="">Semua Angkatan</option>
                    @for($y = date('Y'); $y >= date('Y') - 10; $y--)
                    <option value="{{ $y }}" {{ request('angkatan') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('laporan.mahasiswa') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Hasil -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-people me-2"></i>Data Mahasiswa ({{ $mahasiswa->count() }} orang)
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
                        <th>Status</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswa as $index => $mhs)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $mhs->nim }}</code></td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td>
                            <span class="badge bg-{{ $mhs->status == 'Aktif' ? 'success' : ($mhs->status == 'Cuti' ? 'warning' : 'secondary') }}">
                                {{ $mhs->status }}
                            </span>
                        </td>
                        <td>{{ $mhs->user->email ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
