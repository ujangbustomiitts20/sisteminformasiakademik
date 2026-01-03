@extends('layouts.app')

@section('title', 'Program Studi')

@section('content')
<div class="page-title">
    <h4>Program Studi Fakultas</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Program Studi</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">{{ $fakultas->nama }}</h6>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($prodis as $prodi)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $prodi->nama }}</h5>
                        <p class="text-muted mb-3">{{ $prodi->kode ?? '-' }}</p>
                        
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <h4 class="mb-0 text-primary">{{ $prodi->mahasiswa_count }}</h4>
                                <small class="text-muted">Mahasiswa</small>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-0 text-success">{{ $prodi->dosen_count }}</h4>
                                <small class="text-muted">Dosen</small>
                            </div>
                        </div>
                        
                        <a href="{{ route('dekan.program-studi.show', $prodi) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye me-1"></i>Detail
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">
                Tidak ada program studi
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
