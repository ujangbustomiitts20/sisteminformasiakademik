@extends('layouts.app')

@section('title', 'Dashboard Akademik')

@section('content')
<div class="page-title">
    <h4>Dashboard Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Akademik</li>
        </ol>
    </nav>
</div>

@if($mahasiswa)

{{-- Alert jika tidak ada KRS di semester aktif --}}
@if(isset($hasKrsInActiveTa) && !$hasKrsInActiveTa && isset($tahunAkademikAktif))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Perhatian!</strong> Anda belum memiliki KRS di semester {{ $tahunAkademikAktif->nama_lengkap ?? 'aktif' }}.
    @if(isset($displayTahunAkademik) && $displayTahunAkademik->id !== $tahunAkademikAktif->id)
    Data di bawah menampilkan semester terakhir: <strong>{{ $displayTahunAkademik->nama_lengkap }}</strong>.
    @endif
    <a href="{{ route('krs.create') }}" class="btn btn-sm btn-warning ms-2">Isi KRS Sekarang</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Alert kehadiran di bawah 80% --}}
@if(($persentaseKehadiran ?? 100) < 80)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-octagon me-2"></i>
    <strong>Peringatan!</strong> Kehadiran Anda hanya <strong>{{ $persentaseKehadiran }}%</strong> (minimal 80%). 
    Segera perbaiki kehadiran agar tidak terkena sanksi akademik.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Row 1: Jadwal Hari Ini + Dosen Wali (Paling Penting) -->
<div class="row g-4 mb-4">
    <!-- Jadwal Hari Ini -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-day me-2"></i>Jadwal Hari Ini - {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                <a href="{{ route('jadwal.mahasiswa') }}" class="btn btn-sm btn-light">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if(isset($jadwalHariIni) && $jadwalHariIni->count() > 0)
                <div class="row">
                    @foreach($jadwalHariIni as $krs)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start p-3 rounded border border-primary border-opacity-25 bg-primary bg-opacity-10">
                            <div class="text-center me-3" style="min-width: 65px;">
                                <div class="fw-bold text-primary fs-4">{{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_mulai)->format('H:i') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_selesai)->format('H:i') }}</small>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-semibold">{{ $krs->jadwalKuliah->mataKuliah->nama ?? '-' }}</h6>
                                <small class="text-muted d-block">
                                    <i class="bi bi-person me-1"></i>{{ $krs->jadwalKuliah->dosen->nama ?? '-' }}
                                </small>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $krs->jadwalKuliah->ruangan->nama ?? '-' }}
                                    <span class="badge bg-info ms-2">{{ $krs->jadwalKuliah->mataKuliah->sks ?? 0 }} SKS</span>
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-emoji-smile text-success" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">Tidak ada jadwal kuliah hari ini</h5>
                    <p class="text-muted mb-0">Gunakan waktu untuk belajar mandiri atau istirahat</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Dosen Wali -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <i class="bi bi-person-badge me-2"></i>Dosen Wali
            </div>
            <div class="card-body d-flex flex-column">
                @if($mahasiswa->dosenWali)
                <div class="text-center mb-3">
                    @if($mahasiswa->dosenWali->foto)
                    <img src="{{ Storage::url($mahasiswa->dosenWali->foto) }}" alt="Foto" class="rounded-circle mb-2 border border-3 border-success" style="width: 90px; height: 90px; object-fit: cover;">
                    @else
                    <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-2" style="width: 90px; height: 90px;">
                        <span class="text-white fs-2">{{ strtoupper(substr($mahasiswa->dosenWali->nama, 0, 1)) }}</span>
                    </div>
                    @endif
                    <h6 class="mb-1 fw-semibold">{{ $mahasiswa->dosenWali->nama }}</h6>
                    <span class="badge bg-secondary">{{ $mahasiswa->dosenWali->nidn ?? '-' }}</span>
                </div>
                <div class="mt-auto">
                    @if($mahasiswa->dosenWali->email)
                    <div class="d-flex align-items-center mb-2 small">
                        <i class="bi bi-envelope text-success me-2"></i>
                        <span class="text-truncate">{{ $mahasiswa->dosenWali->email }}</span>
                    </div>
                    @endif
                    @if($mahasiswa->dosenWali->no_hp)
                    <div class="d-flex align-items-center small">
                        <i class="bi bi-phone text-success me-2"></i>
                        <span>{{ $mahasiswa->dosenWali->no_hp }}</span>
                    </div>
                    @endif
                    <a href="{{ route('bimbingan.mahasiswa') }}" class="btn btn-success btn-sm w-100 mt-3">
                        <i class="bi bi-chat-dots me-1"></i>Ajukan Bimbingan
                    </a>
                </div>
                @else
                <div class="text-center py-4 my-auto">
                    <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-0 mt-2">Dosen wali belum ditentukan</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card bg-primary position-relative">
            <i class="bi bi-award stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ number_format($ipk ?? 0, 2) }}</div>
            <div class="stat-label opacity-75">IPK Kumulatif</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card bg-success position-relative">
            <i class="bi bi-journal-check stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $totalSks ?? 0 }} <small class="fs-6">/ {{ $targetSks ?? 144 }}</small></div>
            <div class="stat-label opacity-75">SKS Lulus</div>
            <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.3);">
                <div class="progress-bar bg-white" style="width: {{ $progressSks ?? 0 }}%"></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card bg-info position-relative">
            <i class="bi bi-book stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $sksSemesterIni ?? 0 }} SKS</div>
            <div class="stat-label opacity-75">Semester Ini</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card bg-{{ ($persentaseKehadiran ?? 100) >= 80 ? 'success' : 'danger' }} position-relative">
            <i class="bi bi-clipboard-check stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $persentaseKehadiran ?? 100 }}%</div>
            <div class="stat-label opacity-75">Kehadiran</div>
        </div>
    </div>
</div>

<!-- Row 3: Rekap Kehadiran + Event Mendatang -->
<div class="row g-4 mb-4">
    <!-- Rekap Kehadiran -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard-data me-2"></i>Rekap Kehadiran {{ $displayTahunAkademik->nama_lengkap ?? '' }}</span>
                <a href="{{ route('mahasiswa.kehadiran') }}" class="btn btn-sm btn-outline-primary">Detail</a>
            </div>
            <div class="card-body">
                <div class="row text-center g-2">
                    <div class="col">
                        <div class="p-3 rounded bg-success bg-opacity-10 h-100">
                            <h2 class="text-success mb-0 fw-bold">{{ $rekapKehadiran['hadir'] ?? 0 }}</h2>
                            <small class="text-muted">Hadir</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 rounded bg-info bg-opacity-10 h-100">
                            <h2 class="text-info mb-0 fw-bold">{{ $rekapKehadiran['izin'] ?? 0 }}</h2>
                            <small class="text-muted">Izin</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 rounded bg-warning bg-opacity-10 h-100">
                            <h2 class="text-warning mb-0 fw-bold">{{ $rekapKehadiran['sakit'] ?? 0 }}</h2>
                            <small class="text-muted">Sakit</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 rounded bg-danger bg-opacity-10 h-100">
                            <h2 class="text-danger mb-0 fw-bold">{{ $rekapKehadiran['alpha'] ?? 0 }}</h2>
                            <small class="text-muted">Alpha</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 rounded bg-secondary bg-opacity-10 h-100">
                            <h2 class="text-secondary mb-0 fw-bold">{{ $rekapKehadiran['total'] ?? 0 }}</h2>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Event Akademik Mendatang -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-calendar-event me-2"></i>Event Mendatang
            </div>
            <div class="card-body p-0">
                @if(isset($eventMendatang) && $eventMendatang->count() > 0)
                @foreach($eventMendatang->take(4) as $event)
                @php
                    $colorMap = ['akademik' => 'primary', 'libur' => 'danger', 'ujian' => 'warning', 'pendaftaran' => 'success', 'lainnya' => 'secondary'];
                    $color = $colorMap[$event->jenis] ?? 'secondary';
                    $diffDays = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($event->tanggal_mulai)->startOfDay(), false);
                @endphp
                <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <div class="me-2">
                        <span class="badge bg-{{ $color }} me-1">{{ ucfirst($event->jenis) }}</span>
                        <span class="small fw-semibold">{{ Str::limit($event->nama, 25) }}</span>
                        <div class="small text-muted">{{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('d M') }}</div>
                    </div>
                    <span class="badge bg-{{ $diffDays <= 3 ? 'danger' : 'light text-dark' }}">
                        @if($diffDays == 0) Hari ini
                        @elseif($diffDays == 1) Besok
                        @else {{ $diffDays }}h
                        @endif
                    </span>
                </div>
                @endforeach
                @else
                <div class="text-center py-4">
                    <i class="bi bi-calendar-check text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2 small">Tidak ada event mendatang</p>
                </div>
                @endif
            </div>
            <div class="card-footer bg-light py-2">
                <a href="{{ route('kalender.index') }}" class="text-decoration-none small">
                    Lihat Kalender Akademik <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Jadwal Kuliah Semester -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-week me-2"></i>Jadwal Kuliah {{ $displayTahunAkademik->nama_lengkap ?? '' }}</span>
        @if(isset($displayTahunAkademik) && isset($tahunAkademikAktif) && $displayTahunAkademik->id !== $tahunAkademikAktif->id)
        <span class="badge bg-warning text-dark">Data Semester Lalu</span>
        @else
        <span class="badge bg-primary">{{ $sksSemesterIni ?? 0 }} SKS</span>
        @endif
    </div>
    <div class="card-body p-0">
        @if(isset($krsSemesterIni) && $krsSemesterIni->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;">Hari</th>
                        <th style="width: 130px;">Jam</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th style="width: 120px;">Ruangan</th>
                        <th style="width: 60px;" class="text-center">SKS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
                        $sortedKrs = $krsSemesterIni->filter(fn($k) => $k->jadwalKuliah)->sortBy([
                            fn($a, $b) => ($hariOrder[$a->jadwalKuliah->hari] ?? 99) <=> ($hariOrder[$b->jadwalKuliah->hari] ?? 99),
                            fn($a, $b) => $a->jadwalKuliah->jam_mulai <=> $b->jadwalKuliah->jam_mulai
                        ]);
                        $currentHari = null;
                    @endphp
                    @foreach($sortedKrs as $krs)
                    @php
                        $isNewHari = $currentHari !== $krs->jadwalKuliah->hari;
                        $currentHari = $krs->jadwalKuliah->hari;
                        $hariColors = ['Senin' => 'primary', 'Selasa' => 'success', 'Rabu' => 'info', 'Kamis' => 'warning', 'Jumat' => 'danger', 'Sabtu' => 'secondary'];
                    @endphp
                    <tr @if($isNewHari) class="border-top-2" @endif>
                        <td>
                            @if($isNewHari)
                            <span class="badge bg-{{ $hariColors[$krs->jadwalKuliah->hari] ?? 'secondary' }}">{{ $krs->jadwalKuliah->hari }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_mulai)->format('H:i') }}</span>
                            <span class="text-muted">-</span>
                            <span>{{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_selesai)->format('H:i') }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $krs->jadwalKuliah->mataKuliah->nama ?? '-' }}</div>
                            <small class="text-muted">{{ $krs->jadwalKuliah->mataKuliah->kode ?? '-' }}</small>
                        </td>
                        <td>{{ $krs->jadwalKuliah->dosen->nama ?? '-' }}</td>
                        <td>{{ $krs->jadwalKuliah->ruangan->nama ?? '-' }}</td>
                        <td class="text-center"><span class="badge bg-info">{{ $krs->jadwalKuliah->mataKuliah->sks ?? 0 }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mb-0 mt-2">Belum ada KRS yang disetujui</p>
            <a href="{{ route('krs.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-lg me-1"></i>Isi KRS Sekarang
            </a>
        </div>
        @endif
    </div>
</div>

@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data mahasiswa tidak ditemukan. Silakan hubungi administrator.
</div>
@endif
@endsection
