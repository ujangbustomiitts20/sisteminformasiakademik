@extends('layouts.app')

@section('title', $pengumuman->judul)

@section('content')
<div class="page-title">
    <h4>Detail Pengumuman</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pengumuman.index') }}">Pengumuman</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-megaphone me-2"></i>{{ $pengumuman->judul }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>{{ $pengumuman->created_at->format('d F Y, H:i') }}
                        @if($pengumuman->target)
                        <span class="ms-3"><i class="bi bi-people me-1"></i>{{ ucfirst($pengumuman->target) }}</span>
                        @endif
                    </div>
                    @if(Auth::user()->isAdmin())
                    <div>
                        <a href="{{ route('pengumuman.edit', $pengumuman) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </div>
                    @endif
                </div>
                
                <div class="pengumuman-content">
                    {!! nl2br(e($pengumuman->konten)) !!}
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Pengumuman
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
