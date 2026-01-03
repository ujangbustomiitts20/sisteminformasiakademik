@extends('layouts.app')

@section('title', 'Laporan Pengambilan KRS')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Pengambilan KRS</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">KRS</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('laporan.krs', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.krs') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select">
                    <option value="">Pilih Tahun Akademik</option>
                    @foreach($tahunAkademik as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                        {{ $ta->tahun ?? '' }} - {{ $ta->semester ?? '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('laporan.krs') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Statistik -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['total_mk'] ?? 0 }}</h3>
                <small>Total Mata Kuliah</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['total_peserta'] ?? 0 }}</h3>
                <small>Total Peserta</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['rata_peserta'] ?? 0 }}</h3>
                <small>Rata-rata Peserta/MK</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['mk_penuh'] ?? 0 }}</h3>
                <small>Kelas Penuh</small>
            </div>
        </div>
    </div>
</div>

<!-- Hasil -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-journal-text me-2"></i>Data Pengambilan KRS per Mata Kuliah
        @if($tahunAkademikAktif)
            - {{ $tahunAkademikAktif->tahun ?? '' }} {{ $tahunAkademikAktif->semester ?? '' }}
        @endif
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode MK</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Kelas</th>
                        <th>SKS</th>
                        <th>Peserta</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($krsData as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $data['kode'] }}</code></td>
                        <td>{{ $data['mata_kuliah'] }}</td>
                        <td>{{ $data['dosen'] }}</td>
                        <td>{{ $data['kelas'] }}</td>
                        <td>{{ $data['sks'] }} SKS</td>
                        <td><strong>{{ $data['peserta'] }}</strong></td>
                        <td>{{ $data['kapasitas'] }}</td>
                        <td>
                            @if($data['peserta'] >= $data['kapasitas'])
                                <span class="badge bg-danger">Penuh</span>
                            @elseif($data['peserta'] >= $data['kapasitas'] * 0.8)
                                <span class="badge bg-warning">Hampir Penuh</span>
                            @else
                                <span class="badge bg-success">Tersedia</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">Tidak ada data KRS</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
