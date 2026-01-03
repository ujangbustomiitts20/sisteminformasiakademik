@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
<div class="page-title">
    <h4>Detail Absensi Mata Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.absensi.index') }}">Monitoring Absensi</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Informasi Jadwal</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted">Mata Kuliah</td>
                        <td>
                            <strong>{{ $jadwal->mataKuliah->kode ?? '-' }}</strong><br>
                            {{ $jadwal->mataKuliah->nama ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">SKS</td>
                        <td>{{ $jadwal->mataKuliah->sks ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dosen</td>
                        <td>{{ $jadwal->dosen->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kelas</td>
                        <td>{{ $jadwal->kelas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Hari</td>
                        <td>{{ $jadwal->hari ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jam</td>
                        <td>{{ $jadwal->jam_mulai ?? '-' }} - {{ $jadwal->jam_selesai ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ruangan</td>
                        <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Statistik Kehadiran</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="text-primary mb-0">{{ $statistik['total_pertemuan'] ?? 0 }}</h4>
                        <small class="text-muted">Total Pertemuan</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-success mb-0">{{ number_format($statistik['rata_kehadiran'] ?? 0, 1) }}%</h4>
                        <small class="text-muted">Rata-rata Hadir</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-0">{{ $statistik['total_mahasiswa'] ?? 0 }}</h4>
                        <small class="text-muted">Total Mahasiswa</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-danger mb-0">{{ $statistik['mhs_bermasalah'] ?? 0 }}</h4>
                        <small class="text-muted">Kehadiran <75%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Rekap Kehadiran Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Izin</th>
                                <th class="text-center">Sakit</th>
                                <th class="text-center">Alpha</th>
                                <th class="text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapMahasiswa as $rekap)
                            @php
                                $total = ($rekap->hadir ?? 0) + ($rekap->izin ?? 0) + ($rekap->sakit ?? 0) + ($rekap->alpha ?? 0);
                                $persentase = $total > 0 ? (($rekap->hadir ?? 0) / $total) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $rekap->mahasiswa->nim ?? '-' }}</td>
                                <td>{{ $rekap->mahasiswa->nama ?? '-' }}</td>
                                <td class="text-center text-success">{{ $rekap->hadir ?? 0 }}</td>
                                <td class="text-center text-info">{{ $rekap->izin ?? 0 }}</td>
                                <td class="text-center text-warning">{{ $rekap->sakit ?? 0 }}</td>
                                <td class="text-center text-danger">{{ $rekap->alpha ?? 0 }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $persentase >= 80 ? 'success' : ($persentase >= 75 ? 'warning' : 'danger') }}">
                                        {{ number_format($persentase, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data absensi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Per Pertemuan -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Detail Pertemuan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Pertemuan</th>
                                <th>Tanggal</th>
                                <th>Materi</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Izin</th>
                                <th class="text-center">Sakit</th>
                                <th class="text-center">Alpha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pertemuans as $idx => $pertemuan)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $pertemuan->tanggal?->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ Str::limit($pertemuan->materi ?? '-', 30) }}</td>
                                <td class="text-center text-success">{{ $pertemuan->hadir ?? 0 }}</td>
                                <td class="text-center text-info">{{ $pertemuan->izin ?? 0 }}</td>
                                <td class="text-center text-warning">{{ $pertemuan->sakit ?? 0 }}</td>
                                <td class="text-center text-danger">{{ $pertemuan->alpha ?? 0 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">Belum ada data pertemuan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('kaprodi.absensi.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>
@endsection
