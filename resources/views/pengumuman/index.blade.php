@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
<div class="page-title">
    <h4>Pengumuman</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pengumuman</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-megaphone me-2"></i>Daftar Pengumuman</span>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('pengumuman.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Buat Pengumuman
        </a>
        @endif
    </div>
    <div class="card-body">
        @forelse($pengumuman as $p)
        <div class="card mb-3 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title mb-1">{{ $p->judul }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-calendar3 me-1"></i>{{ $p->created_at->format('d F Y, H:i') }}
                            @if($p->target)
                            <span class="ms-2"><i class="bi bi-people me-1"></i>{{ ucfirst($p->target) }}</span>
                            @endif
                        </p>
                    </div>
                    @if(Auth::user()->isAdmin())
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('pengumuman.edit', $p) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('pengumuman.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                <p class="card-text">{{ Str::limit($p->konten, 300) }}</p>
                <a href="{{ route('pengumuman.show', $p) }}" class="btn btn-outline-primary btn-sm">
                    Baca selengkapnya <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="bi bi-megaphone text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2">Belum ada pengumuman</p>
        </div>
        @endforelse
        
        @if($pengumuman->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $pengumuman->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
