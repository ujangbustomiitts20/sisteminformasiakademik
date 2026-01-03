@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@push('styles')
<style>
    .stat-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    .stat-card .stat-icon {
        opacity: 0.2;
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }
    .stat-card .stat-label {
        font-size: 0.8rem;
        opacity: 0.9;
    }
    .quick-action-btn {
        border-radius: 8px;
        padding: 12px 16px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .quick-action-btn:hover {
        transform: translateX(3px);
    }
    .attention-item {
        transition: background-color 0.2s ease;
    }
    .attention-item:hover {
        background-color: #f8f9fa;
    }
    .profile-avatar {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .card {
        border-radius: 12px;
        border: none;
    }
    .card-header {
        border-radius: 12px 12px 0 0 !important;
        border-bottom: 1px solid #eee;
    }
    .table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
        border-color: #0dcaf0 !important;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Dashboard Dosen</h4>
        <p class="text-muted mb-0">
            Selamat datang, <strong>{{ $dosen->gelar_depan }} {{ $dosen->nama }}{{ $dosen->gelar_belakang ? ', ' . $dosen->gelar_belakang : '' }}</strong>
        </p>
    </div>
    <div class="text-end">
        <span class="badge bg-primary fs-6 px-3 py-2">
            <i class="bi bi-calendar3 me-1"></i>{{ $tahunAkademik?->nama ?? 'Belum ada periode aktif' }}
        </span>
    </div>
</div>

<!-- Statistik Utama -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card bg-primary text-white shadow h-100 position-relative">
            <div class="card-body p-3">
                <div class="stat-icon"><i class="bi bi-book"></i></div>
                <div class="d-flex flex-column">
                    <span class="stat-value">{{ $totalMataKuliah }}</span>
                    <span class="stat-label mt-1">Mata Kuliah Diampu</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card bg-success text-white shadow h-100 position-relative">
            <div class="card-body p-3">
                <div class="stat-icon"><i class="bi bi-clock"></i></div>
                <div class="d-flex flex-column">
                    <span class="stat-value">{{ $totalSks }}</span>
                    <span class="stat-label mt-1">Total SKS</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card bg-info text-white shadow h-100 position-relative">
            <div class="card-body p-3">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="d-flex flex-column">
                    <span class="stat-value">{{ $totalMahasiswaDiampu }}</span>
                    <span class="stat-label mt-1">Mahasiswa Diampu</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card bg-warning text-dark shadow h-100 position-relative">
            <div class="card-body p-3">
                <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                <div class="d-flex flex-column">
                    <span class="stat-value">{{ $mahasiswaWali }}</span>
                    <span class="stat-label mt-1">Mahasiswa Perwalian</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri - Konten Utama -->
    <div class="col-lg-8">
        <!-- Jadwal Hari Ini -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-calendar-day text-primary me-2"></i>
                    <strong>Jadwal Hari Ini</strong>
                    <small class="text-muted ms-2">({{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }})</small>
                </div>
                <a href="{{ route('jadwal.dosen') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-calendar-week me-1"></i>Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                @if($jadwalHariIni->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="130">Waktu</th>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>Ruangan</th>
                                <th width="80" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalHariIni->take(5) as $jadwal)
                            <tr>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                        <i class="bi bi-clock me-1"></i>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $jadwal->mataKuliah->nama ?? '-' }}</div>
                                    <small class="text-muted">{{ $jadwal->mataKuliah->kode ?? '' }} • {{ $jadwal->mataKuliah->sks ?? 0 }} SKS</small>
                                </td>
                                <td><span class="badge bg-secondary">{{ $jadwal->kelas ?? '-' }}</span></td>
                                <td>
                                    <i class="bi bi-geo-alt text-muted me-1"></i>{{ $jadwal->ruangan->nama ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('absensi.show', $jadwal) }}" class="btn btn-sm btn-success" title="Input Absensi">
                                        <i class="bi bi-clipboard-check"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($jadwalHariIni->count() > 5)
                <div class="text-center py-2 border-top">
                    <a href="{{ route('jadwal.dosen') }}" class="btn btn-sm btn-link text-decoration-none">
                        Lihat {{ $jadwalHariIni->count() - 5 }} jadwal lainnya <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-5">
                    <div class="text-muted mb-2">
                        <i class="bi bi-calendar-x" style="font-size: 3.5rem; opacity: 0.5;"></i>
                    </div>
                    <p class="text-muted mb-0">Tidak ada jadwal mengajar hari ini</p>
                    <small class="text-muted">Nikmati waktu istirahat Anda</small>
                </div>
                @endif
            </div>
        </div>

        <!-- Daftar Mata Kuliah Diampu -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-journal-text text-primary me-2"></i>
                <strong>Mata Kuliah yang Diampu</strong>
            </div>
            <div class="card-body p-0">
                @if($rekapAbsensi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>Jadwal</th>
                                <th class="text-center">Mhs</th>
                                <th class="text-center">Pertemuan</th>
                                <th width="130" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekapAbsensi->take(5) as $rekap)
                            <tr>
                                <td><code class="bg-light px-2 py-1 rounded">{{ $rekap['kode'] }}</code></td>
                                <td class="fw-semibold">{{ $rekap['mata_kuliah'] }}</td>
                                <td><span class="badge bg-secondary">{{ $rekap['kelas'] }}</span></td>
                                <td>
                                    <div><small><i class="bi bi-calendar3 me-1"></i>{{ $rekap['hari'] }}, {{ $rekap['jam'] }}</small></div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $rekap['ruangan'] }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info">{{ $rekap['total_mahasiswa'] }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $progress = ($rekap['total_pertemuan'] / 16) * 100;
                                        $progressClass = $progress < 50 ? 'bg-danger' : ($progress < 80 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="progress" style="width: 50px; height: 6px;">
                                            <div class="progress-bar {{ $progressClass }}" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $rekap['total_pertemuan'] }}/16</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('absensi.show', $rekap['jadwal']) }}" class="btn btn-outline-info" title="Absensi">
                                            <i class="bi bi-clipboard-check"></i>
                                        </a>
                                        <a href="{{ route('nilai.input', $rekap['jadwal']) }}" class="btn btn-outline-success" title="Nilai">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="{{ route('pertemuan.index', $rekap['jadwal']) }}" class="btn btn-outline-primary" title="Pertemuan">
                                            <i class="bi bi-collection"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($rekapAbsensi->count() > 5)
                <div class="text-center py-2 border-top">
                    <a href="{{ route('jadwal.dosen') }}" class="btn btn-sm btn-link text-decoration-none">
                        Lihat {{ $rekapAbsensi->count() - 5 }} mata kuliah lainnya <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-5">
                    <div class="text-muted mb-2">
                        <i class="bi bi-journal-x" style="font-size: 3.5rem; opacity: 0.5;"></i>
                    </div>
                    <p class="text-muted mb-0">Belum ada jadwal mengajar</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Progress Bimbingan TA -->
        @if($progressTA->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-mortarboard text-primary me-2"></i>
                    <strong>Progress Bimbingan Tugas Akhir</strong>
                </div>
                <a href="{{ route('dosen.tugas-akhir.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Program Studi</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($progressTA->take(3) as $ta)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $ta->mahasiswa->nama ?? '-' }}</div>
                                    <small class="text-muted">{{ $ta->mahasiswa->nim ?? '' }}</small>
                                </td>
                                <td>{{ $ta->mahasiswa->programStudi->nama ?? '-' }}</td>
                                <td class="text-center">
                                    @php
                                        $statusColors = [
                                            'Pengajuan' => 'secondary',
                                            'Bimbingan' => 'info',
                                            'Sidang Proposal' => 'primary',
                                            'Revisi Proposal' => 'warning',
                                            'Penelitian' => 'info',
                                            'Sidang Hasil' => 'primary',
                                            'Revisi Hasil' => 'warning',
                                            'Sidang Akhir' => 'primary',
                                            'Revisi Akhir' => 'warning',
                                            'Lulus' => 'success',
                                            'Gagal' => 'danger',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$ta->status] ?? 'secondary' }}">{{ $ta->status }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($progressTA->count() > 3)
                <div class="text-center py-2 border-top">
                    <a href="{{ route('dosen.tugas-akhir.index') }}" class="btn btn-sm btn-link text-decoration-none">
                        Lihat {{ $progressTA->count() - 3 }} bimbingan lainnya <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Kolom Kanan - Sidebar -->
    <div class="col-lg-4">
        <!-- Profil Singkat -->
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center py-4">
                @if($dosen->foto)
                <img src="{{ Storage::url($dosen->foto) }}" alt="{{ $dosen->nama }}" class="rounded-circle profile-avatar mb-3">
                @else
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center profile-avatar mb-3" style="font-size: 2rem;">
                    {{ strtoupper(substr($dosen->nama, 0, 1)) }}
                </div>
                @endif
                <h6 class="fw-bold mb-1">{{ $dosen->gelar_depan }} {{ $dosen->nama }}{{ $dosen->gelar_belakang ? ', ' . $dosen->gelar_belakang : '' }}</h6>
                <p class="text-muted small mb-2">
                    <i class="bi bi-credit-card-2-front me-1"></i>NIDN: {{ $dosen->nidn ?? '-' }}
                </p>
                <div class="d-flex justify-content-center gap-1 flex-wrap">
                    @if($dosen->jabatan_fungsional)
                    <span class="badge bg-primary-subtle text-primary">{{ $dosen->jabatan_fungsional }}</span>
                    @endif
                    @if($dosen->golongan)
                    <span class="badge bg-success-subtle text-success">{{ $dosen->golongan }}</span>
                    @endif
                </div>
                <hr class="my-3">
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('dosen.profil') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye me-1"></i>Profil
                    </a>
                    <a href="{{ route('dosen.profil.edit') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifikasi/Perlu Perhatian -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-bell text-warning me-2"></i>
                <strong>Perlu Perhatian</strong>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @if($krsMenunggu > 0)
                    <a href="{{ route('bimbingan.persetujuan-krs') }}" class="list-group-item list-group-item-action attention-item d-flex justify-content-between align-items-center py-3">
                        <span><i class="bi bi-file-earmark-check text-warning me-2"></i>KRS Menunggu Persetujuan</span>
                        <span class="badge bg-warning text-dark rounded-pill">{{ $krsMenunggu }}</span>
                    </a>
                    @endif
                    @if($nilaiPending > 0)
                    <a href="{{ route('nilai.index') }}" class="list-group-item list-group-item-action attention-item d-flex justify-content-between align-items-center py-3">
                        <span><i class="bi bi-pencil-square text-danger me-2"></i>Nilai Belum Diinput</span>
                        <span class="badge bg-danger rounded-pill">{{ $nilaiPending }}</span>
                    </a>
                    @endif
                    @if($bimbinganAkademik > 0)
                    <a href="{{ route('bimbingan.dosen') }}" class="list-group-item list-group-item-action attention-item d-flex justify-content-between align-items-center py-3">
                        <span><i class="bi bi-chat-left-text text-info me-2"></i>Bimbingan Belum Direspon</span>
                        <span class="badge bg-info rounded-pill">{{ $bimbinganAkademik }}</span>
                    </a>
                    @endif
                    @if($jadwalBimbinganPending > 0)
                    <a href="{{ route('dosen.tugas-akhir.jadwal-bimbingan') }}" class="list-group-item list-group-item-action attention-item d-flex justify-content-between align-items-center py-3">
                        <span><i class="bi bi-calendar-check text-primary me-2"></i>Jadwal Bimbingan TA</span>
                        <span class="badge bg-primary rounded-pill">{{ $jadwalBimbinganPending }}</span>
                    </a>
                    @endif
                    @if($bimbinganTA > 0)
                    <a href="{{ route('dosen.tugas-akhir.index') }}" class="list-group-item list-group-item-action attention-item d-flex justify-content-between align-items-center py-3">
                        <span><i class="bi bi-mortarboard text-success me-2"></i>Bimbingan TA Aktif</span>
                        <span class="badge bg-success rounded-pill">{{ $bimbinganTA }}</span>
                    </a>
                    @endif
                    @if($krsMenunggu == 0 && $nilaiPending == 0 && $bimbinganAkademik == 0 && $jadwalBimbinganPending == 0 && $bimbinganTA == 0)
                    <div class="list-group-item text-center py-4">
                        <i class="bi bi-check-circle text-success fs-3 mb-2 d-block"></i>
                        <span class="text-muted">Tidak ada tugas mendesak</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-lightning-charge text-warning me-2"></i>
                <strong>Aksi Cepat</strong>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('nilai.index') }}" class="btn btn-outline-primary quick-action-btn text-start">
                        <i class="bi bi-pencil-square me-2"></i>Input Nilai
                    </a>
                    <a href="{{ route('absensi.index') }}" class="btn btn-outline-success quick-action-btn text-start">
                        <i class="bi bi-clipboard-check me-2"></i>Input Absensi
                    </a>
                    <a href="{{ route('bimbingan.dosen') }}" class="btn btn-outline-info quick-action-btn text-start">
                        <i class="bi bi-chat-dots me-2"></i>Bimbingan Akademik
                    </a>
                    <a href="{{ route('dosen.tugas-akhir.index') }}" class="btn btn-outline-warning quick-action-btn text-start">
                        <i class="bi bi-file-earmark-text me-2"></i>Bimbingan TA
                    </a>
                    <hr class="my-2">
                    <a href="{{ route('dosen.kepegawaian') }}" class="btn btn-outline-secondary quick-action-btn text-start">
                        <i class="bi bi-folder me-2"></i>Data Kepegawaian
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pengumuman - Full Width -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-megaphone text-info me-2"></i>
                    <strong>Pengumuman Terbaru</strong>
                </div>
                <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-outline-info">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                @if($pengumuman->count() > 0)
                <div class="row g-3">
                    @foreach($pengumuman->take(3) as $item)
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route('pengumuman.show', $item) }}" class="text-decoration-none">
                            <div class="card h-100 border hover-shadow" style="transition: all 0.2s ease;">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="bg-info-subtle text-info rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="bi bi-megaphone-fill fs-5"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $item->judul }}
                                            </h6>
                                            <div class="d-flex align-items-center gap-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>{{ $item->created_at->format('d M Y') }}
                                                </small>
                                                @if($item->created_at->isToday())
                                                <span class="badge bg-success-subtle text-success" style="font-size: 0.65rem;">Baru</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ strip_tags($item->isi) }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <div class="text-muted mb-2">
                        <i class="bi bi-megaphone" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                    <p class="text-muted mb-0">Belum ada pengumuman</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
