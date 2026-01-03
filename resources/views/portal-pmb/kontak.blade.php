@extends('portal-pmb.layouts.main')

@section('title', 'Kontak Kami')

@php
    // Helper untuk mendapatkan value kontak berdasarkan type
    $getKontakValue = function($type) use ($kontak) {
        $item = $kontak->where('type', $type)->first();
        return $item ? $item->value : null;
    };
    
    $getKontakLink = function($type) use ($kontak) {
        $item = $kontak->where('type', $type)->first();
        return $item ? $item->link : null;
    };
    
    // Helper untuk social media
    $getSosmedLink = function($type) use ($sosialMedia) {
        $item = $sosialMedia->where('type', $type)->first();
        return $item ? ($item->link ?? $item->value) : null;
    };
@endphp

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Kontak</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Hubungi Kami</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Kami siap membantu menjawab pertanyaan Anda</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Info -->
            <div class="col-lg-4">
                <div class="card card-pmb h-100" data-aos="fade-up">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Informasi Kontak</h5>
                        
                        <!-- Alamat -->
                        <div class="d-flex mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3 flex-shrink-0">
                                <i class="bi bi-geo-alt text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Alamat</h6>
                                <p class="text-muted mb-0">{{ $getKontakValue('address') ?? 'Jl. Pendidikan No. 123, Kota Pendidikan' }}</p>
                            </div>
                        </div>
                        
                        <!-- Telepon -->
                        <div class="d-flex mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3 flex-shrink-0">
                                <i class="bi bi-telephone text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Telepon</h6>
                                <p class="text-muted mb-0">{{ $getKontakValue('phone') ?? '(021) 123-4567' }}</p>
                            </div>
                        </div>
                        
                        <!-- WhatsApp -->
                        <div class="d-flex mb-4">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3 flex-shrink-0">
                                <i class="bi bi-whatsapp text-success fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">WhatsApp</h6>
                                <p class="text-muted mb-0">{{ $getKontakValue('whatsapp') ?? '0812-3456-7890' }}</p>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="d-flex mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3 flex-shrink-0">
                                <i class="bi bi-envelope text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Email</h6>
                                <p class="text-muted mb-0">{{ $getKontakValue('email') ?? 'pmb@kampus.ac.id' }}</p>
                            </div>
                        </div>
                        
                        <!-- Jam Operasional -->
                        <div class="d-flex">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3 flex-shrink-0">
                                <i class="bi bi-clock text-warning fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Jam Operasional</h6>
                                <p class="text-muted mb-0">{{ $getKontakValue('jam_operasional') ?? 'Senin - Jumat: 08.00 - 16.00 WIB' }}</p>
                            </div>
                        </div>
                        
                        <!-- Social Media -->
                        @if($sosialMedia && $sosialMedia->count() > 0)
                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Ikuti Kami</h6>
                        <div class="d-flex gap-2">
                            @if($facebook = $getSosmedLink('facebook'))
                            <a href="{{ $facebook }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-facebook"></i>
                            </a>
                            @endif
                            @if($instagram = $getSosmedLink('instagram'))
                            <a href="{{ $instagram }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-instagram"></i>
                            </a>
                            @endif
                            @if($twitter = $getSosmedLink('twitter'))
                            <a href="{{ $twitter }}" target="_blank" class="btn btn-outline-info btn-sm rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-twitter"></i>
                            </a>
                            @endif
                            @if($youtube = $getSosmedLink('youtube'))
                            <a href="{{ $youtube }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-youtube"></i>
                            </a>
                            @endif
                            @if($tiktok = $getSosmedLink('tiktok'))
                            <a href="{{ $tiktok }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-tiktok"></i>
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card card-pmb" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Kirim Pesan</h5>
                        
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif
                        
                        <form action="{{ route('portal-pmb.kontak.send') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. HP/WhatsApp</label>
                                    <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon') }}">
                                    @error('telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subjek <span class="text-danger">*</span></label>
                                    <select name="subjek" class="form-select @error('subjek') is-invalid @enderror" required>
                                        <option value="">Pilih Subjek</option>
                                        <option value="Informasi PMB" {{ old('subjek') == 'Informasi PMB' ? 'selected' : '' }}>Informasi PMB</option>
                                        <option value="Biaya Kuliah" {{ old('subjek') == 'Biaya Kuliah' ? 'selected' : '' }}>Biaya Kuliah</option>
                                        <option value="Jadwal & Prosedur" {{ old('subjek') == 'Jadwal & Prosedur' ? 'selected' : '' }}>Jadwal & Prosedur</option>
                                        <option value="Program Studi" {{ old('subjek') == 'Program Studi' ? 'selected' : '' }}>Program Studi</option>
                                        <option value="Beasiswa" {{ old('subjek') == 'Beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                                        <option value="Lainnya" {{ old('subjek') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('subjek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Pesan <span class="text-danger">*</span></label>
                                    <textarea name="pesan" class="form-control @error('pesan') is-invalid @enderror" rows="5" required>{{ old('pesan') }}</textarea>
                                    @error('pesan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-send me-2"></i>Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map -->
        @php
            $googleMaps = $kontak->where('type', 'google_maps')->first();
        @endphp
        @if($googleMaps && $googleMaps->value)
        <div class="row mt-5">
            <div class="col-12" data-aos="fade-up">
                <div class="card card-pmb overflow-hidden">
                    <div class="ratio ratio-21x9">
                        <iframe src="{{ $googleMaps->value }}" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
