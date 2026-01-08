@extends('layouts.app')

@section('title', 'Preview Menu')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye me-2"></i>Preview Sidebar Menu
                    </h5>
                    <a href="{{ route('admin.menus.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="bg-dark text-white" style="min-height: 500px;">
                        <div class="p-3 border-bottom border-secondary">
                            <h5 class="mb-0 text-white">
                                <i class="bi bi-mortarboard me-2"></i>SIAKAD
                            </h5>
                            <small class="text-white-50">Preview Mode</small>
                        </div>
                        
                        <div class="p-2">
                            @forelse($menus as $menu)
                                @if($menu->is_divider)
                                    <div class="text-uppercase text-white-50 small px-3 py-2 mt-2">
                                        {{ $menu->nama }}
                                    </div>
                                @elseif($menu->children->count() > 0)
                                    <div class="px-3 py-2">
                                        <a class="d-flex align-items-center text-white text-decoration-none" 
                                           data-bs-toggle="collapse" href="#previewMenu{{ $menu->id }}">
                                            @if($menu->icon)<i class="{{ $menu->icon }} me-2"></i>@endif
                                            <span>{{ $menu->nama }}</span>
                                            @if($menu->badge_text)
                                                <span class="badge bg-{{ $menu->badge_color ?? 'primary' }} ms-2">{{ $menu->badge_text }}</span>
                                            @endif
                                            <i class="bi bi-chevron-down ms-auto"></i>
                                        </a>
                                        <div class="collapse mt-1" id="previewMenu{{ $menu->id }}">
                                            @foreach($menu->children as $child)
                                                <a href="#" class="d-block text-white-50 text-decoration-none py-1 ps-4 small">
                                                    @if($child->icon)<i class="{{ $child->icon }} me-2"></i>@endif
                                                    {{ $child->nama }}
                                                    @if($child->badge_text)
                                                        <span class="badge bg-{{ $child->badge_color ?? 'primary' }} ms-2">{{ $child->badge_text }}</span>
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="#" class="d-flex align-items-center text-white text-decoration-none px-3 py-2">
                                        @if($menu->icon)<i class="{{ $menu->icon }} me-2"></i>@endif
                                        <span>{{ $menu->nama }}</span>
                                        @if($menu->badge_text)
                                            <span class="badge bg-{{ $menu->badge_color ?? 'primary' }} ms-auto">{{ $menu->badge_text }}</span>
                                        @endif
                                    </a>
                                @endif
                            @empty
                                <div class="text-center text-white-50 py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada menu
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
