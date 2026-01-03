@extends('portal-pmb.layouts.main')

@section('title', $prodi->nama . ' - Program Studi')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.program-studi') }}" class="text-white-50">Program Studi</a></li>
                        <li class="breadcrumb-item active text-white">{{ $prodi->nama }}</li>
                    </ol>
                </nav>
                <div class="d-flex flex-wrap gap-2 mb-3" data-aos="fade-up" data-aos-delay="50">
                    <span class="badge bg-light text-primary fs-6">{{ $prodi->jenjang }}</span>
                    @if($prodi->akreditasi)
                    <span class="badge bg-warning text-dark fs-6">Akreditasi {{ $prodi->akreditasi }}</span>
                    @endif
                </div>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">{{ $prodi->nama }}</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">{{ $prodi->fakultas->nama ?? '' }}</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Deskripsi -->
                <div class="card card-pmb mb-4" data-aos="fade-up">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><i class="bi bi-info-circle text-primary me-2"></i>Tentang Program Studi</h4>
                        <div class="text-muted">
                            {!! nl2br(e($prodi->deskripsi ?? 'Program studi ini dirancang untuk menghasilkan lulusan yang kompeten dan siap bersaing di dunia kerja. Dengan kurikulum yang relevan dengan kebutuhan industri dan didukung oleh tenaga pengajar yang berpengalaman, mahasiswa akan mendapatkan pengetahuan dan keterampilan yang dibutuhkan untuk sukses di bidangnya.')) !!}
                        </div>
                    </div>
                </div>
                
                <!-- Visi Misi -->
                @if($prodi->visi || $prodi->misi)
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><i class="bi bi-bullseye text-primary me-2"></i>Visi & Misi</h4>
                        @if($prodi->visi)
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary">Visi</h6>
                            <p class="text-muted">{{ $prodi->visi }}</p>
                        </div>
                        @endif
                        @if($prodi->misi)
                        <div>
                            <h6 class="fw-bold text-primary">Misi</h6>
                            <div class="text-muted">{!! nl2br(e($prodi->misi)) !!}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Profil Lulusan -->
                @if($prodi->profil_lulusan)
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="150">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><i class="bi bi-person-check text-primary me-2"></i>Profil Lulusan</h4>
                        <div class="text-muted">{!! nl2br(e($prodi->profil_lulusan)) !!}</div>
                    </div>
                </div>
                @endif
                
                <!-- Prospek Karir -->
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><i class="bi bi-briefcase text-primary me-2"></i>Prospek Karir</h4>
                        <div class="row g-3">
                            @php
                                $karir = $prodi->prospek_karir ?? ['Profesional di bidangnya', 'Wirausahawan', 'Peneliti', 'Akademisi', 'Konsultan'];
                                if (is_string($karir)) $karir = explode("\n", $karir);
                            @endphp
                            @foreach($karir as $item)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <span>{{ $item }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Info Box -->
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-file-text text-primary me-2"></i>Informasi Program</h5>
                        
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted">Jenjang</span>
                            <span class="fw-bold">{{ $prodi->jenjang }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted">Akreditasi</span>
                            <span class="fw-bold badge bg-warning text-dark">{{ $prodi->akreditasi ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted">Total SKS</span>
                            <span class="fw-bold">{{ $prodi->total_sks ?? 144 }} SKS</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted">Lama Studi</span>
                            <span class="fw-bold">{{ $prodi->lama_studi ?? '4' }} Tahun</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted">Kuota</span>
                            <span class="fw-bold">{{ $prodi->kuota ?? '-' }} Mahasiswa</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Fakultas</span>
                            <span class="fw-bold">{{ $prodi->fakultas->singkatan ?? $prodi->fakultas->nama ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Biaya -->
                @if(isset($biaya) && $biaya)
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-cash-stack text-primary me-2"></i>Biaya Kuliah</h5>
                        
                        @if($biaya->ukt)
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted">UKT/Semester</span>
                            <span class="fw-bold text-primary">Rp {{ number_format($biaya->ukt, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($biaya->spp)
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted">SPP/Semester</span>
                            <span class="fw-bold text-primary">Rp {{ number_format($biaya->spp, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <small class="text-muted d-block mt-3">* Biaya dapat berubah sewaktu-waktu</small>
                    </div>
                </div>
                @endif
                
                <!-- CTA -->
                <div class="card bg-primary text-white" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-mortarboard-fill display-4 mb-3"></i>
                        <h5 class="fw-bold mb-3">Tertarik dengan Program Studi ini?</h5>
                        <p class="opacity-75 mb-4">Daftarkan dirimu sekarang dan raih masa depanmu!</p>
                        <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-light btn-lg w-100">
                            <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
