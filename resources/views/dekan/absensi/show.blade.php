@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
<div class="page-title">
    <h4>Detail Absensi Perkuliahan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.absensi.index') }}">Absensi</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Info Mata Kuliah -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Mata Kuliah</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Kode</strong></td>
                        <td>: {{ $jadwalKuliah->mataKuliah->kode ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama MK</strong></td>
                        <td>: {{ $jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>SKS</strong></td>
                        <td>: {{ $jadwalKuliah->mataKuliah->sks ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Prodi</strong></td>
                        <td>: {{ $jadwalKuliah->mataKuliah->programStudi->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Info Jadwal -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Jadwal</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Kelas</strong></td>
                        <td>: {{ $jadwalKuliah->kelas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Hari</strong></td>
                        <td>: {{ $jadwalKuliah->hari ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jam</strong></td>
                        <td>: {{ $jadwalKuliah->jam_mulai ?? '-' }} - {{ $jadwalKuliah->jam_selesai ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ruangan</strong></td>
                        <td>: {{ $jadwalKuliah->ruangan->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Dosen</strong></td>
                        <td>: {{ $jadwalKuliah->dosen->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Statistik -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Statistik</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h3 class="text-primary mb-0">{{ $totalPertemuan }}</h3>
                        <small class="text-muted">Total Pertemuan</small>
                    </div>
                    <div class="col-6">
                        <h3 class="text-success mb-0">{{ $totalMahasiswa }}</h3>
                        <small class="text-muted">Mahasiswa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Daftar Pertemuan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Daftar Pertemuan</h6>
            </div>
            <div class="card-body">
                @if($jadwalKuliah->pertemuan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pertemuan</th>
                                <th>Tanggal</th>
                                <th>Materi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalKuliah->pertemuan as $pertemuan)
                            <tr>
                                <td>{{ $pertemuan->pertemuan_ke }}</td>
                                <td>{{ $pertemuan->tanggal ? \Carbon\Carbon::parse($pertemuan->tanggal)->format('d/m/Y') : '-' }}</td>
                                <td>{{ Str::limit($pertemuan->materi ?? '-', 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ $pertemuan->status == 'selesai' ? 'success' : 'warning' }}">
                                        {{ ucfirst($pertemuan->status ?? 'pending') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                    <p class="mt-2">Belum ada data pertemuan</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Daftar Mahasiswa -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Daftar Mahasiswa Terdaftar</h6>
            </div>
            <div class="card-body">
                @if($jadwalKuliah->krs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalKuliah->krs as $index => $krs)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $krs->mahasiswa->nim ?? '-' }}</td>
                                <td>{{ $krs->mahasiswa->nama ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-people" style="font-size: 3rem;"></i>
                    <p class="mt-2">Belum ada mahasiswa terdaftar</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('dekan.absensi.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
@endsection
