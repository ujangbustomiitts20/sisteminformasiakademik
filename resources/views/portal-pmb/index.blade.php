@extends('portal-pmb.layouts.main')

@section('title', 'Penerimaan Mahasiswa Baru')

@section('content')
<!-- Hero Section with Slider -->
<section class="hero-section" id="hero">
    @if($sliders->count() > 0)
    <div id="heroCarousel" class="carousel slide w-100 h-100" data-bs-ride="carousel">
        <div class="carousel-inner h-100">
            @foreach($sliders as $index => $slider)
            <div class="carousel-item h-100 {{ $index == 0 ? 'active' : '' }}">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);"></div>
                @if($slider->gambar)
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('{{ $slider->gambar_url }}') center/cover; opacity: 0.2;"></div>
                @endif
                <div class="container h-100 position-relative">
                    <div class="row h-100 align-items-center">
                        <div class="col-lg-8">
                            <h1 class="hero-title" data-aos="fade-up">{{ $slider->judul ?? ($konten['hero_title'] ?? 'Raih Masa Depanmu Bersama Kami') }}</h1>
                            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">{{ $slider->deskripsi ?? ($konten['hero_subtitle'] ?? 'Bergabunglah dengan ribuan mahasiswa yang telah mewujudkan mimpi mereka') }}</p>
                            <div class="mt-4" data-aos="fade-up" data-aos-delay="200">
                                @if($slider->link)
                                <a href="{{ $slider->link }}" class="btn btn-light btn-lg px-4 me-2">
                                    <i class="bi bi-arrow-right-circle me-2"></i>{{ $slider->button_text ?? 'Selengkapnya' }}
                                </a>
                                @endif
                                <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-outline-light btn-lg px-4">
                                    <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($sliders->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        @endif
    </div>
    @else
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="hero-title" data-aos="fade-up">{{ $konten['hero_title'] ?? 'Raih Masa Depanmu Bersama Kami' }}</h1>
                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">{{ $konten['hero_subtitle'] ?? 'Bergabunglah dengan ribuan mahasiswa yang telah mewujudkan mimpi mereka di universitas kami' }}</p>
                <div class="mt-4" data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-light btn-lg px-4 me-2">
                        <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                    </a>
                    <a href="{{ route('portal-pmb.program-studi') }}" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-book me-2"></i>Lihat Program Studi
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Info Box Gelombang Aktif -->
    @if($gelombangAktif)
    <div class="position-absolute bottom-0 start-0 end-0 pb-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="bg-white rounded-4 shadow-lg p-4" data-aos="fade-up">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                                        <i class="bi bi-calendar-check text-success fs-3"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $gelombangAktif->nama }} Sedang Dibuka!</h5>
                                        <p class="text-muted mb-0">Pendaftaran sampai {{ $gelombangAktif->tanggal_selesai_daftar->format('d F Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-primary btn-lg">
                                    Daftar Sekarang <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</section>

<!-- Stats Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-lg-3" data-aos="fade-up">
                <div class="stat-box">
                    <div class="stat-number">{{ $statistik['total_prodi'] ?? '0' }}</div>
                    <div class="stat-label">Program Studi</div>
                </div>
            </div>
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-box">
                    <div class="stat-number">{{ number_format($statistik['total_mahasiswa'] ?? 0) }}+</div>
                    <div class="stat-label">Mahasiswa Aktif</div>
                </div>
            </div>
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-box">
                    <div class="stat-number">{{ $statistik['total_dosen'] ?? '0' }}+</div>
                    <div class="stat-label">Dosen Berkualitas</div>
                </div>
            </div>
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-box">
                    <div class="stat-number">{{ date('Y') - ($statistik['tahun_berdiri'] ?? 1990) }}+</div>
                    <div class="stat-label">Tahun Pengalaman</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
@if($keunggulan->count() > 0)
<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title" data-aos="fade-up">Mengapa Memilih Kami?</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Keunggulan yang menjadi alasan mahasiswa memilih kampus kami</p>
        </div>
        
        <div class="row g-4">
            @foreach($keunggulan as $index => $item)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="bi bi-{{ $item->icon ?? 'star-fill' }}"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ $item->judul }}</h5>
                    <p class="text-muted mb-0">{{ $item->deskripsi }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Program Studi -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title" data-aos="fade-up">Program Studi</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Pilih program studi yang sesuai dengan minat dan bakatmu</p>
        </div>
        
        <div class="row g-4">
            @foreach($programStudi->take(6) as $index => $prodi)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="prodi-card h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-book text-primary fs-4"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary mb-1">{{ $prodi->jenjang }}</span>
                            <h5 class="mb-0 fw-bold">{{ $prodi->nama }}</h5>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">{{ $prodi->fakultas->nama ?? '' }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small"><i class="bi bi-clock me-1"></i>{{ $prodi->total_sks ?? 144 }} SKS</span>
                        <a href="{{ route('portal-pmb.program-studi.detail', $prodi->id) }}" class="btn btn-sm btn-outline-primary">
                            Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($programStudi->count() > 6)
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="{{ route('portal-pmb.program-studi') }}" class="btn btn-primary btn-lg">
                Lihat Semua Program Studi <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Jalur Seleksi -->
@if($jalurSeleksi->count() > 0)
<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title" data-aos="fade-up">Jalur Pendaftaran</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Pilih jalur pendaftaran yang sesuai dengan kemampuanmu</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            @foreach($jalurSeleksi as $index => $jalur)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="card card-pmb h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color), #764ba2);">
                                <span class="text-white fw-bold">{{ $index + 1 }}</span>
                            </div>
                            <h5 class="mb-0 fw-bold">{{ $jalur->nama }}</h5>
                        </div>
                        <p class="text-muted">{{ Str::limit($jalur->deskripsi, 100) }}</p>
                        <a href="{{ route('portal-pmb.jalur-seleksi') }}" class="text-primary fw-semibold text-decoration-none">
                            Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Testimoni -->
@if($testimoni->count() > 0)
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title" data-aos="fade-up">Apa Kata Mereka?</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Testimoni dari mahasiswa dan alumni kami</p>
        </div>
        
        <div class="row g-4">
            @foreach($testimoni as $index => $item)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="card card-pmb h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="bi bi-quote text-primary fs-1 opacity-25"></i>
                        </div>
                        <p class="text-muted mb-4">{{ Str::limit($item->testimoni, 150) }}</p>
                        <div class="d-flex align-items-center">
                            <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $item->nama }}</h6>
                                <small class="text-muted">{{ $item->program_studi ?? '' }} {{ $item->angkatan ? "'".$item->angkatan : '' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Fasilitas -->
@if($fasilitas->count() > 0)
<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title" data-aos="fade-up">Fasilitas Kampus</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Fasilitas modern untuk mendukung proses belajar mengajar</p>
        </div>
        
        <div class="row g-4">
            @foreach($fasilitas as $index => $item)
            <div class="col-6 col-md-4 col-lg-2" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                <div class="text-center">
                    <div class="bg-light rounded-4 p-4 mb-3 mx-auto" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                        @if($item->gambar)
                            <img src="{{ $item->gambar_url }}" alt="{{ $item->nama }}" class="img-fluid" style="max-height: 60px;">
                        @else
                            <i class="bi bi-{{ $item->icon ?? 'building' }} fs-1 text-primary"></i>
                        @endif
                    </div>
                    <h6 class="fw-semibold mb-0">{{ $item->nama }}</h6>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="{{ route('portal-pmb.fasilitas') }}" class="btn btn-outline-primary">
                Lihat Semua Fasilitas <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Berita Terbaru -->
@if($beritaTerbaru->count() > 0)
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="section-title mb-1" data-aos="fade-up">Berita & Pengumuman</h2>
                <p class="text-muted" data-aos="fade-up" data-aos-delay="100">Informasi terbaru seputar PMB dan kampus</p>
            </div>
            <a href="{{ route('portal-pmb.berita') }}" class="btn btn-outline-primary d-none d-md-inline-flex" data-aos="fade-up">
                Lihat Semua <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="row g-4">
            @foreach($beritaTerbaru as $index => $berita)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="card card-pmb h-100">
                    <div class="position-relative">
                        <img src="{{ $berita->gambar_url }}" class="card-img-top" alt="{{ $berita->judul }}" style="height: 200px; object-fit: cover;">
                        <span class="badge bg-primary position-absolute top-0 end-0 m-3">{{ ucfirst($berita->kategori) }}</span>
                    </div>
                    <div class="card-body p-4">
                        <small class="text-muted"><i class="bi bi-calendar me-1"></i>{{ $berita->published_at->format('d M Y') }}</small>
                        <h5 class="card-title fw-bold mt-2">{{ Str::limit($berita->judul, 60) }}</h5>
                        <p class="card-text text-muted">{{ $berita->ringkasan_text }}</p>
                        <a href="{{ route('portal-pmb.berita.detail', $berita->slug) }}" class="text-primary fw-semibold text-decoration-none">
                            Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4 d-md-none">
            <a href="{{ route('portal-pmb.berita') }}" class="btn btn-outline-primary">
                Lihat Semua Berita <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- FAQ -->
@if($faqPopuler->count() > 0)
<section class="py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <h2 class="section-title" data-aos="fade-up">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-muted" data-aos="fade-up" data-aos-delay="100">Temukan jawaban dari pertanyaan yang paling sering diajukan oleh calon mahasiswa</p>
                <a href="{{ route('portal-pmb.faq') }}" class="btn btn-primary mt-3" data-aos="fade-up" data-aos-delay="200">
                    Lihat Semua FAQ <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-7">
                <div class="accordion" id="faqAccordion" data-aos="fade-up" data-aos-delay="100">
                    @foreach($faqPopuler as $index => $faq)
                    <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">
                                {{ $faq->pertanyaan }}
                            </button>
                        </h2>
                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                {!! nl2br(e($faq->jawaban)) !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="cta-section">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8 text-center text-lg-start text-white" data-aos="fade-up">
                <h2 class="display-5 fw-bold mb-3">Siap Bergabung Bersama Kami?</h2>
                <p class="fs-5 opacity-75 mb-4">Wujudkan impianmu menjadi mahasiswa di kampus kami. Pendaftaran mudah, cepat, dan online!</p>
            </div>
            <div class="col-lg-4 text-center text-lg-end" data-aos="fade-up" data-aos-delay="100">
                <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-light btn-lg px-5 py-3">
                    <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .hero-section {
        padding-top: 80px;
    }
    
    @media (min-width: 992px) {
        .hero-section {
            padding-bottom: 120px;
        }
    }
</style>
@endpush
