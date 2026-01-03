@extends('portal-pmb.layouts.main')

@section('title', 'FAQ - Pertanyaan yang Sering Diajukan')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">FAQ</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Pertanyaan yang Sering Diajukan</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Temukan jawaban dari pertanyaan yang paling sering ditanyakan</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Search -->
                <div class="mb-5" data-aos="fade-up">
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                        <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-0 py-3" id="faqSearch" placeholder="Cari pertanyaan...">
                    </div>
                </div>
                
                @if($categories->count() > 0)
                    <!-- Tabs for Categories -->
                    <ul class="nav nav-pills nav-fill mb-4 gap-2" id="faqTabs" role="tablist" data-aos="fade-up" data-aos-delay="100">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                                <i class="bi bi-grid me-1"></i>Semua
                            </button>
                        </li>
                        @foreach($categories as $category)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="{{ Str::slug($category) }}-tab" data-bs-toggle="tab" data-bs-target="#{{ Str::slug($category) }}" type="button" role="tab">
                                {{ $category }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                    
                    <div class="tab-content" id="faqTabContent">
                        <!-- All FAQs -->
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            <div class="accordion faq-accordion" id="allFaq" data-aos="fade-up" data-aos-delay="150">
                                @foreach($faq as $index => $item)
                                <div class="accordion-item faq-item border-0 mb-3 rounded-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $item->id }}">
                                            <span class="me-3 badge bg-primary bg-opacity-10 text-primary">{{ $index + 1 }}</span>
                                            {{ $item->pertanyaan }}
                                        </button>
                                    </h2>
                                    <div id="faq{{ $item->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#allFaq">
                                        <div class="accordion-body text-muted">
                                            {!! nl2br(e($item->jawaban)) !!}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Category Tabs -->
                        @foreach($categories as $category)
                        <div class="tab-pane fade" id="{{ Str::slug($category) }}" role="tabpanel">
                            <div class="accordion faq-accordion" id="faq{{ Str::slug($category) }}">
                                @php $catIndex = 0; @endphp
                                @foreach($faq->where('kategori', $category) as $item)
                                <div class="accordion-item faq-item border-0 mb-3 rounded-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $catIndex > 0 ? 'collapsed' : '' }} rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCat{{ $item->id }}">
                                            <span class="me-3 badge bg-primary bg-opacity-10 text-primary">{{ ++$catIndex }}</span>
                                            {{ $item->pertanyaan }}
                                        </button>
                                    </h2>
                                    <div id="faqCat{{ $item->id }}" class="accordion-collapse collapse {{ $catIndex == 1 ? 'show' : '' }}" data-bs-parent="#faq{{ Str::slug($category) }}">
                                        <div class="accordion-body text-muted">
                                            {!! nl2br(e($item->jawaban)) !!}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="accordion faq-accordion" id="allFaq" data-aos="fade-up" data-aos-delay="100">
                        @forelse($faq as $index => $item)
                        <div class="accordion-item faq-item border-0 mb-3 rounded-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $item->id }}">
                                    <span class="me-3 badge bg-primary bg-opacity-10 text-primary">{{ $index + 1 }}</span>
                                    {{ $item->pertanyaan }}
                                </button>
                            </h2>
                            <div id="faq{{ $item->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#allFaq">
                                <div class="accordion-body text-muted">
                                    {!! nl2br(e($item->jawaban)) !!}
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <img src="https://illustrations.popsy.co/gray/question-mark.svg" alt="No FAQ" class="mb-4" style="max-width: 200px;">
                            <h4 class="fw-bold text-muted">Belum Ada FAQ</h4>
                            <p class="text-muted">Saat ini belum ada FAQ yang tersedia.</p>
                        </div>
                        @endforelse
                    </div>
                @endif
                
                <!-- Tidak menemukan jawaban -->
                @php
                    $whatsappItem = $kontak->where('type', 'whatsapp')->first();
                    $emailItem = $kontak->where('type', 'email')->first();
                @endphp
                <div class="card card-pmb mt-5" data-aos="fade-up">
                    <div class="card-body p-5 text-center">
                        <i class="bi bi-chat-dots text-primary display-4 mb-3"></i>
                        <h4 class="fw-bold">Tidak Menemukan Jawaban?</h4>
                        <p class="text-muted mb-4">Hubungi tim kami untuk mendapatkan bantuan lebih lanjut</p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            @if($whatsappItem && $whatsappItem->value)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappItem->value) }}" target="_blank" class="btn btn-success">
                                <i class="bi bi-whatsapp me-2"></i>Chat WhatsApp
                            </a>
                            @endif
                            @if($emailItem && $emailItem->value)
                            <a href="mailto:{{ $emailItem->value }}" class="btn btn-outline-primary">
                                <i class="bi bi-envelope me-2"></i>Kirim Email
                            </a>
                            @endif
                            <a href="{{ route('portal-pmb.kontak') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-geo-alt me-2"></i>Kunjungi Kampus
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .nav-pills .nav-link {
        color: var(--text-secondary);
        font-weight: 500;
    }
    .nav-pills .nav-link.active {
        background-color: var(--primary-color);
    }
    .faq-accordion .accordion-button {
        font-weight: 600;
        font-size: 1rem;
    }
    .faq-accordion .accordion-button:not(.collapsed) {
        background-color: var(--primary-color);
        color: white;
    }
    .faq-accordion .accordion-button:not(.collapsed) .badge {
        background-color: white !important;
        color: var(--primary-color) !important;
    }
    .faq-accordion .accordion-body {
        font-size: 1rem;
        line-height: 1.7;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faqSearch');
    const faqItems = document.querySelectorAll('.faq-item');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        faqItems.forEach(item => {
            const question = item.querySelector('.accordion-button').textContent.toLowerCase();
            const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
            
            if (question.includes(query) || answer.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
