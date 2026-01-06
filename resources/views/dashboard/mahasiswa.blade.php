@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-title">
    <h4>Dashboard</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Overview</li>
        </ol>
    </nav>
</div>

@if($mahasiswa)
<!-- Profil Singkat -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                @if($mahasiswa->foto)
                <img src="{{ Storage::url($mahasiswa->foto) }}" alt="Foto" class="rounded-circle" style="width: 64px; height: 64px; object-fit: cover;">
                @else
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <span class="text-white fs-4">{{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}</span>
                </div>
                @endif
            </div>
            <div class="col">
                <h5 class="mb-1">Selamat datang, {{ explode(' ', $mahasiswa->nama)[0] }}!</h5>
                <p class="text-muted mb-0">
                    <span class="me-3"><i class="bi bi-credit-card me-1"></i>{{ $mahasiswa->nim }}</span>
                    <span class="me-3"><i class="bi bi-building me-1"></i>{{ $mahasiswa->programStudi->nama ?? '-' }}</span>
                    <span><i class="bi bi-calendar me-1"></i>Semester {{ $mahasiswa->semester_aktif ?? 1 }}</span>
                </p>
            </div>
            <div class="col-auto">
                <span class="badge bg-{{ $mahasiswa->status == 'Aktif' ? 'success' : 'secondary' }} fs-6">
                    {{ $mahasiswa->status }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-primary position-relative">
            <i class="bi bi-award stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ number_format($ipk ?? 0, 2) }}</div>
            <div class="stat-label opacity-75">IPK</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-success position-relative">
            <i class="bi bi-journal-check stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $totalSks ?? 0 }} SKS</div>
            <div class="stat-label opacity-75">SKS Lulus</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-{{ ($totalTagihan ?? 0) > 0 ? 'danger' : 'success' }} position-relative">
            <i class="bi bi-receipt stat-icon"></i>
            <div class="stat-value h5 mb-1">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label opacity-75">Tagihan</div>
            @if(($tagihanJatuhTempo ?? 0) > 0)
            <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">{{ $tagihanJatuhTempo }} jatuh tempo</span>
            @endif
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-info position-relative">
            <i class="bi bi-calendar3 stat-icon"></i>
            <div class="stat-value h3 mb-1">Semester {{ $mahasiswa->semester_aktif ?? 1 }}</div>
            <div class="stat-label opacity-75">{{ $tahunAkademikAktif->nama_lengkap ?? '-' }}</div>
        </div>
    </div>
</div>

<!-- Quick Links Dashboard -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <a href="{{ route('mahasiswa.dashboard.akademik') }}" class="card text-decoration-none h-100 border-primary">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-4 me-4">
                    <i class="bi bi-mortarboard text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="text-primary mb-1">Dashboard Akademik</h4>
                    <p class="text-muted mb-0">KRS, Jadwal, Kehadiran, Nilai, Dosen Wali</p>
                </div>
                <i class="bi bi-chevron-right ms-auto text-primary fs-3"></i>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('mahasiswa.dashboard.keuangan') }}" class="card text-decoration-none h-100 border-danger">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 p-4 me-4">
                    <i class="bi bi-wallet2 text-danger" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="text-danger mb-1">Dashboard Keuangan</h4>
                    <p class="text-muted mb-0">Tagihan, Pembayaran, Cicilan, Potongan</p>
                </div>
                <i class="bi bi-chevron-right ms-auto text-danger fs-3"></i>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Jadwal Hari Ini -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-day me-2"></i>Jadwal Hari Ini - {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                <a href="{{ route('jadwal.mahasiswa') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if(isset($jadwalHariIni) && $jadwalHariIni->count() > 0)
                <div class="row">
                    @foreach($jadwalHariIni as $krs)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start p-3 rounded bg-light">
                            <div class="text-center me-3" style="min-width: 60px;">
                                <div class="fw-bold text-primary fs-5">{{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_mulai)->format('H:i') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_selesai)->format('H:i') }}</small>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $krs->jadwalKuliah->mataKuliah->nama ?? '-' }}</h6>
                                <small class="text-muted d-block">
                                    <i class="bi bi-person me-1"></i>{{ $krs->jadwalKuliah->dosen->nama ?? '-' }}
                                </small>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $krs->jadwalKuliah->ruangan->nama ?? '-' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-emoji-smile text-success" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-0 mt-2">Tidak ada jadwal kuliah hari ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Event Mendatang -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-calendar-event me-2"></i>Event Mendatang
            </div>
            <div class="card-body p-0">
                @if(isset($eventMendatang) && $eventMendatang->count() > 0)
                @foreach($eventMendatang as $event)
                @php
                    $colorMap = ['akademik' => 'primary', 'libur' => 'danger', 'ujian' => 'warning', 'pendaftaran' => 'success', 'lainnya' => 'secondary'];
                    $color = $colorMap[$event->jenis] ?? 'secondary';
                    $diffDays = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($event->tanggal_mulai)->startOfDay(), false);
                @endphp
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-{{ $color }} mb-1">{{ ucfirst($event->jenis) }}</span>
                            <h6 class="mb-1">{{ $event->nama }}</h6>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('d M') }}</small>
                        </div>
                        <span class="badge bg-{{ $diffDays <= 3 ? 'danger' : 'secondary' }}">
                            @if($diffDays == 0) Hari ini
                            @elseif($diffDays == 1) Besok
                            @else {{ $diffDays }}h
                            @endif
                        </span>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-4">
                    <i class="bi bi-calendar-check text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Tidak ada event mendatang</p>
                </div>
                @endif
            </div>
            <div class="card-footer bg-light">
                <a href="{{ route('kalender.index') }}" class="text-decoration-none">
                    <small>Lihat Kalender <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Pengumuman -->
@if($pengumuman->count() > 0)
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-megaphone me-2"></i>Pengumuman Terbaru</span>
        <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @foreach($pengumuman as $p)
            <a href="{{ route('pengumuman.show', $p) }}" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">{{ $p->judul }}</h6>
                        <small class="text-muted">{{ $p->tanggal_mulai->format('d M Y') }}</small>
                    </div>
                    <span class="badge bg-{{ $p->kategori == 'Akademik' ? 'primary' : ($p->kategori == 'Keuangan' ? 'success' : 'secondary') }}">
                        {{ $p->kategori }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data mahasiswa tidak ditemukan. Silakan hubungi administrator.
</div>
@endif
@endsection
