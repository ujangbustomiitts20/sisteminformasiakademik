@extends('layouts.app')

@section('title', 'Detail Periode PMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $periode->nama }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pmb.periode.index') }}">Periode</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('pmb.periode.edit', $periode->hashid) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <a href="{{ route('pmb.periode.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Informasi Periode
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%">Nama Periode</td>
                            <td>{{ $periode->nama }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tahun Akademik</td>
                            <td>{{ $periode->tahun_akademik }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal Mulai</td>
                            <td>{{ $periode->tanggal_mulai->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal Selesai</td>
                            <td>{{ $periode->tanggal_selesai->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status</td>
                            <td><span class="badge bg-{{ $periode->status_badge }}">{{ $periode->status }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Aktif</td>
                            <td>
                                @if($periode->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @if($periode->deskripsi)
                        <tr>
                            <td class="fw-bold">Deskripsi</td>
                            <td>{{ $periode->deskripsi }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-layers me-2"></i>Gelombang PMB</span>
                    <a href="{{ route('pmb.gelombang.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </a>
                </div>
                <div class="card-body">
                    @if($periode->gelombang->count() > 0)
                    <div class="list-group">
                        @foreach($periode->gelombang as $gelombang)
                        <a href="{{ route('pmb.gelombang.show', $gelombang->hashid) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $gelombang->nama }}</h6>
                                    <small class="text-muted">
                                        {{ $gelombang->tanggal_mulai_daftar->format('d M') }} - {{ $gelombang->tanggal_selesai_daftar->format('d M Y') }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $gelombang->status_badge }}">{{ $gelombang->status_pendaftaran }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">Belum ada gelombang PMB</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
