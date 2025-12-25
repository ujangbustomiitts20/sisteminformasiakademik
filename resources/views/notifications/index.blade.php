@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Notifikasi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notifikasi</li>
                </ol>
            </nav>
        </div>
        <div>
            @if($notifications->where('is_read', false)->count() > 0)
            <a href="{{ route('notifications.markAllRead') }}" class="btn btn-outline-primary me-2" onclick="return confirm('Tandai semua sebagai dibaca?')">
                <i class="bi bi-check-all me-1"></i>Tandai Semua Dibaca
            </a>
            @endif
            @if($notifications->where('is_read', true)->count() > 0)
            <a href="{{ route('notifications.clearRead') }}" class="btn btn-outline-danger" onclick="return confirm('Hapus semua notifikasi yang sudah dibaca?')">
                <i class="bi bi-trash me-1"></i>Hapus yang Dibaca
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            @if($notifications->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-bell-slash display-1 text-muted"></i>
                <p class="text-muted mt-3">Tidak ada notifikasi</p>
            </div>
            @else
            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                <div class="list-group-item list-group-item-action {{ !$notification->is_read ? 'bg-light' : '' }}">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                @switch($notification->type)
                                    @case('success')
                                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                        @break
                                    @case('warning')
                                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                                        @break
                                    @case('danger')
                                        <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                                        @break
                                    @default
                                        <i class="bi bi-info-circle-fill text-info fs-4"></i>
                                @endswitch
                            </div>
                            <div>
                                <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                    {{ $notification->title }}
                                    @if(!$notification->is_read)
                                    <span class="badge bg-primary ms-2">Baru</span>
                                    @endif
                                </h6>
                                <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                    @if($notification->is_read && $notification->read_at)
                                    <span class="ms-2">
                                        <i class="bi bi-eye me-1"></i>Dibaca {{ $notification->read_at->diffForHumans() }}
                                    </span>
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            @if(!$notification->is_read)
                            <a href="{{ route('notifications.markRead', $notification->id) }}" class="btn btn-sm btn-outline-primary me-2" title="Tandai Dibaca">
                                <i class="bi bi-check"></i>
                            </a>
                            @endif
                            @if($notification->link)
                            <a href="{{ route('notifications.markRead', $notification->id) }}" class="btn btn-sm btn-primary me-2" title="Lihat">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            @endif
                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus notifikasi ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @if($notifications->hasPages())
        <div class="card-footer">
            {{ $notifications->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
