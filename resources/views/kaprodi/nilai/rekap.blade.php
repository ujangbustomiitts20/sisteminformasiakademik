@extends('layouts.app')

@section('title', 'Rekap Nilai')

@section('content')
<div class="page-title">
    <h4>Rekap Nilai Mahasiswa</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Rekap Nilai</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Rekap Nilai - {{ $prodi->nama }}</h6>
        <a href="{{ route('kaprodi.nilai.export') }}?{{ http_build_query(request()->all()) }}" class="btn btn-success btn-sm">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select">
                    @foreach($tahunAkademiks as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_akademik_id', $tahunAkademikAktif?->id) == $ta->id ? 'selected' : '' }}>
                        {{ $ta->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mata Kuliah</label>
                <select name="mata_kuliah_id" class="form-select">
                    <option value="">Semua Mata Kuliah</option>
                    @foreach($mataKuliahs as $mk)
                    <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                        {{ $mk->kode }} - {{ $mk->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
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
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $summary['total'] ?? 0 }}</h3>
                        <small>Total Mahasiswa</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ number_format($summary['rata_ipk'] ?? 0, 2) }}</h3>
                        <small>Rata-rata IPK</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $summary['lulus'] ?? 0 }}</h3>
                        <small>Lulus (≥D)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $summary['tidak_lulus'] ?? 0 }}</h3>
                        <small>Tidak Lulus (E)</small>
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
                        <th>Mata Kuliah</th>
                        <th class="text-center">SKS</th>
                        <th class="text-center">Nilai Angka</th>
                        <th class="text-center">Nilai Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nilais as $nilai)
                    <tr>
                        <td>{{ $nilai->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $nilai->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $nilai->mataKuliah->kode ?? '-' }} - {{ $nilai->mataKuliah->nama ?? '-' }}</td>
                        <td class="text-center">{{ $nilai->mataKuliah->sks ?? '-' }}</td>
                        <td class="text-center">{{ number_format($nilai->nilai_angka ?? 0, 2) }}</td>
                        <td class="text-center">
                            @if($nilai->nilai_huruf)
                            <span class="badge bg-{{ in_array($nilai->nilai_huruf, ['A', 'A-', 'B+', 'B']) ? 'success' : ($nilai->nilai_huruf == 'E' ? 'danger' : 'secondary') }}">
                                {{ $nilai->nilai_huruf }}
                            </span>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data nilai</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $nilais->links() }}
    </div>
</div>
@endsection
