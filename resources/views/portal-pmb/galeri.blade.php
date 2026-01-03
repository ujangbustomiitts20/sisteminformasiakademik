@extends('portal-pmb.layouts.main')

@section('title', 'Galeri')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Galeri</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Galeri Foto</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Kumpulan foto kegiatan kampus dan mahasiswa</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Filter Kategori -->
        @if($categories->count() > 0)
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button class="btn btn-primary filter-btn active" data-filter="all">Semua</button>
                    @foreach($categories as $category)
                    <button class="btn btn-outline-primary filter-btn" data-filter="{{ Str::slug($category) }}">{{ $category }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        @if($galeri->count() > 0)
        <div class="row g-4" id="galeriGrid">
            @foreach($galeri as $index => $item)
            <div class="col-6 col-md-4 col-lg-3 galeri-item" data-category="{{ Str::slug($item->kategori) }}" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 50 }}">
                <div class="galeri-card">
                    <div class="galeri-img-wrapper">
                        <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" class="galeri-img" loading="lazy">
                        <div class="galeri-overlay">
                            <div class="galeri-actions">
                                <button class="btn btn-light btn-sm rounded-circle me-2" data-bs-toggle="modal" data-bs-target="#galeriModal" data-img="{{ $item->gambar_url }}" data-title="{{ $item->judul }}" data-desc="{{ $item->deskripsi }}">
                                    <i class="bi bi-zoom-in"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">{{ Str::limit($item->judul, 30) }}</h6>
                        <small class="text-muted">{{ $item->kategori }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
            {{ $galeri->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <img src="https://illustrations.popsy.co/gray/photos.svg" alt="No Photos" class="mb-4" style="max-width: 200px;">
            <h4 class="fw-bold text-muted">Belum Ada Foto</h4>
            <p class="text-muted">Saat ini belum ada foto yang dipublikasikan.</p>
        </div>
        @endif
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="galeriModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="galeriModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img src="" alt="" class="w-100" id="galeriModalImg">
            </div>
            <div class="modal-footer border-0">
                <p class="text-muted mb-0 me-auto" id="galeriModalDesc"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .galeri-card {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .galeri-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .galeri-img-wrapper {
        position: relative;
        overflow: hidden;
        aspect-ratio: 1/1;
    }
    .galeri-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .galeri-card:hover .galeri-img {
        transform: scale(1.1);
    }
    .galeri-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .galeri-card:hover .galeri-overlay {
        opacity: 1;
    }
    .galeri-actions .btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galeriItems = document.querySelectorAll('.galeri-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active state
            filterBtns.forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-primary');
            });
            this.classList.remove('btn-outline-primary');
            this.classList.add('active', 'btn-primary');
            
            // Filter items
            galeriItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Modal
    const galeriModal = document.getElementById('galeriModal');
    galeriModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const img = button.dataset.img;
        const title = button.dataset.title;
        const desc = button.dataset.desc;
        
        document.getElementById('galeriModalImg').src = img;
        document.getElementById('galeriModalTitle').textContent = title;
        document.getElementById('galeriModalDesc').textContent = desc || '';
    });
});
</script>
@endpush
