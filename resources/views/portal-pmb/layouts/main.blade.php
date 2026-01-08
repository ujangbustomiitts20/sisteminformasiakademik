<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'PMB') - {{ $konten['nama_universitas'] ?? 'Universitas' }}</title>
    
    <!-- SEO Meta -->
    <meta name="description" content="{{ $konten['meta_description'] ?? 'Penerimaan Mahasiswa Baru' }}">
    <meta name="keywords" content="{{ $konten['meta_keywords'] ?? 'pmb, mahasiswa baru, pendaftaran' }}">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ isset($konten['favicon']) ? asset('storage/' . $konten['favicon']) : asset('favicon.ico') }}">
    
    <!-- Vite Assets (Bootstrap, Bootstrap Icons) -->
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-dark: #0b5ed7;
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
            --success-color: #198754;
            --light-bg: #f8f9fa;
            --dark-color: #212529;
        }
        
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        body {
            background-color: #ffffff;
            overflow-x: hidden;
        }
        
        /* Navbar */
        .navbar-pmb {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            padding: 0.8rem 0;
            transition: all 0.3s ease;
        }
        
        .navbar-pmb.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-pmb .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .navbar-pmb .navbar-brand img {
            height: 45px;
            margin-right: 10px;
        }
        
        .navbar-pmb .nav-link {
            font-weight: 500;
            color: var(--dark-color);
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }
        
        .navbar-pmb .nav-link:hover,
        .navbar-pmb .nav-link.active {
            color: var(--primary-color);
        }
        
        .btn-daftar {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            border-radius: 50px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-daftar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(13, 110, 253, 0.3);
            color: white;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 1.5rem;
        }
        
        /* Section Styles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }
        
        .section-subtitle {
            color: var(--secondary-color);
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }
        
        /* Cards */
        .card-pmb {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card-pmb:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
        }
        
        /* Feature Cards */
        .feature-card {
            padding: 2rem;
            text-align: center;
            border-radius: 20px;
            background: white;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }
        
        /* Stats */
        .stat-box {
            text-align: center;
            padding: 2rem;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: var(--secondary-color);
            font-weight: 500;
            margin-top: 0.5rem;
        }
        
        /* Prodi Cards */
        .prodi-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }
        
        .prodi-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15);
        }
        
        /* Footer */
        .footer-pmb {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 4rem 0 2rem;
        }
        
        .footer-pmb h5 {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .footer-pmb a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-pmb a:hover {
            color: white;
        }
        
        .footer-social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .footer-social a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        /* Timeline */
        .timeline {
            position: relative;
            padding: 0;
            list-style: none;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 2px;
            background: #e9ecef;
            transform: translateX(-50%);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .timeline-badge {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-weight: 700;
        }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
        }
        
        @media (max-width: 767.98px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-section {
                min-height: auto;
                padding: 8rem 0 5rem;
            }
        }
        
        /* Animations */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Floating Animation */
        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-pmb fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('portal-pmb.index') }}">
                @if(isset($konten['logo']))
                    <img src="{{ asset('storage/' . $konten['logo']) }}" alt="Logo">
                @else
                    <i class="bi bi-mortarboard-fill me-2"></i>
                @endif
                <span>{{ $konten['nama_universitas'] ?? 'PMB Online' }}</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal-pmb.index') ? 'active' : '' }}" href="{{ route('portal-pmb.index') }}">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Informasi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('portal-pmb.program-studi') }}">Program Studi</a></li>
                            <li><a class="dropdown-item" href="{{ route('portal-pmb.jalur-seleksi') }}">Jalur Seleksi</a></li>
                            <li><a class="dropdown-item" href="{{ route('portal-pmb.biaya') }}">Biaya Kuliah</a></li>
                            <li><a class="dropdown-item" href="{{ route('portal-pmb.fasilitas') }}">Fasilitas</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Pendaftaran
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('portal-pmb.jadwal') }}">Jadwal Pendaftaran</a></li>
                            <li><a class="dropdown-item" href="{{ route('portal-pmb.alur-pendaftaran') }}">Alur Pendaftaran</a></li>
                            <li><a class="dropdown-item" href="{{ route('portal-pmb.syarat') }}">Syarat & Ketentuan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('portal-pmb.cek-pengumuman') }}">Cek Pengumuman</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal-pmb.berita*') ? 'active' : '' }}" href="{{ route('portal-pmb.berita') }}">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal-pmb.faq') ? 'active' : '' }}" href="{{ route('portal-pmb.faq') }}">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal-pmb.kontak') ? 'active' : '' }}" href="{{ route('portal-pmb.kontak') }}">Kontak</a>
                    </li>
                </ul>
                <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-daftar">
                    <i class="bi bi-pencil-square me-1"></i> Daftar Sekarang
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    @yield('content')
    
    <!-- Footer -->
    <footer class="footer-pmb">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5><i class="bi bi-mortarboard-fill me-2"></i>{{ $konten['nama_universitas'] ?? 'Universitas' }}</h5>
                    <p class="text-white-50">{{ $konten['deskripsi_singkat'] ?? 'Perguruan tinggi terkemuka yang berkomitmen menghasilkan lulusan berkualitas dan berdaya saing global.' }}</p>
                    <div class="footer-social mt-3">
                        @if(isset($sosialMedia))
                            @foreach($sosialMedia as $social)
                                <a href="{{ $social->link ?? '#' }}" target="_blank" title="{{ $social->label }}">
                                    <i class="bi bi-{{ $social->icon ?? $social->type }}"></i>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4">
                    <h5>Link Cepat</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('portal-pmb.program-studi') }}">Program Studi</a></li>
                        <li class="mb-2"><a href="{{ route('portal-pmb.jalur-seleksi') }}">Jalur Seleksi</a></li>
                        <li class="mb-2"><a href="{{ route('portal-pmb.jadwal') }}">Jadwal PMB</a></li>
                        <li class="mb-2"><a href="{{ route('portal-pmb.biaya') }}">Biaya Kuliah</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-4">
                    <h5>Informasi</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('portal-pmb.alur-pendaftaran') }}">Alur Pendaftaran</a></li>
                        <li class="mb-2"><a href="{{ route('portal-pmb.syarat') }}">Persyaratan</a></li>
                        <li class="mb-2"><a href="{{ route('portal-pmb.faq') }}">FAQ</a></li>
                        <li class="mb-2"><a href="{{ route('portal-pmb.galeri') }}">Galeri</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-4">
                    <h5>Hubungi Kami</h5>
                    @if(isset($kontak))
                        @foreach($kontak as $k)
                            <p class="text-white-50 mb-2">
                                <i class="bi bi-{{ $k->icon ?? 'geo-alt' }} me-2"></i>
                                @if($k->type == 'whatsapp')
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $k->value) }}" target="_blank">{{ $k->value }}</a>
                                @elseif($k->type == 'email')
                                    <a href="mailto:{{ $k->value }}">{{ $k->value }}</a>
                                @elseif($k->type == 'phone')
                                    <a href="tel:{{ $k->value }}">{{ $k->value }}</a>
                                @else
                                    {{ $k->value }}
                                @endif
                            </p>
                        @endforeach
                    @endif
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-white-50 mb-0">&copy; {{ date('Y') }} {{ $konten['nama_universitas'] ?? 'Universitas' }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <a href="{{ route('portal-pmb.login') }}" class="text-white-50 me-3">
                        <i class="bi bi-person-circle me-1"></i> Login Pendaftar
                    </a>
                    <a href="{{ route('login') }}" class="text-white-50">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login Admin
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- WhatsApp Float Button -->
    @if(isset($kontak))
        @php $wa = $kontak->where('type', 'whatsapp')->first(); @endphp
        @if($wa)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa->value) }}?text=Halo, saya ingin bertanya tentang PMB" 
               class="btn btn-success rounded-circle position-fixed shadow-lg"
               style="bottom: 30px; right: 30px; width: 60px; height: 60px; font-size: 1.5rem; z-index: 1000;"
               target="_blank"
               title="Hubungi via WhatsApp">
                <i class="bi bi-whatsapp"></i>
            </a>
        @endif
    @endif
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-pmb');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
