@extends('layouts.app')

@section('title', 'Kelola Konten Portal PMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Konten Portal PMB</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item active">Konten Portal</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pmb.konten-pmb.pengaturan') }}" class="btn btn-outline-secondary">
            <i class="bi bi-gear me-1"></i> Pengaturan Umum
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Slider -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-images fs-2 text-primary"></i>
                    </div>
                    <h5 class="card-title">Slider</h5>
                    <p class="text-muted small">Banner/Carousel di halaman utama</p>
                    <h3 class="mb-3">{{ $sliderCount }}</h3>
                    <a href="{{ route('pmb.konten-pmb.slider.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-gear me-1"></i> Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Berita -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-newspaper fs-2 text-success"></i>
                    </div>
                    <h5 class="card-title">Berita</h5>
                    <p class="text-muted small">Berita & pengumuman PMB</p>
                    <h3 class="mb-3">{{ $beritaCount }}</h3>
                    <a href="{{ route('pmb.konten-pmb.berita.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-gear me-1"></i> Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-question-circle fs-2 text-info"></i>
                    </div>
                    <h5 class="card-title">FAQ</h5>
                    <p class="text-muted small">Pertanyaan yang sering diajukan</p>
                    <h3 class="mb-3">{{ $faqCount }}</h3>
                    <a href="{{ route('pmb.konten-pmb.faq.index') }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-gear me-1"></i> Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Testimoni -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-chat-quote fs-2 text-warning"></i>
                    </div>
                    <h5 class="card-title">Testimoni</h5>
                    <p class="text-muted small">Testimoni alumni/mahasiswa</p>
                    <h3 class="mb-3">{{ $testimoniCount }}</h3>
                    <a href="{{ route('pmb.konten-pmb.testimoni.index') }}" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-gear me-1"></i> Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Galeri -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-camera fs-2 text-danger"></i>
                    </div>
                    <h5 class="card-title">Galeri</h5>
                    <p class="text-muted small">Foto kegiatan & kampus</p>
                    <h3 class="mb-3">{{ $galeriCount }}</h3>
                    <a href="{{ route('pmb.konten-pmb.galeri.index') }}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-gear me-1"></i> Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Keunggulan -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-secondary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-award fs-2 text-secondary"></i>
                    </div>
                    <h5 class="card-title">Keunggulan</h5>
                    <p class="text-muted small">Keunggulan & prestasi kampus</p>
                    <h3 class="mb-3">{{ $keunggulanCount }}</h3>
                    <a href="{{ route('pmb.konten-pmb.keunggulan.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-gear me-1"></i> Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Fasilitas -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-dark bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-building fs-2 text-dark"></i>
                    </div>
                    <h5 class="card-title">Fasilitas</h5>
                    <p class="text-muted small">Fasilitas kampus</p>
                    <h3 class="mb-3">{{ $fasilitasCount }}</h3>
                    <a href="{{ route('pmb.konten-pmb.fasilitas.index') }}" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-gear me-1"></i> Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Kontak -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-telephone fs-2 text-primary"></i>
                    </div>
                    <h5 class="card-title">Kontak</h5>
                    <p class="text-muted small">Informasi kontak PMB</p>
                    <h3 class="mb-3">{{ $kontakCount }}</h3>
                    <a href="{{ route('pmb.konten-pmb.kontak.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-gear me-1"></i> Kelola
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Tips -->
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips Mengelola Konten</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Pastikan gambar slider memiliki resolusi yang baik (min. 1920x600px)</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Update berita secara berkala untuk menjaga engagement</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>FAQ yang lengkap dapat mengurangi pertanyaan berulang</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Testimoni dari alumni dapat meningkatkan kepercayaan calon mahasiswa</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Galeri foto kampus membantu calon mahasiswa mengenal lingkungan</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Pastikan informasi kontak selalu up-to-date</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
