@extends('portal-pmb.layouts.main')

@section('title', 'Berita & Pengumuman')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Berita & Pengumuman</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Berita & Pengumuman</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Informasi terbaru seputar PMB dan kampus</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Filter Kategori -->
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('portal-pmb.berita') }}" class="btn {{ !request('kategori') ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
                    <a href="{{ route('portal-pmb.berita', ['kategori' => 'berita']) }}" class="btn {{ request('kategori') == 'berita' ? 'btn-primary' : 'btn-outline-primary' }}">Berita</a>
                    <a href="{{ route('portal-pmb.berita', ['kategori' => 'pengumuman']) }}" class="btn {{ request('kategori') == 'pengumuman' ? 'btn-primary' : 'btn-outline-primary' }}">Pengumuman</a>
                    <a href="{{ route('portal-pmb.berita', ['kategori' => 'tips']) }}" class="btn {{ request('kategori') == 'tips' ? 'btn-primary' : 'btn-outline-primary' }}">Tips & Trik</a>
                </div>
            </div>
        </div>
        
        @if($berita->count() > 0)
        <div class="row g-4">
            @foreach($berita as $index => $item)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}">
                <div class="card card-pmb h-100">
                    <div class="position-relative">
                        <img src="{{ $item->gambar_url }}" class="card-img-top" alt="{{ $item->judul }}" style="height: 200px; object-fit: cover;">
                        <span class="badge bg-primary position-absolute top-0 end-0 m-3">{{ ucfirst($item->kategori) }}</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <small class="text-muted"><i class="bi bi-calendar me-1"></i>{{ $item->published_at->format('d M Y') }}</small>
                            <small class="text-muted"><i class="bi bi-eye me-1"></i>{{ number_format($item->views ?? 0) }}</small>
                        </div>
                        <h5 class="card-title fw-bold">{{ Str::limit($item->judul, 60) }}</h5>
                        <p class="card-text text-muted">{{ $item->ringkasan_text }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="{{ route('portal-pmb.berita.detail', $item->slug) }}" class="btn btn-outline-primary w-100">
                            Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
            {{ $berita->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <img src="https://illustrations.popsy.co/gray/news.svg" alt="No News" class="mb-4" style="max-width: 200px;">
            <h4 class="fw-bold text-muted">Belum Ada Berita</h4>
            <p class="text-muted">Saat ini belum ada berita atau pengumuman yang dipublikasikan.</p>
        </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
    .pagination {
        gap: 0.5rem;
    }
    .page-link {
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
    }
</style>
@endpush
