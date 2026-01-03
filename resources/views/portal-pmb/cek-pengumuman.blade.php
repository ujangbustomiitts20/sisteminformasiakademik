@extends('portal-pmb.layouts.main')

@section('title', 'Cek Pengumuman Kelulusan')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Cek Pengumuman</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Cek Pengumuman Kelulusan</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Masukkan nomor pendaftaran untuk melihat hasil seleksi</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card card-pmb" data-aos="fade-up">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-search text-primary fs-1"></i>
                            </div>
                            <h4 class="fw-bold">Cek Hasil Seleksi</h4>
                            <p class="text-muted">Masukkan nomor pendaftaran Anda untuk melihat pengumuman hasil seleksi PMB</p>
                        </div>

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form action="{{ route('portal-pmb.cek-pengumuman.result') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Nomor Pendaftaran <span class="text-danger">*</span></label>
                                <input type="text" name="no_pendaftaran" class="form-control form-control-lg @error('no_pendaftaran') is-invalid @enderror" 
                                    value="{{ old('no_pendaftaran') }}" placeholder="Contoh: PMB2025001234" required>
                                @error('no_pendaftaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control form-control-lg @error('tanggal_lahir') is-invalid @enderror" 
                                    value="{{ old('tanggal_lahir') }}" required>
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-search me-2"></i>Cek Pengumuman
                            </button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="text-muted small mb-0">
                                Belum mendaftar? 
                                <a href="{{ route('portal-pmb.pendaftaran') }}" class="text-primary fw-semibold">Daftar Sekarang</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="card border-0 bg-light mt-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Informasi</h6>
                        <ul class="small text-muted mb-0">
                            <li class="mb-2">Pengumuman hasil seleksi akan tersedia sesuai jadwal yang telah ditentukan</li>
                            <li class="mb-2">Pastikan nomor pendaftaran dan tanggal lahir yang Anda masukkan benar</li>
                            <li class="mb-2">Jika mengalami kendala, silakan hubungi panitia PMB</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
