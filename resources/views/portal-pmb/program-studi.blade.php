@extends('portal-pmb.layouts.main')

@section('title', 'Program Studi')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Program Studi</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Program Studi</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Temukan program studi yang sesuai dengan minat dan bakatmu</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Filter -->
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button class="btn btn-primary filter-btn active" data-filter="all">Semua</button>
                    @foreach($fakultas as $fak)
                    <button class="btn btn-outline-primary filter-btn" data-filter="{{ Str::slug($fak->nama) }}">{{ $fak->nama }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Daftar Prodi per Fakultas -->
        @foreach($fakultas as $fak)
        <div class="fakultas-section mb-5" data-fakultas="{{ Str::slug($fak->nama) }}" data-aos="fade-up">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                    <i class="bi bi-building text-primary fs-4"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $fak->nama }}</h3>
                    <p class="text-muted mb-0">{{ $fak->programStudi->count() }} Program Studi</p>
                </div>
            </div>
            
            <div class="row g-4">
                @foreach($fak->programStudi as $prodi)
                <div class="col-md-6 col-lg-4">
                    <div class="prodi-card h-100">
                        <div class="d-flex align-items-start mb-3">
                            <span class="badge bg-{{ $prodi->jenjang == 'S1' ? 'primary' : ($prodi->jenjang == 'S2' ? 'success' : ($prodi->jenjang == 'S3' ? 'warning' : 'secondary')) }} me-2">{{ $prodi->jenjang }}</span>
                            @if($prodi->akreditasi)
                            <span class="badge bg-light text-dark">{{ $prodi->akreditasi }}</span>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-2">{{ $prodi->nama }}</h5>
                        <p class="text-muted small mb-3">{{ Str::limit($prodi->deskripsi ?? 'Program studi yang menghasilkan lulusan berkualitas dan siap bersaing di dunia kerja.', 100) }}</p>
                        
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-light text-dark"><i class="bi bi-clock me-1"></i>{{ $prodi->total_sks ?? 144 }} SKS</span>
                            <span class="badge bg-light text-dark"><i class="bi bi-people me-1"></i>{{ $prodi->kuota ?? '-' }} Kuota</span>
                        </div>
                        
                        <a href="{{ route('portal-pmb.program-studi.detail', $prodi->id) }}" class="btn btn-sm btn-outline-primary w-100">
                            Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container position-relative text-center text-white">
        <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">Sudah Menentukan Pilihan?</h2>
        <p class="fs-5 opacity-75 mb-4" data-aos="fade-up" data-aos-delay="100">Daftarkan dirimu sekarang dan jadilah bagian dari keluarga besar kami!</p>
        <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-light btn-lg px-5" data-aos="fade-up" data-aos-delay="200">
            <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const fakultasSections = document.querySelectorAll('.fakultas-section');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active state
            filterBtns.forEach(b => b.classList.remove('active', 'btn-primary'));
            filterBtns.forEach(b => b.classList.add('btn-outline-primary'));
            this.classList.remove('btn-outline-primary');
            this.classList.add('active', 'btn-primary');
            
            // Filter sections
            fakultasSections.forEach(section => {
                if (filter === 'all' || section.dataset.fakultas === filter) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush
