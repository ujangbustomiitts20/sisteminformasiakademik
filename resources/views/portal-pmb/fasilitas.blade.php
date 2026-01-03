@extends('portal-pmb.layouts.main')

@section('title', 'Fasilitas Kampus')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Fasilitas</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Fasilitas Kampus</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Fasilitas modern untuk mendukung proses belajar mengajar</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if($fasilitas->count() > 0)
        <div class="row g-4">
            @foreach($fasilitas as $index => $item)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}">
                <div class="card card-pmb h-100">
                    @if($item->gambar)
                    <div class="position-relative">
                        <img src="{{ $item->gambar_url }}" class="card-img-top" alt="{{ $item->nama }}" style="height: 200px; object-fit: cover;">
                    </div>
                    @endif
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                <i class="bi bi-{{ $item->icon ?? 'building' }} text-primary fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-0">{{ $item->nama }}</h5>
                        </div>
                        <p class="text-muted mb-0">{{ $item->deskripsi }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <img src="https://illustrations.popsy.co/gray/working-vacation.svg" alt="No Facilities" class="mb-4" style="max-width: 200px;">
            <h4 class="fw-bold text-muted">Belum Ada Data Fasilitas</h4>
            <p class="text-muted">Saat ini belum ada data fasilitas yang tersedia.</p>
        </div>
        @endif
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container position-relative text-center text-white">
        <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">Ingin Melihat Langsung?</h2>
        <p class="fs-5 opacity-75 mb-4" data-aos="fade-up" data-aos-delay="100">Kunjungi kampus kami dan rasakan sendiri suasana belajar yang nyaman!</p>
        <a href="{{ route('portal-pmb.kontak') }}" class="btn btn-light btn-lg px-5" data-aos="fade-up" data-aos-delay="200">
            <i class="bi bi-geo-alt me-2"></i>Kunjungi Kampus
        </a>
    </div>
</section>
@endsection
