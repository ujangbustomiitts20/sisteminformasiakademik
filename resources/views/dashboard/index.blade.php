@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-title">
    <h4>Dashboard</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-mortarboard-fill text-primary" style="font-size: 4rem;"></i>
                <h3 class="mt-3">Selamat Datang di SIAKAD</h3>
                <p class="text-muted">Sistem Informasi Akademik</p>
            </div>
        </div>
    </div>
</div>

@if(isset($pengumuman) && $pengumuman->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-megaphone me-2"></i>Pengumuman Terbaru
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($pengumuman as $p)
                    <a href="{{ route('pengumuman.show', $p) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $p->judul }}</h6>
                            <small class="text-muted">{{ $p->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1 small text-muted">{{ Str::limit(strip_tags($p->isi), 100) }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
