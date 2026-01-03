@extends('layouts.app')

@section('title', 'Monitoring Absensi')

@section('content')
<div class="page-title">
    <h4>Monitoring Absensi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Monitoring Absensi</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Monitoring Absensi - {{ $prodi->nama }}</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select">
                    @foreach($tahunAkademiks as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_akademik_id', $tahunAkademikAktif?->id) == $ta->id ? 'selected' : '' }}>
                        {{ $ta->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Dosen</label>
                <select name="dosen_id" class="form-select">
                    <option value="">Semua Dosen</option>
                    @foreach($dosens as $dosen)
                    <option value="{{ $dosen->id }}" {{ request('dosen_id') == $dosen->id ? 'selected' : '' }}>
                        {{ $dosen->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>

        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['total_jadwal'] ?? 0 }}</h4>
                        <small>Total Jadwal</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['total_pertemuan'] ?? 0 }}</h4>
                        <small>Total Pertemuan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ number_format($summary['rata_kehadiran'] ?? 0, 1) }}%</h4>
                        <small>Rata-rata Kehadiran</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['perlu_perhatian'] ?? 0 }}</h4>
                        <small>Perlu Perhatian</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Kelas</th>
                        <th class="text-center">Pertemuan</th>
                        <th class="text-center">Kehadiran</th>
                        <th class="text-center">Progress</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $jadwal)
                    @php
                        $totalPertemuan = $jadwal->absensi_count ?? 0;
                        $targetPertemuan = 16;
                        $progress = $targetPertemuan > 0 ? ($totalPertemuan / $targetPertemuan) * 100 : 0;
                        $kehadiran = $jadwal->rata_kehadiran ?? 0;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $jadwal->mataKuliah->kode ?? '-' }}</strong><br>
                            <small>{{ $jadwal->mataKuliah->nama ?? '-' }}</small>
                        </td>
                        <td>{{ $jadwal->dosen->nama ?? '-' }}</td>
                        <td>{{ $jadwal->kelas ?? '-' }}</td>
                        <td class="text-center">{{ $totalPertemuan }}/{{ $targetPertemuan }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $kehadiran >= 80 ? 'success' : ($kehadiran >= 60 ? 'warning' : 'danger') }}">
                                {{ number_format($kehadiran, 1) }}%
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $progress >= 75 ? 'success' : ($progress >= 50 ? 'info' : 'warning') }}" 
                                     style="width: {{ min($progress, 100) }}%">
                                    {{ number_format($progress, 0) }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('kaprodi.absensi.show', $jadwal) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Tidak ada data jadwal kuliah</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $jadwals->links() }}
    </div>
</div>
@endsection
