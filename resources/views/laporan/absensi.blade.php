@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Absensi</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">Absensi</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('laporan.absensi', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
        <i class="bi bi-file-pdf me-1"></i>Export PDF
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.absensi') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select">
                    @foreach($tahunAkademik as $ta)
                    <option value="{{ $ta->id }}" {{ ($tahunAkademikAktif?->id == $ta->id) ? 'selected' : '' }}>
                        {{ $ta->tahun }} - {{ $ta->semester }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hasil -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-clipboard-check me-2"></i>Rekap Kehadiran Mahasiswa
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Mata Kuliah</th>
                        <th class="text-center">Pertemuan</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Alpha</th>
                        <th class="text-center">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $data['mahasiswa']->nim }}</code></td>
                        <td>{{ $data['mahasiswa']->nama }}</td>
                        <td>{{ $data['mata_kuliah'] }}</td>
                        <td class="text-center">{{ $data['total'] }}</td>
                        <td class="text-center"><span class="badge bg-success">{{ $data['hadir'] }}</span></td>
                        <td class="text-center"><span class="badge bg-info">{{ $data['izin'] }}</span></td>
                        <td class="text-center"><span class="badge bg-warning">{{ $data['sakit'] }}</span></td>
                        <td class="text-center"><span class="badge bg-danger">{{ $data['alpha'] }}</span></td>
                        <td class="text-center">
                            <span class="badge bg-{{ $data['persentase'] >= 80 ? 'success' : ($data['persentase'] >= 60 ? 'warning' : 'danger') }}">
                                {{ $data['persentase'] }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-3">Tidak ada data absensi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <small class="text-muted">
                <strong>Keterangan:</strong> Persentase kehadiran minimal 80% untuk dapat mengikuti ujian
            </small>
        </div>
    </div>
</div>
@endsection
