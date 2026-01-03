@extends('portal-pmb.layouts.main')

@section('title', $berita->judul)

@section('content')
<!-- Page Header -->
<section class="page-header py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.berita') }}" class="text-white-50">Berita</a></li>
                        <li class="breadcrumb-item active text-white">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <article data-aos="fade-up">
                    <!-- Header -->
                    <div class="mb-4">
                        <span class="badge bg-primary mb-3">{{ ucfirst($berita->kategori) }}</span>
                        <h1 class="display-5 fw-bold mb-3">{{ $berita->judul }}</h1>
                        <div class="d-flex flex-wrap align-items-center gap-4 text-muted">
                            <span><i class="bi bi-calendar me-1"></i>{{ $berita->published_at->format('d F Y') }}</span>
                            <span><i class="bi bi-person me-1"></i>{{ $berita->penulis ?? 'Admin' }}</span>
                            <span><i class="bi bi-eye me-1"></i>{{ number_format($berita->views ?? 0) }} views</span>
                        </div>
                    </div>
                    
                    <!-- Featured Image -->
                    @if($berita->gambar)
                    <div class="mb-4">
                        <img src="{{ $berita->gambar_url }}" class="img-fluid rounded-4 w-100" alt="{{ $berita->judul }}" style="max-height: 400px; object-fit: cover;">
                    </div>
                    @endif
                    
                    <!-- Content -->
                    <div class="article-content">
                        {!! $berita->konten !!}
                    </div>
                    
                    <!-- Tags -->
                    @if($berita->tags)
                    <div class="mt-4 pt-4 border-top">
                        <i class="bi bi-tags me-2"></i>
                        @foreach(explode(',', $berita->tags) as $tag)
                        <span class="badge bg-light text-dark me-1">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    <!-- Share -->
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="fw-bold mb-3">Bagikan Artikel Ini:</h6>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-facebook"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($berita->judul) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-twitter"></i> Twitter
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($berita->judul . ' ' . url()->current()) }}" target="_blank" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                            <button class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText('{{ url()->current() }}'); alert('Link berhasil disalin!');">
                                <i class="bi bi-link-45deg"></i> Salin Link
                            </button>
                        </div>
                    </div>
                </article>
                
                <!-- Navigation -->
                <div class="row mt-5 pt-4 border-top">
                    @if($prevBerita)
                    <div class="col-6">
                        <a href="{{ route('portal-pmb.berita.detail', $prevBerita->slug) }}" class="text-decoration-none">
                            <small class="text-muted"><i class="bi bi-arrow-left me-1"></i>Sebelumnya</small>
                            <p class="fw-bold text-primary mb-0">{{ Str::limit($prevBerita->judul, 40) }}</p>
                        </a>
                    </div>
                    @endif
                    @if($nextBerita)
                    <div class="col-6 text-end">
                        <a href="{{ route('portal-pmb.berita.detail', $nextBerita->slug) }}" class="text-decoration-none">
                            <small class="text-muted">Selanjutnya<i class="bi bi-arrow-right ms-1"></i></small>
                            <p class="fw-bold text-primary mb-0">{{ Str::limit($nextBerita->judul, 40) }}</p>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search -->
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Cari Berita</h5>
                        <form action="{{ route('portal-pmb.berita') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" placeholder="Kata kunci...">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Kategori -->
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="150">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Kategori</h5>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('portal-pmb.berita', ['kategori' => 'berita']) }}" class="d-flex justify-content-between align-items-center text-decoration-none text-muted py-2 border-bottom">
                                <span><i class="bi bi-newspaper me-2"></i>Berita</span>
                                <span class="badge bg-light text-dark rounded-pill">{{ $beritaCount['berita'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('portal-pmb.berita', ['kategori' => 'pengumuman']) }}" class="d-flex justify-content-between align-items-center text-decoration-none text-muted py-2 border-bottom">
                                <span><i class="bi bi-megaphone me-2"></i>Pengumuman</span>
                                <span class="badge bg-light text-dark rounded-pill">{{ $beritaCount['pengumuman'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('portal-pmb.berita', ['kategori' => 'tips']) }}" class="d-flex justify-content-between align-items-center text-decoration-none text-muted py-2">
                                <span><i class="bi bi-lightbulb me-2"></i>Tips & Trik</span>
                                <span class="badge bg-light text-dark rounded-pill">{{ $beritaCount['tips'] ?? 0 }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Berita Terkait -->
                @if(isset($beritaTerkait) && $beritaTerkait->count() > 0)
                <div class="card card-pmb" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Berita Terkait</h5>
                        @foreach($beritaTerkait as $related)
                        <div class="d-flex gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                            <img src="{{ $related->gambar_url }}" alt="{{ $related->judul }}" class="rounded" style="width: 70px; height: 70px; object-fit: cover;">
                            <div>
                                <a href="{{ route('portal-pmb.berita.detail', $related->slug) }}" class="text-decoration-none">
                                    <h6 class="fw-bold mb-1 text-dark">{{ Str::limit($related->judul, 50) }}</h6>
                                </a>
                                <small class="text-muted">{{ $related->published_at->format('d M Y') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
    }
    .article-content p {
        margin-bottom: 1.5rem;
    }
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1rem 0;
    }
    .article-content h2, .article-content h3, .article-content h4 {
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    .article-content ul, .article-content ol {
        margin-bottom: 1.5rem;
    }
    .article-content blockquote {
        border-left: 4px solid var(--primary-color);
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #666;
    }
</style>
@endpush
