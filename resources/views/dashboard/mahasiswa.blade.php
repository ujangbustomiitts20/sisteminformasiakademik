@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="page-title">
    <h4>Dashboard Mahasiswa</h4>
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
                <div class="user-avatar bg-primary" style="width: 64px; height: 64px; font-size: 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff;">
                    {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
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

<!-- Jadwal Hari Ini & Event Mendatang -->
<div class="row g-4 mb-4">
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
    
    <!-- Event Akademik Mendatang -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-calendar-event me-2"></i>Event Mendatang
            </div>
            <div class="card-body p-0">
                @if(isset($eventMendatang) && $eventMendatang->count() > 0)
                @foreach($eventMendatang as $event)
                @php
                    $colorMap = [
                        'akademik' => 'primary',
                        'libur' => 'danger',
                        'ujian' => 'warning',
                        'pendaftaran' => 'success',
                        'lainnya' => 'secondary'
                    ];
                    $color = $colorMap[$event->jenis] ?? 'secondary';
                    $diffDays = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($event->tanggal_mulai)->startOfDay(), false);
                @endphp
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-{{ $color }} mb-1">{{ ucfirst($event->jenis) }}</span>
                            <h6 class="mb-1">{{ $event->nama }}</h6>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('d M') }}
                                @if($event->tanggal_selesai && $event->tanggal_selesai != $event->tanggal_mulai)
                                 - {{ \Carbon\Carbon::parse($event->tanggal_selesai)->format('d M') }}
                                @endif
                            </small>
                        </div>
                        <span class="badge bg-{{ $diffDays <= 3 ? 'danger' : 'secondary' }}">
                            @if($diffDays == 0) Hari ini
                            @elseif($diffDays == 1) Besok
                            @else {{ $diffDays }}h lagi
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
                <a href="{{ route('kalender-akademik.index') }}" class="text-decoration-none">
                    <small>Lihat Kalender Akademik <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Akademik -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-primary position-relative">
            <i class="bi bi-award stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ number_format($ipk ?? 0, 2) }}</div>
            <div class="stat-label opacity-75">IPK Kumulatif</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-success position-relative">
            <i class="bi bi-journal-check stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $totalSks ?? 0 }} <small class="fs-6">/ {{ $targetSks ?? 144 }}</small></div>
            <div class="stat-label opacity-75">SKS Lulus</div>
            <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.3);">
                <div class="progress-bar bg-white" style="width: {{ $progressSks ?? 0 }}%"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-info position-relative">
            <i class="bi bi-book stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $sksSemesterIni ?? 0 }} SKS</div>
            <div class="stat-label opacity-75">Semester Ini</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-{{ ($persentaseKehadiran ?? 100) >= 80 ? 'success' : 'danger' }} position-relative">
            <i class="bi bi-clipboard-check stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ $persentaseKehadiran ?? 100 }}%</div>
            <div class="stat-label opacity-75">Kehadiran</div>
        </div>
    </div>
</div>

<!-- Stats Keuangan -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="bi bi-receipt text-danger fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Tagihan</h6>
                        <h4 class="mb-0 text-danger">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</h4>
                        @if(($tagihanJatuhTempo ?? 0) > 0)
                        <small class="text-danger"><i class="bi bi-exclamation-circle"></i> {{ $tagihanJatuhTempo }} jatuh tempo</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-cash-stack text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Dibayar Tahun Ini</h6>
                        <h4 class="mb-0 text-success">Rp {{ number_format($totalDibayarTahunIni ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="bi bi-tags text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Potongan Aktif</h6>
                        <h4 class="mb-0 text-primary">{{ ($potonganAktif ?? collect())->count() }}</h4>
                        <small class="text-muted">potongan tersedia</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="bi bi-list-check text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Cicilan Aktif</h6>
                        <h4 class="mb-0 text-warning">{{ ($cicilanAktif ?? collect())->count() }}</h4>
                        <small class="text-muted">cicilan berjalan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Tagihan Belum Lunas -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>Tagihan Belum Lunas</span>
                <a href="{{ route('tagihan.mahasiswa') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if(isset($tagihanBelumLunas) && $tagihanBelumLunas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Tagihan</th>
                                <th>Periode</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Sisa Tagihan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tagihanBelumLunas->take(5) as $tagihan)
                            <tr>
                                <td>
                                    <strong>{{ $tagihan->jenis_tagihan }}</strong>
                                    <br><small class="text-muted">{{ $tagihan->no_tagihan }}</small>
                                </td>
                                <td>{{ $tagihan->tahunAkademik->nama_lengkap ?? '-' }}</td>
                                <td>
                                    @if($tagihan->tanggal_jatuh_tempo)
                                        @php $jatuhTempo = \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo); @endphp
                                        @if($jatuhTempo->isPast())
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            {{ $jatuhTempo->format('d/m/Y') }}
                                        </span>
                                        @elseif($jatuhTempo->diffInDays(now()) <= 7)
                                        <span class="badge bg-warning text-dark">
                                            {{ $jatuhTempo->format('d/m/Y') }}
                                        </span>
                                        @else
                                        <span class="text-muted">{{ $jatuhTempo->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pembayaran.bayar', $tagihan) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-credit-card me-1"></i>Bayar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-0 mt-2">Tidak ada tagihan yang belum lunas</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sidebar Kanan -->
    <div class="col-lg-4">
        <!-- Promo Tersedia -->
        @if(isset($promoTersedia) && $promoTersedia->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="bi bi-gift me-2"></i>Promo Tersedia
            </div>
            <div class="card-body p-0">
                @foreach($promoTersedia as $promo)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $promo->nama }}</h6>
                            <span class="badge bg-success">
                                @if($promo->tipe_nilai == 'persen')
                                Diskon {{ $promo->nilai }}%
                                @else
                                Potongan Rp {{ number_format($promo->nilai, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        <small class="text-muted">s.d {{ $promo->tanggal_selesai->format('d/m') }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Potongan Saya -->
        @if(isset($potonganAktif) && $potonganAktif->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-tags me-2"></i>Potongan Saya
            </div>
            <div class="card-body p-0">
                @foreach($potonganAktif->take(3) as $potongan)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{ $potongan->jenisPotongan->nama ?? 'Potongan' }}</h6>
                            <small class="text-muted">{{ $potongan->alasan ?? '-' }}</small>
                        </div>
                        <span class="badge bg-primary">
                            @if($potongan->tipe_nilai == 'persen')
                            {{ $potongan->nilai }}%
                            @else
                            Rp {{ number_format($potongan->nilai, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="card-footer bg-light">
                <a href="{{ route('mahasiswa.potongan') }}" class="text-decoration-none">
                    <small>Lihat semua potongan <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
        </div>
        @endif
        
        <!-- Quick Links Keuangan -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-lightning me-2"></i>Menu Keuangan
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('tagihan.mahasiswa') }}" class="btn btn-outline-danger text-start">
                        <i class="bi bi-receipt me-2"></i>Tagihan Saya
                        @if(($totalTagihan ?? 0) > 0)
                        <span class="badge bg-danger float-end">{{ ($tagihanBelumLunas ?? collect())->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('transaksi.mahasiswa') }}" class="btn btn-outline-success text-start">
                        <i class="bi bi-cash-stack me-2"></i>Riwayat Pembayaran
                    </a>
                    <a href="{{ route('cicilan.tracking') }}" class="btn btn-outline-warning text-start">
                        <i class="bi bi-list-check me-2"></i>Cicilan Saya
                        @if(($cicilanAktif ?? collect())->count() > 0)
                        <span class="badge bg-warning text-dark float-end">{{ $cicilanAktif->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('beasiswa.available') }}" class="btn btn-outline-info text-start">
                        <i class="bi bi-award me-2"></i>Beasiswa
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Links Akademik -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-mortarboard me-2"></i>Menu Akademik
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('krs.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-journal-text me-2"></i>KRS
                    </a>
                    <a href="{{ route('mahasiswa.khs') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-file-earmark-text me-2"></i>KHS
                    </a>
                    <a href="{{ route('mahasiswa.transkrip') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-file-earmark-ruled me-2"></i>Transkrip
                    </a>
                    <a href="{{ route('mahasiswa.jadwal-ujian') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-calendar-event me-2"></i>Jadwal Ujian
                    </a>
                    <a href="{{ route('jadwal.mahasiswa') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-calendar-week me-2"></i>Jadwal Kuliah
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal & Transaksi -->
<div class="row g-4 mt-2">
    <!-- Jadwal Kuliah Semester Ini -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-week me-2"></i>Jadwal Kuliah Semester Ini</span>
                <span class="badge bg-primary">{{ $tahunAkademikAktif->nama_lengkap ?? '-' }}</span>
            </div>
            <div class="card-body">
                @if(isset($krsSemesterIni) && $krsSemesterIni->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($krsSemesterIni->sortBy(fn($k) => array_search($k->jadwalKuliah->hari ?? '', ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'])) as $krs)
                            @if($krs->jadwalKuliah)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $krs->jadwalKuliah->hari }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($krs->jadwalKuliah->jam_selesai)->format('H:i') }}</td>
                                <td>
                                    <strong>{{ $krs->jadwalKuliah->mataKuliah->nama ?? '-' }}</strong>
                                    <br><small class="text-muted">{{ $krs->jadwalKuliah->mataKuliah->sks ?? 0 }} SKS</small>
                                </td>
                                <td>{{ $krs->jadwalKuliah->dosen->nama ?? '-' }}</td>
                                <td>{{ $krs->jadwalKuliah->ruangan->nama ?? '-' }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-0 mt-2">Belum ada KRS yang disetujui</p>
                    @if(isset($tahunAkademikAktif) && $tahunAkademikAktif && method_exists($tahunAkademikAktif, 'isPeriodeKrs') && $tahunAkademikAktif->isPeriodeKrs())
                    <a href="{{ route('krs.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-lg me-1"></i>Isi KRS Sekarang
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Transaksi Terbaru -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Transaksi Terbaru
            </div>
            <div class="card-body p-0">
                @if(isset($transaksiTerbaru) && $transaksiTerbaru->count() > 0)
                @foreach($transaksiTerbaru as $trx)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $trx->tagihan->jenis_tagihan ?? 'Pembayaran' }}</h6>
                            <small class="text-muted">{{ $trx->tanggal_bayar->format('d M Y') }}</small>
                        </div>
                        <span class="text-success fw-bold">
                            +Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Belum ada transaksi</p>
                </div>
                @endif
            </div>
            @if(isset($transaksiTerbaru) && $transaksiTerbaru->count() > 0)
            <div class="card-footer bg-light">
                <a href="{{ route('transaksi.mahasiswa') }}" class="text-decoration-none">
                    <small>Lihat semua transaksi <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
            @endif
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
                            <span class="badge bg-{{ $p->kategori == 'Akademik' ? 'primary' : ($p->kategori == 'Keuangan' ? 'success' : 'secondary') }}">
                                {{ $p->kategori }}
                            </span>
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
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data mahasiswa tidak ditemukan. Silakan hubungi administrator.
</div>
@endif
@endsection
