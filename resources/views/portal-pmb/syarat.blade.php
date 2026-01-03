@extends('portal-pmb.layouts.main')

@section('title', 'Syarat Pendaftaran')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Syarat Pendaftaran</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Syarat Pendaftaran</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Persyaratan yang harus dipenuhi untuk mendaftar</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Syarat Umum -->
                <div class="card card-pmb mb-4" data-aos="fade-up">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-file-earmark-text text-primary me-2"></i>Syarat Umum</h5>
                        <ol class="mb-0">
                            <li class="mb-3">Warga Negara Indonesia (WNI) atau Warga Negara Asing (WNA) yang memiliki izin tinggal di Indonesia</li>
                            <li class="mb-3">Lulusan SMA/SMK/MA/sederajat atau siswa kelas XII yang akan lulus tahun ini</li>
                            <li class="mb-3">Memiliki ijazah atau Surat Keterangan Lulus (SKL)</li>
                            <li class="mb-3">Sehat jasmani dan rohani</li>
                            <li class="mb-3">Tidak buta warna (untuk program studi tertentu)</li>
                            <li class="mb-3">Bersedia mematuhi peraturan yang berlaku di kampus</li>
                        </ol>
                    </div>
                </div>

                <!-- Dokumen yang Diperlukan -->
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-folder text-primary me-2"></i>Dokumen yang Diperlukan</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <span>Pas foto 3x4 (3 lembar)</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <span>Fotokopi Ijazah/SKL</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <span>Fotokopi KTP</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <span>Fotokopi Kartu Keluarga</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <span>Fotokopi Rapor (jika diperlukan)</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <span>Surat Keterangan Sehat</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <span>Akta Kelahiran</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <span>Sertifikat Prestasi (opsional)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Syarat Khusus per Jalur -->
                @if($jalurSeleksi && $jalurSeleksi->count() > 0)
                <div class="card card-pmb" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-signpost-split text-primary me-2"></i>Syarat Khusus per Jalur Seleksi</h5>
                        <div class="accordion" id="jalurAccordion">
                            @foreach($jalurSeleksi as $index => $jalur)
                            <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#jalur{{ $jalur->id }}">
                                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                        {{ $jalur->nama }}
                                    </button>
                                </h2>
                                <div id="jalur{{ $jalur->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#jalurAccordion">
                                    <div class="accordion-body">
                                        @if($jalur->persyaratan)
                                            {!! nl2br(e($jalur->persyaratan)) !!}
                                        @else
                                            <p class="text-muted mb-0">Syarat sesuai dengan syarat umum pendaftaran.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- CTA -->
                <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="300">
                    <p class="text-muted mb-4">Sudah memenuhi semua syarat? Segera daftarkan diri Anda!</p>
                    <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                        <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
