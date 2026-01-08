@extends('portal-pmb.layouts.main')

@section('title', 'Jalur Seleksi')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Jalur Seleksi</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Jalur Seleksi</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Pilih jalur pendaftaran yang sesuai dengan kemampuanmu</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if($jalurSeleksi->count() > 0)
        <div class="row g-4">
            @foreach($jalurSeleksi as $index => $jalur)
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="card card-pmb h-100">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), #764ba2);">
                                <span class="text-white fw-bold fs-4">{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-1">{{ $jalur->nama }}</h4>
                                @if($jalur->total_kuota)
                                <span class="badge bg-primary bg-opacity-10 text-primary">Kuota: {{ $jalur->total_kuota }} Peserta</span>
                                @endif
                            </div>
                        </div>
                        
                        <p class="text-muted mb-4">{{ $jalur->deskripsi ?? 'Jalur seleksi untuk calon mahasiswa baru.' }}</p>
                        
                        <!-- Persyaratan -->
                        @if($jalur->persyaratan)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-list-check text-primary me-2"></i>Persyaratan</h6>
                            <ul class="list-unstyled mb-0">
                                @foreach(explode("\n", $jalur->persyaratan) as $syarat)
                                @if(trim($syarat))
                                <li class="d-flex align-items-start mb-2">
                                    <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                    <span>{{ trim($syarat) }}</span>
                                </li>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <!-- Info Tambahan -->
                        <div class="bg-light rounded-3 p-3 mb-4">
                            <div class="row g-3">
                                @if($jalur->biaya_pendaftaran_aktif)
                                <div class="col-6">
                                    <small class="text-muted d-block">Biaya Pendaftaran</small>
                                    <span class="fw-bold text-primary">Rp {{ number_format($jalur->biaya_pendaftaran_aktif, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                @if($jalur->tanggal_mulai_daftar && $jalur->tanggal_selesai_daftar)
                                <div class="col-6">
                                    <small class="text-muted d-block">Periode Pendaftaran</small>
                                    <span class="fw-bold">{{ $jalur->tanggal_mulai_daftar->format('d M') }} - {{ $jalur->tanggal_selesai_daftar->format('d M Y') }}</span>
                                </div>
                                @endif
                                @if($jalur->total_kuota)
                                <div class="col-6">
                                    <small class="text-muted d-block">Kuota Tersedia</small>
                                    <span class="fw-bold text-success">{{ $jalur->total_kuota }} Peserta</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-primary w-100">
                            <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <img src="https://illustrations.popsy.co/gray/list-is-empty.svg" alt="No Data" class="mb-4" style="max-width: 200px;">
            <h4 class="fw-bold text-muted">Belum Ada Jalur Seleksi</h4>
            <p class="text-muted">Saat ini belum ada data jalur seleksi yang tersedia.</p>
        </div>
        @endif
    </div>
</section>

<!-- Perbandingan Jalur -->
@if($jalurSeleksi->count() > 1)
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title" data-aos="fade-up">Perbandingan Jalur Seleksi</h2>
            <p class="text-muted" data-aos="fade-up" data-aos-delay="100">Bandingkan berbagai jalur seleksi untuk menemukan yang paling sesuai</p>
        </div>
        
        <div class="table-responsive" data-aos="fade-up" data-aos-delay="200">
            <table class="table table-hover bg-white rounded-4 overflow-hidden shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th class="py-3 px-4">Jalur Seleksi</th>
                        <th class="py-3 px-4 text-center">Kuota</th>
                        <th class="py-3 px-4 text-center">Biaya</th>
                        <th class="py-3 px-4 text-center">Periode</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jalurSeleksi as $jalur)
                    <tr>
                        <td class="py-3 px-4">
                            <h6 class="fw-bold mb-0">{{ $jalur->nama }}</h6>
                        </td>
                        <td class="py-3 px-4 text-center">{{ $jalur->total_kuota ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($jalur->biaya_pendaftaran_aktif)
                            Rp {{ number_format($jalur->biaya_pendaftaran_aktif, 0, ',', '.') }}
                            @else
                            -
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($jalur->tanggal_mulai_daftar && $jalur->tanggal_selesai_daftar)
                            {{ $jalur->tanggal_mulai_daftar->format('d M') }} - {{ $jalur->tanggal_selesai_daftar->format('d M') }}
                            @else
                            -
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-sm btn-primary">Daftar</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endif

<!-- CTA -->
<section class="cta-section">
    <div class="container position-relative text-center text-white">
        <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">Masih Bingung Pilih Jalur?</h2>
        <p class="fs-5 opacity-75 mb-4" data-aos="fade-up" data-aos-delay="100">Hubungi tim kami untuk mendapatkan konsultasi gratis!</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap" data-aos="fade-up" data-aos-delay="200">
            <a href="{{ route('portal-pmb.kontak') }}" class="btn btn-light btn-lg px-4">
                <i class="bi bi-chat-dots me-2"></i>Hubungi Kami
            </a>
            <a href="{{ route('portal-pmb.faq') }}" class="btn btn-outline-light btn-lg px-4">
                <i class="bi bi-question-circle me-2"></i>Lihat FAQ
            </a>
        </div>
    </div>
</section>
@endsection
