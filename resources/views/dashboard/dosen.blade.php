@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
<div class="page-title">
    <h4>Dashboard Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Overview</li>
        </ol>
    </nav>
</div>

@if($dosen)
<!-- Profil Singkat -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="user-avatar bg-primary" style="width: 64px; height: 64px; font-size: 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff;">
                    {{ strtoupper(substr($dosen->nama, 0, 1)) }}
                </div>
            </div>
            <div class="col">
                <h5 class="mb-1">{{ $dosen->nama }}</h5>
                <p class="text-muted mb-0">
                    <span class="me-3"><i class="bi bi-credit-card me-1"></i>NIDN: {{ $dosen->nidn }}</span>
                    <span><i class="bi bi-building me-1"></i>{{ $dosen->programStudi->nama ?? '-' }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card bg-primary position-relative">
            <i class="bi bi-calendar-week stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $jadwalMengajar->count() ?? 0 }}</div>
            <div class="stat-label opacity-75">Jadwal Mengajar</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card bg-success position-relative">
            <i class="bi bi-people stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $mahasiswaWali ?? 0 }}</div>
            <div class="stat-label opacity-75">Mahasiswa Perwalian</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card bg-info position-relative">
            <i class="bi bi-mortarboard stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $tahunAkademikAktif->nama_lengkap ?? '-' }}</div>
            <div class="stat-label opacity-75">Tahun Akademik Aktif</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Jadwal Mengajar Hari Ini -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-calendar-week me-2"></i>Jadwal Mengajar Semester Ini
            </div>
            <div class="card-body">
                @if(isset($jadwalMengajar) && $jadwalMengajar->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Mata Kuliah</th>
                                <th>Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalMengajar->sortBy('hari') as $jadwal)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $jadwal->hari }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                                <td>
                                    <strong>{{ $jadwal->mataKuliah->nama }}</strong>
                                    <br><small class="text-muted">Kelas {{ $jadwal->kelas }}</small>
                                </td>
                                <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0 py-4">Tidak ada jadwal mengajar</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-lightning me-2"></i>Menu Cepat
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('nilai.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-clipboard-data me-2"></i>Input Nilai
                    </a>
                    <a href="{{ route('krs.persetujuan') }}" class="btn btn-outline-success text-start">
                        <i class="bi bi-check2-square me-2"></i>Persetujuan KRS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pengumuman -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-megaphone me-2"></i>Pengumuman Terbaru</span>
                <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($pengumuman->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($pengumuman as $p)
                    <a href="{{ route('pengumuman.show', $p) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $p->judul }}</h6>
                                <small class="text-muted">{{ $p->tanggal_mulai->format('d M Y') }}</small>
                            </div>
                            <span class="badge bg-secondary">{{ $p->kategori }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada pengumuman</p>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>Data dosen tidak ditemukan. Silakan hubungi administrator.
</div>
@endif
@endsection
