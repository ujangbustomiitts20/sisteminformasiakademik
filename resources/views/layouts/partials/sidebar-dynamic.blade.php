{{-- Dynamic Sidebar Menu based on User Roles --}}
@php
    use App\Models\Menu;
    use Illuminate\Support\Facades\Schema;
    
    $userMenus = collect();
    $useDynamicMenu = false;
    
    // Check if dynamic menu tables exist
    if (Schema::hasTable('menus') && Schema::hasTable('role_menu')) {
        try {
            $useDynamicMenu = Menu::count() > 0;
            if ($useDynamicMenu && auth()->check()) {
                $userMenus = auth()->user()->getMenus();
            }
        } catch (\Exception $e) {
            $useDynamicMenu = false;
        }
    }
@endphp

@if($useDynamicMenu && $userMenus->count() > 0)
    {{-- Dynamic Menu --}}
    @foreach($userMenus as $menu)
        @if($menu->is_divider)
            <li class="sidebar-divider">
                <span>{{ $menu->nama }}</span>
            </li>
        @elseif($menu->children->count() > 0)
            <li class="sidebar-item has-submenu {{ $menu->isActive() ? 'active' : '' }}">
                <a href="#submenu-{{ $menu->id }}" class="sidebar-link" data-bs-toggle="collapse">
                    @if($menu->icon)<i class="{{ $menu->icon }}"></i>@endif
                    <span>{{ $menu->nama }}</span>
                    @if($menu->badge_text)
                        <span class="badge bg-{{ $menu->badge_color ?? 'primary' }} ms-auto">{{ $menu->badge_text }}</span>
                    @endif
                    <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
                </a>
                <ul class="sidebar-submenu collapse {{ $menu->isActive() ? 'show' : '' }}" id="submenu-{{ $menu->id }}">
                    @foreach($menu->children as $child)
                        <li class="submenu-item {{ $child->isActive() ? 'active' : '' }}">
                            <a href="{{ $child->url }}" class="submenu-link">
                                @if($child->icon)<i class="{{ $child->icon }}"></i>@endif
                                <span>{{ $child->nama }}</span>
                                @if($child->badge_text)
                                    <span class="badge bg-{{ $child->badge_color ?? 'primary' }} ms-auto">{{ $child->badge_text }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @else
            <li class="sidebar-item {{ $menu->isActive() ? 'active' : '' }}">
                <a href="{{ $menu->url }}" class="sidebar-link">
                    @if($menu->icon)<i class="{{ $menu->icon }}"></i>@endif
                    <span>{{ $menu->nama }}</span>
                    @if($menu->badge_text)
                        <span class="badge bg-{{ $menu->badge_color ?? 'primary' }} ms-auto">{{ $menu->badge_text }}</span>
                    @endif
                </a>
            </li>
        @endif
    @endforeach
@else
    {{-- Fallback: Static Menu based on role --}}
    @include('layouts.partials.sidebar-static')
@endif
