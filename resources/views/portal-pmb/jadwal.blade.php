@extends('portal-pmb.layouts.main')

@section('title', 'Jadwal PMB')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Jadwal</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Jadwal PMB</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Jadwal lengkap penerimaan mahasiswa baru</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Periode Aktif -->
        @if($periodeAktif)
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-5" data-aos="fade-up">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                    <i class="bi bi-calendar-check text-success fs-3"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Periode PMB {{ $periodeAktif->nama }} Sedang Berlangsung!</h5>
                    <p class="mb-0">Tahun Akademik {{ $periodeAktif->tahun_akademik ?? date('Y').'/'.(date('Y')+1) }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Gelombang Pendaftaran -->
        <h3 class="fw-bold mb-4" data-aos="fade-up"><i class="bi bi-layers text-primary me-2"></i>Gelombang Pendaftaran</h3>
        
        @if($gelombang->count() > 0)
        <div class="row g-4 mb-5">
            @foreach($gelombang as $index => $gel)
            @php
                $isActive = $gel->tanggal_mulai_daftar <= now() && $gel->tanggal_selesai_daftar >= now();
                $isPast = $gel->tanggal_selesai_daftar < now();
                $isFuture = $gel->tanggal_mulai_daftar > now();
            @endphp
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="card card-pmb h-100 {{ $isActive ? 'border-success' : '' }}">
                    @if($isActive)
                    <div class="card-header bg-success text-white border-0 py-2">
                        <small><i class="bi bi-broadcast me-1"></i>Sedang Berlangsung</small>
                    </div>
                    @elseif($isPast)
                    <div class="card-header bg-secondary text-white border-0 py-2">
                        <small><i class="bi bi-check-circle me-1"></i>Selesai</small>
                    </div>
                    @else
                    <div class="card-header bg-primary text-white border-0 py-2">
                        <small><i class="bi bi-clock me-1"></i>Akan Datang</small>
                    </div>
                    @endif
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">{{ $gel->nama }}</h4>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted"><i class="bi bi-calendar-event me-1"></i>Pendaftaran</span>
                            </div>
                            <div class="bg-light rounded-3 p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-center">
                                        <div class="fw-bold">{{ $gel->tanggal_mulai_daftar->format('d') }}</div>
                                        <small class="text-muted">{{ $gel->tanggal_mulai_daftar->format('M Y') }}</small>
                                    </div>
                                    <i class="bi bi-arrow-right text-muted"></i>
                                    <div class="text-center">
                                        <div class="fw-bold">{{ $gel->tanggal_selesai_daftar->format('d') }}</div>
                                        <small class="text-muted">{{ $gel->tanggal_selesai_daftar->format('M Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($gel->tanggal_ujian)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><i class="bi bi-pencil-square me-1"></i>Ujian Seleksi</span>
                            <span class="fw-bold">{{ $gel->tanggal_ujian->format('d M Y') }}</span>
                        </div>
                        @endif
                        
                        @if($gel->tanggal_pengumuman)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><i class="bi bi-megaphone me-1"></i>Pengumuman</span>
                            <span class="fw-bold">{{ $gel->tanggal_pengumuman->format('d M Y') }}</span>
                        </div>
                        @endif
                        
                        @if($gel->tanggal_daftar_ulang)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-person-check me-1"></i>Daftar Ulang</span>
                            <span class="fw-bold">{{ $gel->tanggal_daftar_ulang->format('d M Y') }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        @if($isActive)
                        <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-success w-100">
                            <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                        </a>
                        @elseif($isPast)
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="bi bi-x-circle me-2"></i>Pendaftaran Ditutup
                        </button>
                        @else
                        <button class="btn btn-outline-primary w-100" disabled>
                            <i class="bi bi-clock me-2"></i>Belum Dibuka
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5 mb-5">
            <img src="https://illustrations.popsy.co/gray/calendar.svg" alt="No Schedule" class="mb-4" style="max-width: 200px;">
            <h4 class="fw-bold text-muted">Belum Ada Jadwal</h4>
            <p class="text-muted">Jadwal gelombang pendaftaran belum tersedia.</p>
        </div>
        @endif
        
        <!-- Timeline -->
        <h3 class="fw-bold mb-4" data-aos="fade-up"><i class="bi bi-signpost-2 text-primary me-2"></i>Alur Pendaftaran</h3>
        <div class="row mb-5">
            <div class="col-lg-10 mx-auto">
                <div class="timeline-horizontal" data-aos="fade-up" data-aos-delay="100">
                    <div class="timeline-step">
                        <div class="timeline-icon bg-primary">
                            <i class="bi bi-person-plus text-white"></i>
                        </div>
                        <h6 class="fw-bold mt-3">1. Registrasi</h6>
                        <small class="text-muted">Buat akun dan isi formulir pendaftaran online</small>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon bg-primary">
                            <i class="bi bi-file-earmark-arrow-up text-white"></i>
                        </div>
                        <h6 class="fw-bold mt-3">2. Upload Dokumen</h6>
                        <small class="text-muted">Lengkapi berkas persyaratan</small>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon bg-primary">
                            <i class="bi bi-credit-card text-white"></i>
                        </div>
                        <h6 class="fw-bold mt-3">3. Pembayaran</h6>
                        <small class="text-muted">Bayar biaya pendaftaran</small>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon bg-primary">
                            <i class="bi bi-card-checklist text-white"></i>
                        </div>
                        <h6 class="fw-bold mt-3">4. Verifikasi</h6>
                        <small class="text-muted">Tunggu verifikasi dari panitia</small>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon bg-primary">
                            <i class="bi bi-pencil text-white"></i>
                        </div>
                        <h6 class="fw-bold mt-3">5. Ujian Seleksi</h6>
                        <small class="text-muted">Ikuti ujian sesuai jadwal</small>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon bg-success">
                            <i class="bi bi-trophy text-white"></i>
                        </div>
                        <h6 class="fw-bold mt-3">6. Pengumuman</h6>
                        <small class="text-muted">Cek hasil kelulusan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container position-relative text-center text-white">
        <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">Jangan Lewatkan Kesempatan!</h2>
        <p class="fs-5 opacity-75 mb-4" data-aos="fade-up" data-aos-delay="100">Daftarkan dirimu sebelum kuota penuh</p>
        <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-light btn-lg px-5" data-aos="fade-up" data-aos-delay="200">
            <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
        </a>
    </div>
</section>
@endsection

@push('styles')
<style>
    .timeline-horizontal {
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 20px 0;
    }
    .timeline-horizontal::before {
        content: '';
        position: absolute;
        top: 35px;
        left: 50px;
        right: 50px;
        height: 3px;
        background: #e9ecef;
    }
    .timeline-step {
        text-align: center;
        flex: 1;
        position: relative;
        padding: 0 10px;
    }
    .timeline-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }
    
    @media (max-width: 768px) {
        .timeline-horizontal {
            flex-direction: column;
            align-items: flex-start;
        }
        .timeline-horizontal::before {
            top: 0;
            bottom: 0;
            left: 24px;
            width: 3px;
            height: auto;
        }
        .timeline-step {
            display: flex;
            align-items: flex-start;
            text-align: left;
            margin-bottom: 20px;
            padding-left: 70px;
        }
        .timeline-icon {
            position: absolute;
            left: 0;
        }
    }
</style>
@endpush
