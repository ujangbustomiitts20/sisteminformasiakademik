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
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-primary position-relative">
            <i class="bi bi-calendar-week stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $jadwalMengajar->count() ?? 0 }}</div>
            <div class="stat-label opacity-75">Jadwal Mengajar</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-success position-relative">
            <i class="bi bi-people stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $mahasiswaWali ?? 0 }}</div>
            <div class="stat-label opacity-75">Mahasiswa Perwalian</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-warning position-relative">
            <i class="bi bi-journal-bookmark stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $bimbinganTA ?? 0 }}</div>
            <div class="stat-label opacity-75">Bimbingan TA</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-info position-relative">
            <i class="bi bi-mortarboard stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $totalMahasiswaDiampu ?? 0 }}</div>
            <div class="stat-label opacity-75">Mahasiswa Diampu</div>
        </div>
    </div>
</div>

<!-- Stats Row 2 - Nilai Pending -->
@if(($nilaiPending ?? 0) > 0)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div>
                <strong>{{ $nilaiPending }} nilai</strong> belum diinput untuk semester ini.
                <a href="{{ route('nilai.index') }}" class="alert-link ms-2">Input Nilai <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Alert Notifikasi Penting -->
@if(($krsMenunggu ?? 0) > 0 || ($jadwalBimbinganPending ?? 0) > 0 || ($bimbinganAkademikMenunggu ?? 0) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-bell-fill me-2"></i>Notifikasi Penting
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if(($krsMenunggu ?? 0) > 0)
                    <div class="col-md-4">
                        <a href="{{ route('krs.persetujuan') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-check2-square"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0 text-danger">{{ $krsMenunggu }}</h5>
                                    <small class="text-muted">KRS Menunggu Persetujuan</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                    @if(($jadwalBimbinganPending ?? 0) > 0)
                    <div class="col-md-4">
                        <a href="{{ route('dosen.tugas-akhir.jadwal-bimbingan') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0 text-primary">{{ $jadwalBimbinganPending }}</h5>
                                    <small class="text-muted">Jadwal Bimbingan TA</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                    @if(($bimbinganAkademikMenunggu ?? 0) > 0)
                    <div class="col-md-4">
                        <a href="{{ route('bimbingan.dosen') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-chat-dots"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0 text-info">{{ $bimbinganAkademikMenunggu }}</h5>
                                    <small class="text-muted">Bimbingan Akademik</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    <!-- Jadwal Mengajar Hari Ini -->
    <div class="col-lg-8">
        @if(isset($jadwalHariIni) && $jadwalHariIni->count() > 0)
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-calendar-check me-2"></i>Jadwal Mengajar Hari Ini ({{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }})
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Mata Kuliah</th>
                                <th>Ruangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalHariIni as $jadwal)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                                </td>
                                <td>
                                    <strong>{{ $jadwal->mataKuliah->nama }}</strong>
                                    <br><small class="text-muted">Kelas {{ $jadwal->kelas }}</small>
                                </td>
                                <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('absensi.kode', $jadwal) }}" class="btn btn-sm btn-success" title="Buka Absensi">
                                        <i class="bi bi-qr-code"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
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
                    <a href="{{ route('absensi.index') }}" class="btn btn-outline-success text-start">
                        <i class="bi bi-calendar-check me-2"></i>Kelola Absensi
                    </a>
                    <a href="{{ route('nilai.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-clipboard-data me-2"></i>Input Nilai
                    </a>
                    <a href="{{ route('krs.persetujuan') }}" class="btn btn-outline-warning text-start">
                        <i class="bi bi-check2-square me-2"></i>Persetujuan KRS
                    </a>
                    <a href="{{ route('dosen.rekap-absensi') }}" class="btn btn-outline-info text-start">
                        <i class="bi bi-bar-chart me-2"></i>Rekap Absensi
                    </a>
                    <a href="{{ route('dosen.rekap-nilai') }}" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Rekap Nilai
                    </a>
                    <a href="{{ route('dosen.mahasiswa-wali') }}" class="btn btn-outline-dark text-start">
                        <i class="bi bi-people me-2"></i>Mahasiswa Perwalian
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

<!-- Rekap Absensi & Progress TA -->
<div class="row mt-4 g-4">
    <!-- Rekap Absensi Ringkas -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart me-2"></i>Rekap Absensi per Mata Kuliah</span>
                <a href="{{ route('dosen.rekap-absensi') }}" class="btn btn-sm btn-outline-primary">Detail</a>
            </div>
            <div class="card-body">
                @if(isset($rekapAbsensi) && $rekapAbsensi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Mata Kuliah</th>
                                <th class="text-center">Pertemuan</th>
                                <th class="text-center">Mhs</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekapAbsensi as $rekap)
                            <tr>
                                <td>
                                    <strong>{{ Str::limit($rekap['mata_kuliah'], 20) }}</strong>
                                    <br><small class="text-muted">Kelas {{ $rekap['kelas'] }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $rekap['total_pertemuan'] }}/16</span>
                                </td>
                                <td class="text-center">{{ $rekap['total_mahasiswa'] }}</td>
                                <td>
                                    <a href="{{ route('absensi.show', $rekap['jadwal_id']) }}" class="btn btn-sm btn-outline-secondary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0 py-3">Belum ada data absensi</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Progress Mahasiswa TA -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-bookmark me-2"></i>Progress Bimbingan TA</span>
                <a href="{{ route('dosen.tugas-akhir.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if(isset($progressTA) && $progressTA->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($progressTA as $ta)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $ta->mahasiswa->nama ?? '-' }}</h6>
                                <small class="text-muted">{{ $ta->mahasiswa->nim ?? '-' }} | {{ $ta->mahasiswa->programStudi->nama ?? '-' }}</small>
                                <br><small class="text-primary">{{ Str::limit($ta->judul ?? 'Judul belum ditentukan', 50) }}</small>
                            </div>
                            <div class="text-end">
                                @php
                                    $statusColors = [
                                        'Pengajuan' => 'secondary',
                                        'Bimbingan' => 'primary',
                                        'Sidang Proposal' => 'info',
                                        'Revisi Proposal' => 'warning',
                                        'Penelitian' => 'primary',
                                        'Sidang Hasil' => 'info',
                                        'Revisi Hasil' => 'warning',
                                        'Sidang Akhir' => 'info',
                                        'Revisi Akhir' => 'warning',
                                        'Lulus' => 'success',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$ta->status] ?? 'secondary' }}">{{ $ta->status }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0 py-3">Tidak ada mahasiswa bimbingan TA</p>
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
