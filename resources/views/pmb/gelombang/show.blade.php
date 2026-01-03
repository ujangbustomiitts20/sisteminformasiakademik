@extends('layouts.app')

@section('title', 'Detail Gelombang PMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $gelombang->nama }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pmb.gelombang.index') }}">Gelombang</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('pmb.gelombang.edit', $gelombang->hashid) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <a href="{{ route('pmb.gelombang.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Informasi Gelombang
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%">Periode</td>
                            <td>{{ $gelombang->periodePmb->nama ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nama Gelombang</td>
                            <td>{{ $gelombang->nama }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nomor Gelombang</td>
                            <td>{{ $gelombang->nomor_gelombang }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Pendaftaran</td>
                            <td>{{ $gelombang->tanggal_mulai_daftar->format('d M Y') }} - {{ $gelombang->tanggal_selesai_daftar->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal Ujian</td>
                            <td>{{ $gelombang->tanggal_ujian?->format('d M Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal Pengumuman</td>
                            <td>{{ $gelombang->tanggal_pengumuman?->format('d M Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Daftar Ulang</td>
                            <td>
                                @if($gelombang->tanggal_daftar_ulang_mulai)
                                    {{ $gelombang->tanggal_daftar_ulang_mulai->format('d M Y') }} - {{ $gelombang->tanggal_daftar_ulang_selesai?->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status</td>
                            <td><span class="badge bg-{{ $gelombang->status_badge }}">{{ $gelombang->status_pendaftaran }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Aktif</td>
                            <td>
                                @if($gelombang->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart me-2"></i>Statistik Pendaftar
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h3 class="text-primary mb-1">{{ $gelombang->total_pendaftar }}</h3>
                            <small class="text-muted">Total Pendaftar</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-success mb-1">{{ $gelombang->total_lulus }}</h3>
                            <small class="text-muted">Lulus Seleksi</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-link-45deg me-2"></i>Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('pmb.calon-mahasiswa.index', ['gelombang' => $gelombang->id]) }}" class="btn btn-outline-primary">
                            <i class="bi bi-people me-1"></i> Lihat Pendaftar
                        </a>
                        <a href="{{ route('pmb.seleksi.input-nilai', ['gelombang' => $gelombang->id]) }}" class="btn btn-outline-info">
                            <i class="bi bi-pencil-square me-1"></i> Input Nilai
                        </a>
                        <a href="{{ route('pmb.seleksi.hasil', ['gelombang' => $gelombang->id]) }}" class="btn btn-outline-success">
                            <i class="bi bi-trophy me-1"></i> Hasil Seleksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
