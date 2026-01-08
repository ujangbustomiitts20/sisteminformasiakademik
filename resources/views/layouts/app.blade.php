<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('app_name', 'NADI ITTS')) - {{ setting('app_description', 'Narasi & Akademik Data Integratif') }}</title>
    
    @if(setting('institution_favicon'))
    <link rel="icon" href="{{ Storage::url(setting('institution_favicon')) }}" type="image/x-icon">
    @endif
    
    <!-- Vite Assets (Bootstrap, Bootstrap Icons, Inter Font) -->
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    
    <style>
        :root {
            --sidebar-width: 270px;
            --sidebar-collapsed-width: 70px;
            --primary-color: {{ setting('primary_color', '#6366f1') }};
            --primary-hover: {{ setting('primary_color', '#6366f1') }}dd;
            --sidebar-bg: linear-gradient(180deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
        }
        
        * {
            transition: all 0.2s ease;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
        }
        
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar-brand .logo-wrapper {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.3) 0%, rgba(139, 92, 246, 0.3) 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        
        .sidebar-brand .logo-wrapper i {
            font-size: 2rem;
            color: #fff;
        }
        
        .sidebar-brand .logo-wrapper img {
            max-height: 50px;
            max-width: 50px;
            object-fit: contain;
        }
        
        .sidebar-brand h4 {
            color: #fff;
            font-weight: 800;
            margin: 0;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.6);
            font-size: 0.7rem;
            margin-top: 0.25rem;
            letter-spacing: 0.5px;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
            position: relative;
        }
        
        .sidebar-menu .menu-header {
            color: rgba(255,255,255,0.4);
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 1.25rem 1.5rem 0.5rem;
            margin-top: 0.5rem;
        }
        
        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
            margin: 2px 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-menu .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.2) 0%, transparent 100%);
            transition: width 0.3s ease;
        }
        
        .sidebar-menu .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.08);
            transform: translateX(4px);
        }
        
        .sidebar-menu .nav-link:hover::before {
            width: 100%;
        }
        
        .sidebar-menu .nav-link.active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.3) 0%, rgba(139, 92, 246, 0.15) 100%);
            color: #fff;
            border-left-color: #818cf8;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }
        
        .sidebar-menu .nav-link.active i {
            color: #a5b4fc;
        }
        
        .sidebar-menu .nav-link i {
            font-size: 1.125rem;
            width: 24px;
            text-align: center;
            transition: transform 0.2s ease;
        }
        
        .sidebar-menu .nav-link:hover i {
            transform: scale(1.1);
        }
        
        /* Collapsible Menu */
        .sidebar-menu .menu-collapse-toggle {
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.5);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0.75rem 0.75rem 0;
            border-radius: 0.5rem;
            transition: all 0.25s ease;
        }
        
        .sidebar-menu .menu-collapse-toggle:hover {
            color: rgba(255,255,255,0.8);
            background: rgba(255,255,255,0.05);
        }
        
        .sidebar-menu .menu-collapse-toggle i.bi-chevron-down {
            font-size: 0.6rem;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-menu .menu-collapse-toggle[aria-expanded="true"] i.bi-chevron-down {
            transform: rotate(180deg);
        }
        
        .sidebar-menu .submenu {
            overflow: hidden;
        }
        
        .sidebar-menu .submenu .nav-link {
            padding-left: 3rem;
            font-size: 0.8125rem;
            color: rgba(255,255,255,0.6);
        }
        
        .sidebar-menu .submenu .nav-link i {
            font-size: 0.875rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f8fafc;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: #fff;
            padding: 0.875rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .top-navbar .navbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .top-navbar .search-box {
            position: relative;
        }
        
        .top-navbar .search-box input {
            background: #f1f5f9;
            border: 1px solid transparent;
            border-radius: 0.75rem;
            padding: 0.625rem 1rem 0.625rem 2.75rem;
            width: 280px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .top-navbar .search-box input:focus {
            background: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }
        
        .top-navbar .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        
        .top-navbar .navbar-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .top-navbar .nav-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            background: transparent;
            border: none;
            position: relative;
            transition: all 0.2s ease;
        }
        
        .top-navbar .nav-icon-btn:hover {
            background: #f1f5f9;
            color: var(--primary-color);
        }
        
        .top-navbar .nav-icon-btn .badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            font-size: 0.65rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        .user-dropdown .btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .user-dropdown .btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #8b5cf6 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }
        
        .user-info {
            text-align: left;
        }
        
        .user-info .user-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.875rem;
            line-height: 1.2;
        }
        
        .user-info .user-role {
            font-size: 0.75rem;
            color: #64748b;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            border-radius: 0.75rem;
            padding: 0.5rem;
            min-width: 200px;
        }
        
        .dropdown-menu .dropdown-item {
            border-radius: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #475569;
        }
        
        .dropdown-menu .dropdown-item:hover {
            background: #f1f5f9;
            color: var(--primary-color);
        }
        
        .dropdown-menu .dropdown-item i {
            width: 18px;
            text-align: center;
        }
        
        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: #e2e8f0;
        }
        
        /* Content Area */
        .content-area {
            padding: 1.5rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            background: #fff;
            overflow: hidden;
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Stats Cards */
        .stat-card {
            border-radius: 1rem;
            padding: 1.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .stat-card.bg-primary { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
        .stat-card.bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-card.bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
        .stat-card.bg-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .stat-card.bg-indigo { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .stat-card.bg-pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
        .stat-card.bg-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
        
        .stat-card .stat-icon {
            font-size: 3rem;
            opacity: 0.2;
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
            position: relative;
        }
        
        .stat-card .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
            position: relative;
        }
        
        /* Buttons */
        .btn {
            border-radius: 0.625rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #8b5cf6 100%);
            border: none;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
            background: linear-gradient(135deg, #5558e8 0%, #7c4ddb 100%);
        }
        
        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* Table */
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f8fafc;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
        }
        
        .table tbody tr:hover {
            background: #fafafb;
        }
        
        /* Badges */
        .badge {
            font-weight: 600;
            padding: 0.375em 0.75em;
            border-radius: 0.5rem;
            font-size: 0.75rem;
        }
        
        /* Page Title */
        .page-title {
            margin-bottom: 1.5rem;
        }
        
        .page-title h4 {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.375rem;
            font-size: 1.5rem;
        }
        
        .page-title p {
            color: #64748b;
            margin-bottom: 0;
            font-size: 0.875rem;
        }
        
        .page-title .breadcrumb {
            margin-bottom: 0;
            font-size: 0.8125rem;
            background: transparent;
            padding: 0;
        }
        
        .page-title .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .page-title .breadcrumb-item.active {
            color: #64748b;
        }
        
        /* Form Elements */
        .form-control, .form-select {
            border-radius: 0.625rem;
            border: 1px solid #e2e8f0;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .form-label {
            font-weight: 500;
            color: #374151;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            font-size: 0.875rem;
        }
        
        .alert-success {
            background: #ecfdf5;
            color: #065f46;
        }
        
        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
        }
        
        .alert-warning {
            background: #fffbeb;
            color: #92400e;
        }
        
        .alert-info {
            background: #eff6ff;
            color: #1e40af;
        }
        
        /* Modal */
        .modal-content {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }
        
        .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
        }
        
        .modal-title {
            font-weight: 700;
            color: #0f172a;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 1rem 1.5rem;
        }
        
        /* Pagination */
        .pagination {
            gap: 0.25rem;
        }
        
        .page-link {
            border-radius: 0.5rem;
            border: none;
            padding: 0.5rem 0.875rem;
            color: #475569;
            font-weight: 500;
            font-size: 0.875rem;
        }
        
        .page-link:hover {
            background: #f1f5f9;
            color: var(--primary-color);
        }
        
        .page-item.active .page-link {
            background: var(--primary-color);
            color: #fff;
        }
        
        /* Mobile toggle button */
        .sidebar-toggle {
            display: none;
            background: transparent;
            border: none;
            padding: 0.5rem;
            color: #64748b;
            font-size: 1.25rem;
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
            
            .sidebar-toggle {
                display: block;
            }
            
            .top-navbar .search-box {
                display: none;
            }
            
            .user-info {
                display: none;
            }
        }
        
        /* Sidebar overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }
        
        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.25);
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .content-area {
            animation: fadeIn 0.3s ease;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="logo-wrapper">
                @php
                    // Prioritas: logo_sidebar > logo_wide > institution_logo
                    $sidebarLogo = setting('institution_logo_sidebar') 
                        ?? setting('institution_logo_wide') 
                        ?? setting('institution_logo');
                @endphp
                @if($sidebarLogo)
                <img src="{{ Storage::url($sidebarLogo) }}" alt="Logo">
                @else
                <i class="bi bi-mortarboard-fill"></i>
                @endif
            </div>
            <h4>{{ setting('app_name', 'NADI ITTS') }}</h4>
            <small>{{ setting('app_description', 'Narasi & Akademik Data Integratif') }}</small>
        </div>
        
        <div class="sidebar-menu">
            {{-- Check if dynamic menu is available --}}
            @php
                $useDynamicMenu = false;
                $userMenus = collect();
                
                if (\Illuminate\Support\Facades\Schema::hasTable('menus') && 
                    \Illuminate\Support\Facades\Schema::hasTable('role_menu') && 
                    auth()->check()) {
                    try {
                        $menuCount = \App\Models\Menu::count();
                        if ($menuCount > 0) {
                            $userMenus = auth()->user()->getMenus();
                            $useDynamicMenu = $userMenus->count() > 0;
                        }
                    } catch (\Exception $e) {
                        $useDynamicMenu = false;
                    }
                }
            @endphp

            @if($useDynamicMenu)
                {{-- Dynamic Menu --}}
                @foreach($userMenus as $menu)
                    @if($menu->is_divider)
                        <div class="menu-header">{{ $menu->nama }}</div>
                    @elseif($menu->children->count() > 0)
                        <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#dynamicMenu{{ $menu->id }}" aria-expanded="{{ $menu->isActive() ? 'true' : 'false' }}">
                            <span>@if($menu->icon)<i class="{{ $menu->icon }} me-2"></i>@endif{{ $menu->nama }}</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="collapse submenu {{ $menu->isActive() ? 'show' : '' }}" id="dynamicMenu{{ $menu->id }}">
                            @foreach($menu->children as $child)
                                <a href="{{ $child->url }}" class="nav-link {{ $child->isActive() ? 'active' : '' }}">
                                    @if($child->icon)<i class="{{ $child->icon }}"></i>@endif
                                    <span>{{ $child->nama }}</span>
                                    @if($child->badge_text)
                                        <span class="badge bg-{{ $child->badge_color ?? 'primary' }} ms-auto">{{ $child->badge_text }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <a href="{{ $menu->url }}" class="nav-link {{ $menu->isActive() ? 'active' : '' }}">
                            @if($menu->icon)<i class="{{ $menu->icon }}"></i>@endif
                            <span>{{ $menu->nama }}</span>
                            @if($menu->badge_text)
                                <span class="badge bg-{{ $menu->badge_color ?? 'primary' }} ms-auto">{{ $menu->badge_text }}</span>
                            @endif
                        </a>
                    @endif
                @endforeach

                {{-- Common menus for all users --}}
                <div class="menu-header">Informasi</div>
                <a href="{{ route('pengumuman.index') }}" class="nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Pengumuman</span>
                </a>
                <a href="{{ route('kalender.index') }}" class="nav-link {{ request()->routeIs('kalender.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i>
                    <span>Kalender Akademik</span>
                </a>
                
                <div class="menu-header">Pengaturan</div>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i>
                    <span>Role & Permission</span>
                </a>
                <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                    <i class="bi bi-list"></i>
                    <span>Manajemen Menu</span>
                </a>
                @endif
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>Pengaturan</span>
                </a>
            @else
            {{-- Static Menu (Fallback) --}}
            @if(auth()->check() && auth()->user()->isAdmin())
            <!-- Menu Admin -->
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="menu-header">Sistem</div>
            <a href="{{ route('user.index') }}" class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Kelola User</span>
            </a>
            <a href="{{ route('admin.pejabat-akademik.index') }}" class="nav-link {{ request()->routeIs('admin.pejabat-akademik.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Pejabat Akademik</span>
            </a>
            <a href="{{ route('admin.nama-jabatan.index') }}" class="nav-link {{ request()->routeIs('admin.nama-jabatan.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i>
                <span>Nama Jabatans</span>
            </a>
            <a href="{{ route('admin.pejabat-penandatangan.index') }}" class="nav-link {{ request()->routeIs('admin.pejabat-penandatangan.*') ? 'active' : '' }}">
                <i class="bi bi-pen"></i>
                <span>Pejabat Penandatangan</span>
            </a>
            <a href="{{ route('admin.template-dokumen.index') }}" class="nav-link {{ request()->routeIs('admin.template-dokumen.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Template Dokumen</span>
            </a>
            
            <div class="menu-header">Master Data</div>
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuMaster" aria-expanded="{{ request()->routeIs('fakultas.*') || request()->routeIs('program-studi.*') || request()->routeIs('ruangan.*') || request()->routeIs('tahun-akademik.*') || request()->routeIs('sekolah.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-database me-2"></i>Master Data</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('fakultas.*') || request()->routeIs('program-studi.*') || request()->routeIs('ruangan.*') || request()->routeIs('tahun-akademik.*') || request()->routeIs('sekolah.*') ? 'show' : '' }}" id="menuMaster">
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
                <a href="{{ route('sekolah.index') }}" class="nav-link {{ request()->routeIs('sekolah.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i>
                    <span>Sekolah Asal</span>
                </a>
            </div>
            
            <div class="menu-header">PMB</div>
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuPmb" aria-expanded="{{ request()->routeIs('pmb.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-person-plus me-2"></i>Penerimaan Mahasiswa</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('pmb.*') ? 'show' : '' }}" id="menuPmb">
                <a href="{{ route('pmb.dashboard') }}" class="nav-link {{ request()->routeIs('pmb.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard PMB</span>
                </a>
                <a href="{{ route('pmb.periode.index') }}" class="nav-link {{ request()->routeIs('pmb.periode.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-range"></i>
                    <span>Periode PMB</span>
                </a>
                <a href="{{ route('pmb.gelombang.index') }}" class="nav-link {{ request()->routeIs('pmb.gelombang.*') ? 'active' : '' }}">
                    <i class="bi bi-layers"></i>
                    <span>Gelombang PMB</span>
                </a>
                <a href="{{ route('pmb.jalur-seleksi.index') }}" class="nav-link {{ request()->routeIs('pmb.jalur-seleksi.*') ? 'active' : '' }}">
                    <i class="bi bi-signpost-split"></i>
                    <span>Jalur Seleksi</span>
                </a>
                <a href="{{ route('pmb.biaya-pendaftaran.index') }}" class="nav-link {{ request()->routeIs('pmb.biaya-pendaftaran.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    <span>Biaya Pendaftaran</span>
                </a>
                <a href="{{ route('pmb.kuota.index') }}" class="nav-link {{ request()->routeIs('pmb.kuota.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i>
                    <span>Kuota PMB</span>
                </a>
                <a href="{{ route('pmb.calon-mahasiswa.index') }}" class="nav-link {{ request()->routeIs('pmb.calon-mahasiswa.*') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i>
                    <span>Calon Mahasiswa</span>
                </a>
                <a href="{{ route('pmb.seleksi.index') }}" class="nav-link {{ request()->routeIs('pmb.seleksi.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Seleksi</span>
                </a>
                <a href="{{ route('pmb.daftar-ulang.index') }}" class="nav-link {{ request()->routeIs('pmb.daftar-ulang.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check"></i>
                    <span>Daftar Ulang</span>
                </a>
                <a href="{{ route('pmb.konten-pmb.pengaturan') }}" class="nav-link {{ request()->routeIs('pmb.konten-pmb.*') ? 'active' : '' }}">
                    <i class="bi bi-globe"></i>
                    <span>Konten Portal PMB</span>
                </a>
            </div>
            
            <div class="menu-header">Akademik</div>
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuAkademik" aria-expanded="{{ request()->routeIs('akademik.*') || request()->routeIs('mahasiswa.*') || request()->routeIs('dosen.*') || request()->routeIs('mata-kuliah.*') || request()->routeIs('kurikulum.*') || request()->routeIs('jadwal-kuliah.*') || request()->routeIs('nilai.*') || request()->routeIs('absensi.*') || request()->routeIs('wisuda.*') || request()->routeIs('yudisium.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-mortarboard me-2"></i>Data Akademik</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('akademik.*') || request()->routeIs('mahasiswa.*') || request()->routeIs('dosen.*') || request()->routeIs('mata-kuliah.*') || request()->routeIs('kurikulum.*') || request()->routeIs('jadwal-kuliah.*') || request()->routeIs('nilai.*') || request()->routeIs('absensi.*') || request()->routeIs('wisuda.*') || request()->routeIs('yudisium.*') || request()->routeIs('prasyarat.*') || request()->routeIs('bimbingan.*') || request()->routeIs('cuti.*') || request()->routeIs('pertemuan.*') || request()->routeIs('jadwal-pengganti.*') || request()->routeIs('admin.edom.*') || request()->routeIs('admin.periode-ujian.*') || request()->routeIs('admin.kartu-ujian.*') ? 'show' : '' }}" id="menuAkademik">
                <a href="{{ route('akademik.dashboard') }}" class="nav-link {{ request()->routeIs('akademik.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard Akademik</span>
                </a>
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
                <a href="{{ route('jadwal-pengganti.index') }}" class="nav-link {{ request()->routeIs('jadwal-pengganti.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-range"></i>
                    <span>Jadwal Pengganti</span>
                </a>
                <a href="{{ route('wisuda.index') }}" class="nav-link {{ request()->routeIs('wisuda.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span>Wisuda</span>
                </a>
                <a href="{{ route('yudisium.index') }}" class="nav-link {{ request()->routeIs('yudisium.*') ? 'active' : '' }}">
                    <i class="bi bi-award-fill"></i>
                    <span>Yudisium</span>
                </a>
                <a href="{{ route('admin.edom.index') }}" class="nav-link {{ request()->routeIs('admin.edom.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard2-pulse"></i>
                    <span>EDOM</span>
                </a>
                <a href="{{ route('admin.periode-ujian.index') }}" class="nav-link {{ request()->routeIs('admin.periode-ujian.*') || request()->routeIs('admin.kartu-ujian.*') ? 'active' : '' }}">
                    <i class="bi bi-card-checklist"></i>
                    <span>Kartu Peserta Ujian</span>
                </a>
            </div>
            
            <div class="menu-header">Tugas Akhir & Magang</div>
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuTA" aria-expanded="{{ request()->routeIs('admin.konversi-nilai.*') || request()->routeIs('admin.konversi-kegiatan.*') || request()->routeIs('admin.kegiatan-lapangan.*') || request()->routeIs('admin.tugas-akhir.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-journal-bookmark me-2"></i>TA/PKL/Magang</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('admin.konversi-nilai.*') || request()->routeIs('admin.konversi-kegiatan.*') || request()->routeIs('admin.kegiatan-lapangan.*') || request()->routeIs('admin.tugas-akhir.*') ? 'show' : '' }}" id="menuTA">
                <a href="{{ route('admin.konversi-nilai.index') }}" class="nav-link {{ request()->routeIs('admin.konversi-nilai.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Konversi Nilai</span>
                </a>
                <a href="{{ route('admin.konversi-kegiatan.index') }}" class="nav-link {{ request()->routeIs('admin.konversi-kegiatan.*') ? 'active' : '' }}">
                    <i class="bi bi-award"></i>
                    <span>Konversi Kegiatan</span>
                </a>
                <a href="{{ route('admin.kegiatan-lapangan.jenis.index') }}" class="nav-link {{ request()->routeIs('admin.kegiatan-lapangan.jenis.*') ? 'active' : '' }}">
                    <i class="bi bi-list-ul"></i>
                    <span>Jenis PKL/Magang/KKN</span>
                </a>
                <a href="{{ route('admin.kegiatan-lapangan.mitra.index') }}" class="nav-link {{ request()->routeIs('admin.kegiatan-lapangan.mitra.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>Mitra Kegiatan</span>
                </a>
                <a href="{{ route('admin.kegiatan-lapangan.periode.index') }}" class="nav-link {{ request()->routeIs('admin.kegiatan-lapangan.periode.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar3"></i>
                    <span>Periode Kegiatan</span>
                </a>
                <a href="{{ route('admin.kegiatan-lapangan.pendaftaran.index') }}" class="nav-link {{ request()->routeIs('admin.kegiatan-lapangan.pendaftaran.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Pendaftaran PKL/Magang</span>
                </a>
                <a href="{{ route('admin.tugas-akhir.index') }}" class="nav-link {{ request()->routeIs('admin.tugas-akhir.index') || request()->routeIs('admin.tugas-akhir.show') || request()->routeIs('admin.tugas-akhir.edit') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Tugas Akhir</span>
                </a>
                <a href="{{ route('admin.tugas-akhir.seminar.index') }}" class="nav-link {{ request()->routeIs('admin.tugas-akhir.seminar.*') ? 'active' : '' }}">
                    <i class="bi bi-easel"></i>
                    <span>Seminar Proposal</span>
                </a>
                <a href="{{ route('admin.tugas-akhir.sidang.index') }}" class="nav-link {{ request()->routeIs('admin.tugas-akhir.sidang.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i>
                    <span>Sidang TA</span>
                </a>
            </div>
            
            <div class="menu-header">Kepegawaian</div>
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuKepegawaian" aria-expanded="{{ request()->routeIs('kepegawaian.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-person-badge me-2"></i>SDM & Pegawai</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('kepegawaian.*') ? 'show' : '' }}" id="menuKepegawaian">
                <a href="{{ route('kepegawaian.dashboard') }}" class="nav-link {{ request()->routeIs('kepegawaian.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('kepegawaian.data-pegawai.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.data-pegawai.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Data Pegawai</span>
                </a>
                <a href="{{ route('kepegawaian.unit-kerja.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.unit-kerja.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>Unit Kerja</span>
                </a>
                <a href="{{ route('kepegawaian.cuti.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.cuti.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-x"></i>
                    <span>Cuti Pegawai</span>
                </a>
                <a href="{{ route('kepegawaian.presensi.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.presensi.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Presensi/Kehadiran</span>
                </a>
                <a href="{{ route('kepegawaian.penugasan.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.penugasan.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Penugasan/Mutasi</span>
                </a>
                <a href="{{ route('kepegawaian.kgb.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.kgb.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    <span>Kenaikan Gaji Berkala</span>
                </a>
                <a href="{{ route('kepegawaian.kenaikan-pangkat.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.kenaikan-pangkat.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Kenaikan Pangkat</span>
                </a>
                <a href="{{ route('kepegawaian.pensiun.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.pensiun.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check"></i>
                    <span>Pensiun</span>
                </a>
                <a href="{{ route('kepegawaian.kontrak.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.kontrak.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Kontrak Kerja</span>
                </a>
                <a href="{{ route('kepegawaian.evaluasi.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.evaluasi.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Evaluasi Kinerja</span>
                </a>
                <a href="{{ route('kepegawaian.sertifikasi.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.sertifikasi.*') ? 'active' : '' }}">
                    <i class="bi bi-award"></i>
                    <span>Sertifikasi Dosen</span>
                </a>
                <a href="{{ route('kepegawaian.pelanggaran.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.pelanggaran.*') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-octagon"></i>
                    <span>Pelanggaran & Sanksi</span>
                </a>
                <a href="{{ route('kepegawaian.izin-keluar.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.izin-keluar.*') ? 'active' : '' }}">
                    <i class="bi bi-door-open"></i>
                    <span>Izin Keluar</span>
                </a>
                <a href="{{ route('kepegawaian.lembur.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.lembur.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Lembur</span>
                </a>
                <a href="{{ route('kepegawaian.slip-gaji.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.slip-gaji.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Slip Gaji</span>
                </a>
                <a href="{{ route('kepegawaian.skp.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.skp.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check"></i>
                    <span>SKP Pegawai</span>
                </a>
                <a href="{{ route('kepegawaian.aktivitas-harian.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.aktivitas-harian.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Aktivitas Harian</span>
                </a>
                <a href="{{ route('kepegawaian.uraian-kegiatan-skp.index') }}" class="nav-link {{ request()->routeIs('kepegawaian.uraian-kegiatan-skp.*') ? 'active' : '' }}">
                    <i class="bi bi-list-check"></i>
                    <span>Master Uraian SKP</span>
                </a>
            </div>
            
            <div class="menu-header">Keuangan</div>
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuKeuangan" aria-expanded="{{ request()->routeIs('keuangan.*') || request()->routeIs('tarif.*') || request()->routeIs('tagihan.*') || request()->routeIs('transaksi-pembayaran.*') || request()->routeIs('beasiswa.*') || request()->routeIs('pengaturan-denda.*') || request()->routeIs('skema-cicilan.*') || request()->routeIs('cicilan.*') || request()->routeIs('notifikasi.*') || request()->routeIs('pmb.pembayaran.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-wallet2 me-2"></i>Keuangan</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('keuangan.*') || request()->routeIs('tarif.*') || request()->routeIs('tagihan.*') || request()->routeIs('transaksi-pembayaran.*') || request()->routeIs('beasiswa.*') || request()->routeIs('pengaturan-denda.*') || request()->routeIs('skema-cicilan.*') || request()->routeIs('cicilan.*') || request()->routeIs('notifikasi.*') || request()->routeIs('pmb.pembayaran.*') ? 'show' : '' }}" id="menuKeuangan">
                <a href="{{ route('keuangan.dashboard') }}" class="nav-link {{ request()->routeIs('keuangan.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Dashboard Keuangan</span>
                </a>
                <a href="{{ route('pmb.pembayaran.index') }}" class="nav-link {{ request()->routeIs('pmb.pembayaran.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front"></i>
                    <span>Pembayaran PMB</span>
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
            </div>
            
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuBank" aria-expanded="{{ request()->routeIs('akun-bank.*') || request()->routeIs('mutasi-bank.*') || request()->routeIs('rekonsiliasi.*') || request()->routeIs('keuangan.refund.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-bank me-2"></i>Rekonsiliasi Bank</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('akun-bank.*') || request()->routeIs('mutasi-bank.*') || request()->routeIs('rekonsiliasi.*') || request()->routeIs('keuangan.refund.*') ? 'show' : '' }}" id="menuBank">
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
            </div>
            
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuPotongan" aria-expanded="{{ request()->routeIs('jenis-potongan.*') || request()->routeIs('periode-diskon.*') || request()->routeIs('potongan-mahasiswa.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-percent me-2"></i>Potongan & Diskon</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('jenis-potongan.*') || request()->routeIs('periode-diskon.*') || request()->routeIs('potongan-mahasiswa.*') ? 'show' : '' }}" id="menuPotongan">
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
            </div>
            
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuLaporanKeuangan" aria-expanded="{{ request()->routeIs('laporan.pendapatan*') || request()->routeIs('laporan.tunggakan*') || request()->routeIs('laporan.beasiswa*') ? 'true' : 'false' }}">
                <span><i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Keuangan</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('laporan.pendapatan*') || request()->routeIs('laporan.tunggakan*') || request()->routeIs('laporan.beasiswa*') ? 'show' : '' }}" id="menuLaporanKeuangan">
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
            </div>
            
            <div class="menu-header">Laporan Akademik</div>
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.index') || request()->routeIs('laporan.mahasiswa') || request()->routeIs('laporan.nilai') || request()->routeIs('laporan.absensi') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan & Statistik</span>
            </a>
            
            <div class="menu-header">Tools</div>
            <div class="menu-collapse-toggle" data-bs-toggle="collapse" data-bs-target="#menuTools" aria-expanded="{{ request()->routeIs('import.*') || request()->routeIs('activity-log.*') || request()->routeIs('backup.*') || request()->routeIs('konfigurasi-cetak.*') || request()->routeIs('settings.*') ? 'true' : 'false' }}">
                <span><i class="bi bi-gear me-2"></i>Tools & Pengaturan</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="collapse submenu {{ request()->routeIs('import.*') || request()->routeIs('activity-log.*') || request()->routeIs('backup.*') || request()->routeIs('konfigurasi-cetak.*') || request()->routeIs('settings.*') ? 'show' : '' }}" id="menuTools">
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
                <a href="{{ route('konfigurasi-cetak.index') }}" class="nav-link {{ request()->routeIs('konfigurasi-cetak.*') ? 'active' : '' }}">
                    <i class="bi bi-printer"></i>
                    <span>Konfigurasi Cetak</span>
                </a>
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>Pengaturan</span>
                </a>
            </div>
            @endif
            
            @if(auth()->check() && auth()->user()->isDosen())
            <!-- Menu Dosen -->
            @if(auth()->user()->role !== 'dosen' && auth()->user()->dosen)
            <div class="menu-header">
                <span class="badge bg-success">Portal Dosen</span>
            </div>
            @endif
            <a href="{{ route('dosen.dashboard') }}" class="nav-link {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="menu-header">Profil & Kepegawaian</div>
            <a href="{{ route('dosen.profil') }}" class="nav-link {{ request()->routeIs('dosen.profil*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>
            <a href="{{ route('dosen.kepegawaian') }}" class="nav-link {{ request()->routeIs('dosen.kepegawaian') ? 'active' : '' }}">
                <i class="bi bi-folder-check"></i>
                <span>Data Kepegawaian</span>
            </a>
            <a href="{{ route('dosen.presensi.index') }}" class="nav-link {{ request()->routeIs('dosen.presensi.*') ? 'active' : '' }}">
                <i class="bi bi-fingerprint"></i>
                <span>Presensi Dosen</span>
            </a>
            <a href="{{ route('dosen.izin-keluar.index') }}" class="nav-link {{ request()->routeIs('dosen.izin-keluar.*') ? 'active' : '' }}">
                <i class="bi bi-door-open"></i>
                <span>Izin Keluar</span>
            </a>
            <a href="{{ route('dosen.lembur.index') }}" class="nav-link {{ request()->routeIs('dosen.lembur.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Pengajuan Lembur</span>
            </a>
            <a href="{{ route('dosen.cuti.index') }}" class="nav-link {{ request()->routeIs('dosen.cuti.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i>
                <span>Pengajuan Cuti</span>
            </a>
            <a href="{{ route('dosen.cuti.saldo') }}" class="nav-link {{ request()->routeIs('dosen.cuti.saldo') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Saldo Cuti</span>
            </a>
            <a href="{{ route('dosen.slip-gaji.index') }}" class="nav-link {{ request()->routeIs('dosen.slip-gaji.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Slip Gaji</span>
            </a>
            <a href="{{ route('dosen.skp.index') }}" class="nav-link {{ request()->routeIs('dosen.skp.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check"></i>
                <span>SKP / Kinerja</span>
            </a>
            <a href="{{ route('dosen.aktivitas-harian.index') }}" class="nav-link {{ request()->routeIs('dosen.aktivitas-harian.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Aktivitas Harian</span>
            </a>
            
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
            
            <div class="menu-header">Rekap & Laporan</div>
            <a href="{{ route('dosen.rekap-absensi') }}" class="nav-link {{ request()->routeIs('dosen.rekap-absensi') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Rekap Absensi</span>
            </a>
            <a href="{{ route('dosen.rekap-nilai') }}" class="nav-link {{ request()->routeIs('dosen.rekap-nilai') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>Rekap Nilai</span>
            </a>
            <a href="{{ route('dosen.mahasiswa-wali') }}" class="nav-link {{ request()->routeIs('dosen.mahasiswa-wali*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Mahasiswa Perwalian</span>
            </a>
            <a href="{{ route('dosen.edom.index') }}" class="nav-link {{ request()->routeIs('dosen.edom.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i>
                <span>Hasil EDOM</span>
            </a>
            
            <div class="menu-header">Tugas Akhir</div>
            <a href="{{ route('dosen.tugas-akhir.index') }}" class="nav-link {{ request()->routeIs('dosen.tugas-akhir.index') || request()->routeIs('dosen.tugas-akhir.show') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark"></i>
                <span>Bimbingan TA</span>
            </a>
            @php
                $jadwalBimbinganCount = 0;
                if(auth()->user()->dosen) {
                    $jadwalBimbinganCount = \App\Models\BimbinganTA::where('dosen_id', auth()->user()->dosen->id)
                        ->where('status', 'dijadwalkan')
                        ->count();
                }
            @endphp
            <a href="{{ route('dosen.tugas-akhir.jadwal-bimbingan') }}" class="nav-link {{ request()->routeIs('dosen.tugas-akhir.jadwal-bimbingan') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                <span>Jadwal Bimbingan</span>
                @if($jadwalBimbinganCount > 0)
                <span class="badge bg-danger ms-auto">{{ $jadwalBimbinganCount }}</span>
                @endif
            </a>
            <a href="{{ route('dosen.tugas-akhir.riwayat-bimbingan') }}" class="nav-link {{ request()->routeIs('dosen.tugas-akhir.riwayat-bimbingan') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Bimbingan</span>
            </a>
            <a href="{{ route('dosen.tugas-akhir.seminar-penguji') }}" class="nav-link {{ request()->routeIs('dosen.tugas-akhir.seminar-penguji*') ? 'active' : '' }}">
                <i class="bi bi-easel"></i>
                <span>Seminar (Penguji)</span>
            </a>
            <a href="{{ route('dosen.tugas-akhir.sidang-penguji') }}" class="nav-link {{ request()->routeIs('dosen.tugas-akhir.sidang-penguji*') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i>
                <span>Sidang TA (Penguji)</span>
            </a>
            @endif
            
            @if(auth()->check() && auth()->user()->isMahasiswa())
            <!-- Menu Mahasiswa -->
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('mahasiswa.dashboard.akademik') }}" class="nav-link {{ request()->routeIs('mahasiswa.dashboard.akademik') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i>
                <span>Dashboard Akademik</span>
            </a>
            <a href="{{ route('mahasiswa.dashboard.keuangan') }}" class="nav-link {{ request()->routeIs('mahasiswa.dashboard.keuangan') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Dashboard Keuangan</span>
            </a>
            
            <div class="menu-header">Profil & Dokumen</div>
            <a href="{{ route('mahasiswa.profil') }}" class="nav-link {{ request()->routeIs('mahasiswa.profil*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>
            <a href="{{ route('mahasiswa.kartu') }}" class="nav-link {{ request()->routeIs('mahasiswa.kartu*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Kartu Mahasiswa</span>
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
            <a href="{{ route('mahasiswa.khs') }}" class="nav-link {{ request()->routeIs('mahasiswa.khs*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>KHS</span>
            </a>
            <a href="{{ route('mahasiswa.transkrip') }}" class="nav-link {{ request()->routeIs('mahasiswa.transkrip*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-ruled"></i>
                <span>Transkrip Nilai</span>
            </a>
            
            <div class="menu-header">Perkuliahan</div>
            <a href="{{ route('mahasiswa.kehadiran') }}" class="nav-link {{ request()->routeIs('mahasiswa.kehadiran*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Rekap Kehadiran</span>
            </a>
            <a href="{{ route('absensi.mandiri') }}" class="nav-link {{ request()->routeIs('absensi.mandiri') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan"></i>
                <span>Absensi Mandiri</span>
            </a>
            <a href="{{ route('pertemuan.mahasiswa') }}" class="nav-link {{ request()->routeIs('pertemuan.mahasiswa*') ? 'active' : '' }}">
                <i class="bi bi-book"></i>
                <span>Materi Kuliah</span>
            </a>
            <a href="{{ route('mahasiswa.edom.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.edom.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-pulse"></i>
                <span>Evaluasi Dosen</span>
            </a>
            
            <div class="menu-header">Ujian</div>
            <a href="{{ route('mahasiswa.jadwal-ujian') }}" class="nav-link {{ request()->routeIs('mahasiswa.jadwal-ujian*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>
                <span>Jadwal Ujian</span>
            </a>
            <a href="{{ route('mahasiswa.kartu-ujian.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.kartu-ujian.*') ? 'active' : '' }}">
                <i class="bi bi-card-checklist"></i>
                <span>Kartu Ujian</span>
            </a>
            
            <div class="menu-header">Tugas Akhir & Magang</div>
            <a href="{{ route('mahasiswa.tugas-akhir.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.tugas-akhir.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark"></i>
                <span>Tugas Akhir</span>
            </a>
            <a href="{{ route('mahasiswa.kegiatan-lapangan.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.kegiatan-lapangan.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i>
                <span>PKL/Magang/KKN</span>
            </a>
            <a href="{{ route('konversi-kegiatan.index') }}" class="nav-link {{ request()->routeIs('konversi-kegiatan.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-repeat"></i>
                <span>Konversi Kegiatan</span>
            </a>
            
            <div class="menu-header">Layanan Akademik</div>
            <a href="{{ route('bimbingan.mahasiswa') }}" class="nav-link {{ request()->routeIs('bimbingan.mahasiswa*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i>
                <span>Bimbingan Akademik</span>
            </a>
            <a href="{{ route('pengajuan-surat.index') }}" class="nav-link {{ request()->routeIs('pengajuan-surat.*') ? 'active' : '' }}">
                <i class="bi bi-envelope"></i>
                <span>Pengajuan Surat</span>
            </a>
            <a href="{{ route('cuti.mahasiswa') }}" class="nav-link {{ request()->routeIs('cuti.mahasiswa*') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i>
                <span>Pengajuan Cuti</span>
            </a>
            
            <div class="menu-header">Keuangan</div>
            <a href="{{ route('tagihan.mahasiswa') }}" class="nav-link {{ request()->routeIs('tagihan.mahasiswa') || request()->routeIs('pembayaran.bayar') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Tagihan Saya</span>
            </a>
            <a href="{{ route('transaksi.mahasiswa') }}" class="nav-link {{ request()->routeIs('transaksi.mahasiswa') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Riwayat Pembayaran</span>
            </a>
            <a href="{{ route('cicilan.tracking') }}" class="nav-link {{ request()->routeIs('cicilan.tracking') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-front"></i>
                <span>Cicilan</span>
            </a>
            <a href="{{ route('mahasiswa.potongan') }}" class="nav-link {{ request()->routeIs('mahasiswa.potongan') ? 'active' : '' }}">
                <i class="bi bi-percent"></i>
                <span>Potongan/Diskon</span>
            </a>
            <a href="{{ route('beasiswa.available') }}" class="nav-link {{ request()->routeIs('beasiswa.available') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i>
                <span>Beasiswa</span>
            </a>
            
            <div class="menu-header">Cetak Dokumen</div>
            <a href="{{ route('mahasiswa.tagihan') }}" class="nav-link {{ request()->routeIs('mahasiswa.tagihan*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-pdf"></i>
                <span>Kartu Tagihan</span>
            </a>
            <a href="{{ route('mahasiswa.rekap-pembayaran') }}" class="nav-link {{ request()->routeIs('mahasiswa.rekap-pembayaran*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Rekap Pembayaran</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->isKaprodi())
            <!-- Menu Ketua Prodi -->
            @if(auth()->user()->role === 'dosen' && auth()->user()->dosen)
            <div class="menu-header">
                <span class="badge bg-info">Kaprodi</span>
                @php $prodiKaprodi = auth()->user()->getProdiKaprodi(); @endphp
                @if($prodiKaprodi)
                <small class="text-muted d-block">{{ $prodiKaprodi->nama }}</small>
                @endif
            </div>
            @endif
            <a href="{{ route('kaprodi.dashboard') }}" class="nav-link {{ request()->routeIs('kaprodi.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="menu-header">Data Prodi</div>
            <a href="{{ route('kaprodi.mahasiswa.index') }}" class="nav-link {{ request()->routeIs('kaprodi.mahasiswa.index', 'kaprodi.mahasiswa.show') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Mahasiswa</span>
            </a>
            <a href="{{ route('kaprodi.mahasiswa.bermasalah') }}" class="nav-link {{ request()->routeIs('kaprodi.mahasiswa.bermasalah') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Mahasiswa Bermasalah</span>
            </a>
            <a href="{{ route('kaprodi.dosen.index') }}" class="nav-link {{ request()->routeIs('kaprodi.dosen.*') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i>
                <span>Dosen</span>
            </a>
            <a href="{{ route('kaprodi.kurikulum.index') }}" class="nav-link {{ request()->routeIs('kaprodi.kurikulum.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Kurikulum</span>
            </a>
            <a href="{{ route('kaprodi.mata-kuliah.index') }}" class="nav-link {{ request()->routeIs('kaprodi.mata-kuliah.*') ? 'active' : '' }}">
                <i class="bi bi-book"></i>
                <span>Mata Kuliah</span>
            </a>
            <a href="{{ route('kaprodi.jadwal.index') }}" class="nav-link {{ request()->routeIs('kaprodi.jadwal.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i>
                <span>Jadwal Kuliah</span>
            </a>
            
            <div class="menu-header">Approval & Monitoring</div>
            <a href="{{ route('kaprodi.approval.dashboard') }}" class="nav-link {{ request()->routeIs('kaprodi.approval.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Approval Kepegawaian</span>
            </a>
            <a href="{{ route('kaprodi.krs.index') }}" class="nav-link {{ request()->routeIs('kaprodi.krs.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i>
                <span>Persetujuan KRS</span>
            </a>
            <a href="{{ route('kaprodi.tugas-akhir.index') }}" class="nav-link {{ request()->routeIs('kaprodi.tugas-akhir.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark"></i>
                <span>Tugas Akhir</span>
            </a>
            <a href="{{ route('kaprodi.konversi-nilai.index') }}" class="nav-link {{ request()->routeIs('kaprodi.konversi-nilai.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Konversi Nilai</span>
            </a>
            <a href="{{ route('kaprodi.cuti.index') }}" class="nav-link {{ request()->routeIs('kaprodi.cuti.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i>
                <span>Cuti Akademik</span>
            </a>
            <a href="{{ route('kaprodi.pkl.index') }}" class="nav-link {{ request()->routeIs('kaprodi.pkl.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i>
                <span>PKL/Magang</span>
            </a>
            <a href="{{ route('kaprodi.bimbingan.index') }}" class="nav-link {{ request()->routeIs('kaprodi.bimbingan.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i>
                <span>Bimbingan Akademik</span>
            </a>
            <a href="{{ route('kaprodi.absensi.index') }}" class="nav-link {{ request()->routeIs('kaprodi.absensi.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Monitoring Absensi</span>
            </a>
            <a href="{{ route('kaprodi.jadwal-ujian.index') }}" class="nav-link {{ request()->routeIs('kaprodi.jadwal-ujian.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Jadwal Ujian</span>
            </a>
            
            <div class="menu-header">Nilai & Akademik</div>
            <a href="{{ route('kaprodi.nilai.rekap') }}" class="nav-link {{ request()->routeIs('kaprodi.nilai.rekap') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Rekap Nilai</span>
            </a>
            <a href="{{ route('kaprodi.nilai.monitoring-ipk') }}" class="nav-link {{ request()->routeIs('kaprodi.nilai.monitoring-ipk') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>Monitoring IPK</span>
            </a>
            <a href="{{ route('kaprodi.edom.index') }}" class="nav-link {{ request()->routeIs('kaprodi.edom.*') ? 'active' : '' }}">
                <i class="bi bi-star"></i>
                <span>Hasil EDOM</span>
            </a>
            <a href="{{ route('kaprodi.wisuda.index') }}" class="nav-link {{ request()->routeIs('kaprodi.wisuda.*') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i>
                <span>Pendaftar Wisuda</span>
            </a>
            <a href="{{ route('kaprodi.yudisium.index') }}" class="nav-link {{ request()->routeIs('kaprodi.yudisium.*') ? 'active' : '' }}">
                <i class="bi bi-award"></i>
                <span>Yudisium</span>
            </a>
            
            <div class="menu-header">Laporan</div>
            <a href="{{ route('kaprodi.statistik') }}" class="nav-link {{ request()->routeIs('kaprodi.statistik') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i>
                <span>Statistik Prodi</span>
            </a>
            <a href="{{ route('kaprodi.laporan.index') }}" class="nav-link {{ request()->routeIs('kaprodi.laporan.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan Akademik</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->isDekan())
            <!-- Menu Dekan -->
            @if(auth()->user()->role === 'dosen' && auth()->user()->dosen)
            <div class="menu-header">
                <span class="badge bg-warning text-dark">Dekan</span>
                @php $fakultasDekan = auth()->user()->getFakultasDekan(); @endphp
                @if($fakultasDekan)
                <small class="text-muted d-block">{{ $fakultasDekan->nama }}</small>
                @endif
            </div>
            @endif
            <a href="{{ route('dekan.dashboard') }}" class="nav-link {{ request()->routeIs('dekan.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="menu-header">Data Fakultas</div>
            <a href="{{ route('dekan.program-studi.index') }}" class="nav-link {{ request()->routeIs('dekan.program-studi.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>Program Studi</span>
            </a>
            <a href="{{ route('dekan.mahasiswa.index') }}" class="nav-link {{ request()->routeIs('dekan.mahasiswa.index', 'dekan.mahasiswa.show') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Mahasiswa</span>
            </a>
            <a href="{{ route('dekan.mahasiswa.bermasalah') }}" class="nav-link {{ request()->routeIs('dekan.mahasiswa.bermasalah') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Mahasiswa Bermasalah</span>
            </a>
            <a href="{{ route('dekan.dosen.index') }}" class="nav-link {{ request()->routeIs('dekan.dosen.*') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i>
                <span>Dosen</span>
            </a>
            
            <div class="menu-header">Akademik</div>
            <a href="{{ route('dekan.kurikulum.index') }}" class="nav-link {{ request()->routeIs('dekan.kurikulum.*') ? 'active' : '' }}">
                <i class="bi bi-book"></i>
                <span>Kurikulum</span>
            </a>
            <a href="{{ route('dekan.mata-kuliah.index') }}" class="nav-link {{ request()->routeIs('dekan.mata-kuliah.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Mata Kuliah</span>
            </a>
            <a href="{{ route('dekan.jadwal.index') }}" class="nav-link {{ request()->routeIs('dekan.jadwal.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Jadwal Kuliah</span>
            </a>
            <a href="{{ route('dekan.jadwal-ujian.index') }}" class="nav-link {{ request()->routeIs('dekan.jadwal-ujian.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Jadwal Ujian</span>
            </a>
            <a href="{{ route('dekan.absensi.index') }}" class="nav-link {{ request()->routeIs('dekan.absensi.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Absensi</span>
            </a>
            <a href="{{ route('dekan.nilai.rekap') }}" class="nav-link {{ request()->routeIs('dekan.nilai.rekap') ? 'active' : '' }}">
                <i class="bi bi-card-checklist"></i>
                <span>Rekap Nilai</span>
            </a>
            <a href="{{ route('dekan.nilai.monitoring-ipk') }}" class="nav-link {{ request()->routeIs('dekan.nilai.monitoring-ipk') ? 'active' : '' }}">
                <i class="bi bi-speedometer"></i>
                <span>Monitoring IPK</span>
            </a>
            <a href="{{ route('dekan.bimbingan.index') }}" class="nav-link {{ request()->routeIs('dekan.bimbingan.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i>
                <span>Bimbingan Akademik</span>
            </a>
            <a href="{{ route('dekan.edom.index') }}" class="nav-link {{ request()->routeIs('dekan.edom.*') ? 'active' : '' }}">
                <i class="bi bi-star"></i>
                <span>EDOM</span>
            </a>
            
            <div class="menu-header">Approval & Monitoring</div>
            <a href="{{ route('dekan.cuti.index') }}" class="nav-link {{ request()->routeIs('dekan.cuti.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i>
                <span>Cuti Akademik</span>
            </a>
            <a href="{{ route('dekan.tugas-akhir.index') }}" class="nav-link {{ request()->routeIs('dekan.tugas-akhir.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark"></i>
                <span>Tugas Akhir</span>
            </a>
            <a href="{{ route('dekan.pkl.index') }}" class="nav-link {{ request()->routeIs('dekan.pkl.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>PKL/Magang</span>
            </a>
            <a href="{{ route('dekan.konversi-nilai.index') }}" class="nav-link {{ request()->routeIs('dekan.konversi-nilai.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Konversi Nilai</span>
            </a>
            <a href="{{ route('dekan.yudisium.index') }}" class="nav-link {{ request()->routeIs('dekan.yudisium.*') ? 'active' : '' }}">
                <i class="bi bi-award"></i>
                <span>Yudisium</span>
            </a>
            <a href="{{ route('dekan.wisuda.index') }}" class="nav-link {{ request()->routeIs('dekan.wisuda.*') ? 'active' : '' }}">
                <i class="bi bi-mortarboard-fill"></i>
                <span>Wisuda</span>
            </a>
            
            <div class="menu-header">Dokumen & Export</div>
            <a href="{{ route('dekan.dokumen.index') }}" class="nav-link {{ request()->routeIs('dekan.dokumen.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Tanda Tangan Surat</span>
            </a>
            <a href="{{ route('dekan.export.mahasiswa') }}" class="nav-link {{ request()->routeIs('dekan.export.mahasiswa') ? 'active' : '' }}">
                <i class="bi bi-download"></i>
                <span>Export Mahasiswa</span>
            </a>
            <a href="{{ route('dekan.export.nilai') }}" class="nav-link {{ request()->routeIs('dekan.export.nilai') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Export Nilai</span>
            </a>
            
            <div class="menu-header">Laporan & Statistik</div>
            <a href="{{ route('dekan.laporan.index') }}" class="nav-link {{ request()->routeIs('dekan.laporan.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan Fakultas</span>
            </a>
            <a href="{{ route('dekan.statistik.index') }}" class="nav-link {{ request()->routeIs('dekan.statistik.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>Statistik Akademik</span>
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
            @endif {{-- End of static menu fallback --}}
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="navbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="search-box d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Cari menu, fitur..." id="globalSearch">
                </div>
            </div>
            
            <div class="navbar-right">
                <!-- Notification Bell -->
                <div class="dropdown">
                    <button class="nav-icon-btn" type="button" data-bs-toggle="dropdown" id="notificationDropdown">
                        <i class="bi bi-bell"></i>
                        <span class="badge bg-danger notification-badge" style="display: none;">0</span>
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
                
                <!-- Settings -->
                @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('settings.index') }}" class="nav-icon-btn" title="Pengaturan">
                    <i class="bi bi-gear"></i>
                </a>
                @endif
                
                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()->name ?? 'Guest' }}</div>
                            <div class="user-role">{{ ucfirst(auth()->user()->role ?? 'guest') }}</div>
                        </div>
                        <i class="bi bi-chevron-down ms-2" style="font-size: 0.75rem; color: #94a3b8;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <div class="px-3 py-2">
                                <div class="fw-semibold text-dark">{{ auth()->user()->name ?? 'Guest' }}</div>
                                <div class="text-muted small">{{ auth()->user()->email ?? '' }}</div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person"></i>Profil Saya</a></li>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="bi bi-gear"></i>Pengaturan</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i>Logout
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
    
    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        sidebarToggle?.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
        
        sidebarOverlay?.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 992) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                    sidebarOverlay?.classList.remove('show');
                }
            }
        });
        
        // Global search functionality
        const globalSearch = document.getElementById('globalSearch');
        globalSearch?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim().toLowerCase();
                if (query) {
                    // Get all menu links
                    const menuLinks = document.querySelectorAll('.sidebar-menu .nav-link');
                    menuLinks.forEach(link => {
                        const text = link.textContent.toLowerCase();
                        if (text.includes(query)) {
                            link.click();
                            return;
                        }
                    });
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
