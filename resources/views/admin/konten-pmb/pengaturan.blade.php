@extends('layouts.app')

@section('title', 'Pengaturan Portal PMB')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Pengaturan Portal PMB</h5>
                    <a href="{{ route('pmb.konten-pmb.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-grid me-1"></i> Kelola Konten
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                    
                    <form action="{{ route('pmb.konten-pmb.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Informasi Umum -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Umum</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Institusi</label>
                                        <input type="text" name="nama_institusi" class="form-control" value="{{ $konten['nama_institusi'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Singkatan</label>
                                        <input type="text" name="singkatan_institusi" class="form-control" value="{{ $konten['singkatan_institusi'] ?? '' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Tagline</label>
                                        <input type="text" name="tagline" class="form-control" value="{{ $konten['tagline'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Logo</label>
                                        <input type="file" name="logo" class="form-control" accept="image/*">
                                        @if($konten['logo'] ?? false)
                                        <img src="{{ asset('storage/'.$konten['logo']) }}" alt="Logo" class="mt-2" style="max-height: 60px;">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Favicon</label>
                                        <input type="file" name="favicon" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hero Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-image me-2"></i>Hero Section</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Judul Hero</label>
                                        <input type="text" name="hero_title" class="form-control" value="{{ $konten['hero_title'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subjudul Hero</label>
                                        <input type="text" name="hero_subtitle" class="form-control" value="{{ $konten['hero_subtitle'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statistik -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistik</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Tahun Berdiri</label>
                                        <input type="number" name="tahun_berdiri" class="form-control" value="{{ $konten['tahun_berdiri'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Program Studi</label>
                                        <input type="number" name="total_prodi" class="form-control" value="{{ $konten['total_prodi'] ?? '' }}">
                                        <small class="text-muted">Kosongkan untuk auto dari database</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Mahasiswa</label>
                                        <input type="number" name="total_mahasiswa" class="form-control" value="{{ $konten['total_mahasiswa'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Dosen</label>
                                        <input type="number" name="total_dosen" class="form-control" value="{{ $konten['total_dosen'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEO -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-search me-2"></i>SEO</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Meta Description</label>
                                        <textarea name="meta_description" class="form-control" rows="2">{{ $konten['meta_description'] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Meta Keywords</label>
                                        <input type="text" name="meta_keywords" class="form-control" value="{{ $konten['meta_keywords'] ?? '' }}" placeholder="Pisahkan dengan koma">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
