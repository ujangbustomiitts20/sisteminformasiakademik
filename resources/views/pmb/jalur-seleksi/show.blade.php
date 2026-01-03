@extends('layouts.app')

@section('title', 'Detail Jalur Seleksi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $jalur->nama }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pmb.jalur-seleksi.index') }}">Jalur Seleksi</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('pmb.jalur-seleksi.edit', $jalur->hashid) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('pmb.jalur-seleksi.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>Informasi Jalur Seleksi
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 25%">Kode</td>
                            <td><span class="badge bg-secondary fs-6">{{ $jalur->kode }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nama</td>
                            <td>{{ $jalur->nama }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status</td>
                            <td>
                                @if($jalur->is_active)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Total Pendaftar</td>
                            <td><span class="badge bg-info fs-6">{{ $jalur->calon_mahasiswa_count }}</span></td>
                        </tr>
                        @if($jalur->deskripsi)
                        <tr>
                            <td class="fw-bold">Deskripsi</td>
                            <td>{{ $jalur->deskripsi }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($jalur->persyaratan)
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list-check me-2"></i>Persyaratan
                </div>
                <div class="card-body">
                    {!! nl2br(e($jalur->persyaratan)) !!}
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-link me-2"></i>Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('pmb.calon-mahasiswa.index', ['jalur' => $jalur->id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-users me-1"></i> Lihat Pendaftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
