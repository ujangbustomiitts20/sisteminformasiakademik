@extends('portal-pmb.layouts.main')

@section('title', 'Login Calon Mahasiswa')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Login</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Portal Calon Mahasiswa</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Login untuk mengakses dashboard pendaftaran Anda</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card card-pmb" data-aos="fade-up">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-circle text-primary fs-1"></i>
                            </div>
                            <h4 class="fw-bold">Login Calon Mahasiswa</h4>
                            <p class="text-muted">Masukkan nomor pendaftaran dan password untuk mengakses dashboard</p>
                        </div>

                        @if($errors->has('login'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first('login') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form action="{{ route('portal-pmb.login.post') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nomor Pendaftaran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="no_pendaftaran" class="form-control @error('no_pendaftaran') is-invalid @enderror" 
                                        value="{{ old('no_pendaftaran') }}" placeholder="Contoh: PMB2025001234" required>
                                </div>
                                @error('no_pendaftaran')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                        placeholder="Masukkan password" required>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="text-muted mb-2">Belum punya akun?</p>
                            <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bantuan -->
                <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="100">
                    <p class="text-muted mb-2">Mengalami kesulitan login?</p>
                    <a href="{{ route('portal-pmb.kontak') }}" class="text-decoration-none">
                        <i class="bi bi-headset me-1"></i>Hubungi Panitia PMB
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
