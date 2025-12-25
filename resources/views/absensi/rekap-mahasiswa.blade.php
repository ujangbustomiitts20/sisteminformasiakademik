@extends('layouts.app')

@section('title', 'Rekap Kehadiran')

@section('content')
<div class="page-title">
    <h4>Rekap Kehadiran Saya</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Rekap Kehadiran</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Mahasiswa</h6>
                        <h4 class="mb-0">{{ $mahasiswa->nama }}</h4>
                        <small>{{ $mahasiswa->nim }}</small>
                    </div>
                    <i class="bi bi-person-circle display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Semester</h6>
                        <h4 class="mb-0">{{ $tahunAkademikAktif?->nama_lengkap ?? '-' }}</h4>
                        <small>Mata Kuliah: {{ $krs->count() }}</small>
                    </div>
                    <i class="bi bi-calendar3 display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-clipboard-check me-2"></i>Rekap Kehadiran Per Mata Kuliah
    </div>
    <div class="card-body">
        @if($krs->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Anda belum mengambil mata kuliah di semester ini.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th class="text-center" width="60">SKS</th>
                        <th class="text-center" width="60">Hadir</th>
                        <th class="text-center" width="60">Izin</th>
                        <th class="text-center" width="60">Sakit</th>
                        <th class="text-center" width="60">Alpha</th>
                        <th class="text-center" width="100">Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($krs as $index => $k)
                    @php
                        $hadir = $k->absensi->where('status', 'Hadir')->count();
                        $izin = $k->absensi->where('status', 'Izin')->count();
                        $sakit = $k->absensi->where('status', 'Sakit')->count();
                        $alpha = $k->absensi->where('status', 'Alpha')->count();
                        $total = $k->absensi->count();
                        $persen = $total > 0 ? round((($hadir + $izin + $sakit) / $total) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $k->jadwalKuliah->mataKuliah->kode }}</code></td>
                        <td>{{ $k->jadwalKuliah->mataKuliah->nama }}</td>
                        <td class="text-center">{{ $k->jadwalKuliah->mataKuliah->sks }}</td>
                        <td class="text-center"><span class="badge bg-success">{{ $hadir }}</span></td>
                        <td class="text-center"><span class="badge bg-info">{{ $izin }}</span></td>
                        <td class="text-center"><span class="badge bg-warning">{{ $sakit }}</span></td>
                        <td class="text-center"><span class="badge bg-danger">{{ $alpha }}</span></td>
                        <td class="text-center">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $persen >= 75 ? 'bg-success' : ($persen >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $persen }}%">
                                    {{ $persen }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Catatan:</strong> Kehadiran minimal 75% untuk dapat mengikuti UAS. Status Izin dan Sakit dihitung sebagai kehadiran.
        </div>
        @endif
    </div>
</div>
@endsection
