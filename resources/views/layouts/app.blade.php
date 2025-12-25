<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('app_name', 'SIAKAD')) - {{ setting('app_description', 'Sistem Informasi Akademik') }}</title>
    
    @if(setting('institution_favicon'))
    <link rel="icon" href="{{ Storage::url(setting('institution_favicon')) }}" type="image/x-icon">
    @endif
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: {{ setting('primary_color', '#4f46e5') }};
            --primary-hover: {{ setting('primary_color', '#4f46e5') }}dd;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h4 {
            color: #fff;
            font-weight: 700;
            margin: 0;
        }
        
        .sidebar-brand small {
            color: #94a3b8;
            font-size: 0.75rem;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .sidebar-menu .menu-header {
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.75rem 1.5rem;
            margin-top: 0.5rem;
        }
        
        .sidebar-menu .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: #fff;
        }
        
        .sidebar-menu .nav-link.active {
            background: rgba(79, 70, 229, 0.2);
            color: #818cf8;
            border-left-color: #818cf8;
        }
        
        .sidebar-menu .nav-link i {
            font-size: 1.1rem;
            width: 24px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: #fff;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        .user-dropdown .btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Content Area */
        .content-area {
            padding: 1.5rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        
        /* Stats Cards */
        .stat-card {
            border-radius: 0.75rem;
            padding: 1.25rem;
            color: #fff;
        }
        
        .stat-card.bg-primary { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .stat-card.bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-card.bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
        .stat-card.bg-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        
        .stat-card .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        /* Table */
        .table th {
            font-weight: 600;
            color: #475569;
            border-bottom-width: 2px;
        }
        
        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
        }
        
        /* Page Title */
        .page-title {
            margin-bottom: 1.5rem;
        }
        
        .page-title h4 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        
        .page-title .breadcrumb {
            margin-bottom: 0;
            font-size: 0.875rem;
        }
        
        /* Mobile responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            @if(setting('institution_logo'))
            <img src="{{ Storage::url(setting('institution_logo')) }}" alt="Logo" style="max-height: 40px;" class="mb-2">
            @else
            <i class="bi bi-mortarboard-fill me-2"></i>
            @endif
            <h4>{{ setting('app_name', 'SIAKAD') }}</h4>
            <small>{{ setting('app_description', 'Sistem Informasi Akademik') }}</small>
        </div>
        
        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            
            @if(auth()->check() && auth()->user()->isAdmin())
            <!-- Menu Admin -->
            <div class="menu-header">Sistem</div>
            <a href="{{ route('user.index') }}" class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Kelola User</span>
            </a>
            
            <div class="menu-header">Master Data</div>
            <a href="{{ route('fakultas.index') }}" class="nav-link {{ request()->routeIs('fakultas.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Fakultas</span>
            </a>
            <a href="{{ route('program-studi.index') }}" class="nav-link {{ request()->routeIs('program-studi.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>Program Studi</span>
            </a>
            <a href="{{ route('ruangan.index') }}" class="nav-link {{ request()->routeIs('ruangan.*') ? 'active' : '' }}">
                <i class="bi bi-door-open"></i>
                <span>Ruangan</span>
            </a>
            <a href="{{ route('tahun-akademik.index') }}" class="nav-link {{ request()->routeIs('tahun-akademik.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Tahun Akademik</span>
            </a>
            
            <div class="menu-header">Akademik</div>
            <a href="{{ route('mahasiswa.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Mahasiswa</span>
            </a>
            <a href="{{ route('dosen.index') }}" class="nav-link {{ request()->routeIs('dosen.*') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i>
                <span>Dosen</span>
            </a>
            <a href="{{ route('mata-kuliah.index') }}" class="nav-link {{ request()->routeIs('mata-kuliah.*') ? 'active' : '' }}">
                <i class="bi bi-book"></i>
                <span>Mata Kuliah</span>
            </a>
            <a href="{{ route('prasyarat.index') }}" class="nav-link {{ request()->routeIs('prasyarat.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>Prasyarat MK</span>
            </a>
            <a href="{{ route('kurikulum.index') }}" class="nav-link {{ request()->routeIs('kurikulum.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3"></i>
                <span>Kurikulum</span>
            </a>
            <a href="{{ route('bimbingan.index') }}" class="nav-link {{ request()->routeIs('bimbingan.index') || request()->routeIs('bimbingan.show') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i>
                <span>Bimbingan Akademik</span>
            </a>
            <a href="{{ route('cuti.index') }}" class="nav-link {{ request()->routeIs('cuti.index') || request()->routeIs('cuti.show') || request()->routeIs('cuti.history') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i>
                <span>Cuti Akademik</span>
            </a>
            <a href="{{ route('jadwal-kuliah.index') }}" class="nav-link {{ request()->routeIs('jadwal-kuliah.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i>
                <span>Jadwal Kuliah</span>
            </a>
            <a href="{{ route('pertemuan.approval') }}" class="nav-link {{ request()->routeIs('pertemuan.approval') || request()->routeIs('pertemuan.admin.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Pertemuan</span>
            </a>
            <a href="{{ route('nilai.index') }}" class="nav-link {{ request()->routeIs('nilai.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data"></i>
                <span>Input Nilai</span>
            </a>
            <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Absensi</span>
            </a>
            
            <div class="menu-header">Keuangan</div>
            <a href="{{ route('pembayaran.index') }}" class="nav-link {{ request()->routeIs('pembayaran.*') && !request()->routeIs('pembayaran.mahasiswa') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span>Pembayaran Lama</span>
            </a>
            <a href="{{ route('tarif.index') }}" class="nav-link {{ request()->routeIs('tarif.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span>Tarif</span>
            </a>
            <a href="{{ route('tagihan.index') }}" class="nav-link {{ request()->routeIs('tagihan.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Tagihan</span>
            </a>
            <a href="{{ route('transaksi-pembayaran.index') }}" class="nav-link {{ request()->routeIs('transaksi-pembayaran.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Transaksi</span>
            </a>
            <a href="{{ route('beasiswa.index') }}" class="nav-link {{ request()->routeIs('beasiswa.*') && !request()->routeIs('beasiswa.available') && !request()->routeIs('beasiswa.ajukan') ? 'active' : '' }}">
                <i class="bi bi-award"></i>
                <span>Beasiswa</span>
            </a>
            <a href="{{ route('beasiswa.penerima.index') }}" class="nav-link {{ request()->routeIs('beasiswa.penerima.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Penerima Beasiswa</span>
            </a>
            <a href="{{ route('pengaturan-denda.index') }}" class="nav-link {{ request()->routeIs('pengaturan-denda.*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Pengaturan Denda</span>
            </a>
            <a href="{{ route('skema-cicilan.index') }}" class="nav-link {{ request()->routeIs('skema-cicilan.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3-range"></i>
                <span>Skema Cicilan</span>
            </a>
            <a href="{{ route('cicilan.index') }}" class="nav-link {{ request()->routeIs('cicilan.*') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i>
                <span>Manajemen Cicilan</span>
            </a>
            <a href="{{ route('notifikasi.index') }}" class="nav-link {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i>
                <span>Notifikasi Keuangan</span>
            </a>
            
            <div class="menu-header">Rekonsiliasi Bank</div>
            <a href="{{ route('akun-bank.index') }}" class="nav-link {{ request()->routeIs('akun-bank.*') ? 'active' : '' }}">
                <i class="bi bi-bank"></i>
                <span>Akun Bank</span>
            </a>
            <a href="{{ route('mutasi-bank.index') }}" class="nav-link {{ request()->routeIs('mutasi-bank.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Mutasi Bank</span>
            </a>
            <a href="{{ route('rekonsiliasi.index') }}" class="nav-link {{ request()->routeIs('rekonsiliasi.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Rekonsiliasi</span>
            </a>
            <a href="{{ route('keuangan.refund.index') }}" class="nav-link {{ request()->routeIs('keuangan.refund.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-return-left"></i>
                <span>Refund</span>
            </a>
            
            <div class="menu-header">Potongan & Diskon</div>
            <a href="{{ route('jenis-potongan.index') }}" class="nav-link {{ request()->routeIs('jenis-potongan.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span>Jenis Potongan</span>
            </a>
            <a href="{{ route('periode-diskon.index') }}" class="nav-link {{ request()->routeIs('periode-diskon.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                <span>Periode Diskon</span>
            </a>
            <a href="{{ route('potongan-mahasiswa.index') }}" class="nav-link {{ request()->routeIs('potongan-mahasiswa.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Potongan Mahasiswa</span>
            </a>
            
            <div class="menu-header">Laporan Keuangan</div>
            <a href="{{ route('laporan.dashboard') }}" class="nav-link {{ request()->routeIs('laporan.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard Keuangan</span>
            </a>
            <a href="{{ route('laporan.pendapatan') }}" class="nav-link {{ request()->routeIs('laporan.pendapatan*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Lap. Pendapatan</span>
            </a>
            <a href="{{ route('laporan.tunggakan') }}" class="nav-link {{ request()->routeIs('laporan.tunggakan*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-circle"></i>
                <span>Lap. Tunggakan</span>
            </a>
            <a href="{{ route('laporan.beasiswa') }}" class="nav-link {{ request()->routeIs('laporan.beasiswa*') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i>
                <span>Lap. Beasiswa</span>
            </a>
            
            <div class="menu-header">Laporan Akademik</div>
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.index') || request()->routeIs('laporan.mahasiswa') || request()->routeIs('laporan.nilai') || request()->routeIs('laporan.absensi') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan & Statistik</span>
            </a>
            @endif
            
            @if(auth()->check() && auth()->user()->isDosen())
            <!-- Menu Dosen -->
            <div class="menu-header">Akademik</div>
            <a href="{{ route('jadwal.dosen') }}" class="nav-link {{ request()->routeIs('jadwal.dosen') ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i>
                <span>Jadwal Mengajar</span>
            </a>
            <a href="{{ route('nilai.index') }}" class="nav-link {{ request()->routeIs('nilai.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data"></i>
                <span>Input Nilai</span>
            </a>
            <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Absensi</span>
            </a>
            <a href="{{ route('krs.persetujuan') }}" class="nav-link {{ request()->routeIs('krs.persetujuan') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i>
                <span>Persetujuan KRS</span>
            </a>
            <a href="{{ route('bimbingan.dosen') }}" class="nav-link {{ request()->routeIs('bimbingan.dosen*') || request()->routeIs('bimbingan.persetujuan-krs*') || request()->routeIs('bimbingan.detail-krs*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i>
                <span>Bimbingan Akademik</span>
            </a>
            @endif
            
            @if(auth()->check() && auth()->user()->isMahasiswa())
            <!-- Menu Mahasiswa -->
            <div class="menu-header">Profil</div>
            <a href="{{ route('mahasiswa.profil') }}" class="nav-link {{ request()->routeIs('mahasiswa.profil') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>
            
            <div class="menu-header">Akademik</div>
            <a href="{{ route('krs.index') }}" class="nav-link {{ request()->routeIs('krs.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>KRS</span>
            </a>
            <a href="{{ route('jadwal.mahasiswa') }}" class="nav-link {{ request()->routeIs('jadwal.mahasiswa') ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i>
                <span>Jadwal Kuliah</span>
            </a>
            <a href="{{ route('mahasiswa.jadwal-ujian') }}" class="nav-link {{ request()->routeIs('mahasiswa.jadwal-ujian*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                <span>Jadwal Ujian</span>
            </a>
            <a href="{{ route('mahasiswa.khs') }}" class="nav-link {{ request()->routeIs('mahasiswa.khs*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>KHS</span>
            </a>
            <a href="{{ route('mahasiswa.transkrip') }}" class="nav-link {{ request()->routeIs('mahasiswa.transkrip*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-ruled"></i>
                <span>Transkrip</span>
            </a>
            <a href="{{ route('mahasiswa.kehadiran') }}" class="nav-link {{ request()->routeIs('mahasiswa.kehadiran*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Kehadiran</span>
            </a>
            <a href="{{ route('absensi.mandiri') }}" class="nav-link {{ request()->routeIs('absensi.mandiri') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan"></i>
                <span>Absensi Mandiri</span>
            </a>
            <a href="{{ route('pertemuan.mahasiswa') }}" class="nav-link {{ request()->routeIs('pertemuan.mahasiswa*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Materi Kuliah</span>
            </a>
            
            <div class="menu-header">Layanan</div>
            <a href="{{ route('pengajuan-surat.index') }}" class="nav-link {{ request()->routeIs('pengajuan-surat.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Pengajuan Surat</span>
            </a>
            <a href="{{ route('bimbingan.mahasiswa') }}" class="nav-link {{ request()->routeIs('bimbingan.mahasiswa*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i>
                <span>Bimbingan Akademik</span>
            </a>
            <a href="{{ route('cuti.mahasiswa') }}" class="nav-link {{ request()->routeIs('cuti.mahasiswa*') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i>
                <span>Pengajuan Cuti</span>
            </a>
            
            <div class="menu-header">Keuangan</div>
            <a href="{{ route('pembayaran.mahasiswa') }}" class="nav-link {{ request()->routeIs('pembayaran.mahasiswa') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span>Pembayaran Lama</span>
            </a>
            <a href="{{ route('tagihan.mahasiswa') }}" class="nav-link {{ request()->routeIs('tagihan.mahasiswa') || request()->routeIs('pembayaran.bayar') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Tagihan Saya</span>
            </a>
            <a href="{{ route('transaksi.mahasiswa') }}" class="nav-link {{ request()->routeIs('transaksi.mahasiswa') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Riwayat Pembayaran</span>
            </a>
            <a href="{{ route('cicilan.tracking') }}" class="nav-link {{ request()->routeIs('cicilan.tracking') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i>
                <span>Cicilan Saya</span>
            </a>
            <a href="{{ route('mahasiswa.potongan') }}" class="nav-link {{ request()->routeIs('mahasiswa.potongan') ? 'active' : '' }}">
                <i class="bi bi-percent"></i>
                <span>Potongan Saya</span>
            </a>
            <a href="{{ route('notifikasi.mahasiswa') }}" class="nav-link {{ request()->routeIs('notifikasi.mahasiswa') ? 'active' : '' }}">
                <i class="bi bi-bell"></i>
                <span>Notifikasi</span>
            </a>
            <a href="{{ route('beasiswa.available') }}" class="nav-link {{ request()->routeIs('beasiswa.available') ? 'active' : '' }}">
                <i class="bi bi-award"></i>
                <span>Beasiswa</span>
            </a>
            
            <div class="menu-header">Download</div>
            <a href="{{ route('mahasiswa.download-kartu-tagihan') }}" class="nav-link {{ request()->routeIs('mahasiswa.download-kartu-tagihan') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-pdf"></i>
                <span>Kartu Tagihan</span>
            </a>
            <a href="{{ route('mahasiswa.download-riwayat-pembayaran') }}" class="nav-link {{ request()->routeIs('mahasiswa.download-riwayat-pembayaran') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Riwayat Pembayaran</span>
            </a>
            <a href="{{ route('mahasiswa.download-kartu') }}" class="nav-link {{ request()->routeIs('mahasiswa.download-kartu') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Kartu Mahasiswa</span>
            </a>
            @endif
            
            <div class="menu-header">Informasi</div>
            <a href="{{ route('pengumuman.index') }}" class="nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}">
                <i class="bi bi-megaphone"></i>
                <span>Pengumuman</span>
            </a>
            <a href="{{ route('kalender.index') }}" class="nav-link {{ request()->routeIs('kalender.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                <span>Kalender Akademik</span>
            </a>
            
            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="menu-header">Tools</div>
            <a href="{{ route('import.index') }}" class="nav-link {{ request()->routeIs('import.*') ? 'active' : '' }}">
                <i class="bi bi-upload"></i>
                <span>Import Data</span>
            </a>
            <a href="{{ route('activity-log.index') }}" class="nav-link {{ request()->routeIs('activity-log.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Activity Log</span>
            </a>
            <a href="{{ route('backup.index') }}" class="nav-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                <i class="bi bi-database-down"></i>
                <span>Backup Database</span>
            </a>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>Pengaturan</span>
            </a>
            @endif
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-light d-lg-none me-3" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-none d-md-block">
                    <span class="text-muted">Selamat datang,</span>
                    <strong>{{ auth()->user()->name ?? 'Guest' }}</strong>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Notification Bell -->
                <div class="dropdown">
                    <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown" id="notificationDropdown">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="display: none;">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Notifikasi</span>
                            <a href="{{ route('notifications.markAllRead') }}" class="small text-decoration-none">Tandai Semua</a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div id="notificationList">
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-bell-slash"></i> Tidak ada notifikasi
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('notifications.index') }}" class="dropdown-item text-center small">Lihat Semua Notifikasi</a>
                    </div>
                </div>
                
                <div class="dropdown user-dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline">{{ auth()->user()->name ?? 'Guest' }}</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">{{ ucfirst(auth()->user()->role ?? 'guest') }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Content Area -->
        <div class="content-area">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth < 992) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Notification functions
        function loadNotifications() {
            fetch('{{ route("notifications.recent") }}')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('notificationList');
                    if (data.length === 0) {
                        container.innerHTML = '<div class="text-center py-3 text-muted"><i class="bi bi-bell-slash"></i> Tidak ada notifikasi</div>';
                        return;
                    }
                    
                    let html = '';
                    data.forEach(notif => {
                        const icon = {
                            'success': 'bi-check-circle-fill text-success',
                            'warning': 'bi-exclamation-triangle-fill text-warning',
                            'danger': 'bi-x-circle-fill text-danger',
                            'info': 'bi-info-circle-fill text-info'
                        }[notif.type] || 'bi-info-circle-fill text-info';
                        
                        const unreadClass = notif.is_read ? '' : 'bg-light';
                        html += `
                            <a href="/notifications/${notif.id}/read" class="dropdown-item py-2 ${unreadClass}">
                                <div class="d-flex align-items-start">
                                    <i class="bi ${icon} me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small">${notif.title}</div>
                                        <div class="small text-muted text-truncate" style="max-width: 250px;">${notif.message}</div>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                    container.innerHTML = html;
                });
        }
        
        function updateNotificationCount() {
            fetch('{{ route("notifications.unreadCount") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.notification-badge');
                    if (data.count > 0) {
                        badge.style.display = 'inline-block';
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                    } else {
                        badge.style.display = 'none';
                    }
                });
        }
        
        // Load notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationCount();
            
            // Load notifications when dropdown is opened
            document.getElementById('notificationDropdown')?.addEventListener('show.bs.dropdown', function() {
                loadNotifications();
            });
        });
        
        // Update notification count every 60 seconds
        setInterval(updateNotificationCount, 60000);
    </script>
    
    @stack('scripts')
</body>
</html>
