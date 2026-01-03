@extends('layouts.app')

@section('title', 'Detail Kenaikan Pangkat')

@section('content')
<div class="page-title">
    <h4>Detail Kenaikan Pangkat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.kenaikan-pangkat.index') }}">Kenaikan Pangkat</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@php
$statusList = \App\Models\KenaikanPangkat::STATUS;
$jenisList = \App\Models\KenaikanPangkat::JENIS;
$pangkatList = \App\Models\KenaikanPangkat::PANGKAT;
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-award me-2"></i>Informasi Kenaikan Pangkat</span>
                <div>
                    @if($kenaikanPangkat->status == 'draft')
                    <span class="badge bg-secondary fs-6">Draft</span>
                    @elseif($kenaikanPangkat->status == 'diusulkan')
                    <span class="badge bg-warning text-dark fs-6">Diusulkan</span>
                    @elseif($kenaikanPangkat->status == 'verifikasi')
                    <span class="badge bg-info fs-6">Verifikasi</span>
                    @elseif($kenaikanPangkat->status == 'disetujui')
                    <span class="badge bg-success fs-6">Disetujui</span>
                    @elseif($kenaikanPangkat->status == 'ditolak')
                    <span class="badge bg-danger fs-6">Ditolak</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Info Periode -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Periode</h6>
                        <p class="fw-bold text-primary">{{ ucfirst($kenaikanPangkat->periode) }} {{ $kenaikanPangkat->tahun }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Jenis Kenaikan</h6>
                        <p><span class="badge bg-info">{{ $jenisList[$kenaikanPangkat->jenis] ?? $kenaikanPangkat->jenis }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Masa Kerja</h6>
                        <p>{{ $kenaikanPangkat->masa_kerja_tahun ?? 0 }} Tahun {{ $kenaikanPangkat->masa_kerja_bulan ?? 0 }} Bulan</p>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3"><i class="bi bi-arrow-left-right me-2"></i>Detail Pangkat</h5>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Pangkat Lama</h6>
                                <h5 class="mb-1">{{ $pangkatList[$kenaikanPangkat->golongan_lama] ?? $kenaikanPangkat->pangkat_lama ?? '-' }}</h5>
                                <p class="mb-1"><strong>Golongan:</strong> {{ $kenaikanPangkat->golongan_lama ?? '-' }}</p>
                                <p class="mb-0 text-muted small">TMT: {{ $kenaikanPangkat->tmt_pangkat_lama?->format('d F Y') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success bg-opacity-10">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Pangkat Baru</h6>
                                <h5 class="mb-1 text-success">{{ $pangkatList[$kenaikanPangkat->golongan_baru] ?? $kenaikanPangkat->pangkat_baru ?? '-' }}</h5>
                                <p class="mb-1"><strong>Golongan:</strong> {{ $kenaikanPangkat->golongan_baru ?? '-' }}</p>
                                <p class="mb-0 text-muted small">TMT: {{ $kenaikanPangkat->tmt_pangkat_baru?->format('d F Y') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Surat Keputusan</h5>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">No. SK</h6>
                        <p class="fw-bold">{{ $kenaikanPangkat->no_sk ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Tanggal SK</h6>
                        <p>{{ $kenaikanPangkat->tanggal_sk?->format('d F Y') ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Pejabat Penandatangan</h6>
                        <p>{{ $kenaikanPangkat->pejabat_penandatangan ?? '-' }}</p>
                    </div>
                </div>

                @if($kenaikanPangkat->pendidikan_terakhir || $kenaikanPangkat->angka_kredit || $kenaikanPangkat->penilaian_kinerja)
                <hr>
                <h5 class="mb-3"><i class="bi bi-mortarboard me-2"></i>Informasi Tambahan</h5>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Pendidikan Terakhir</h6>
                        <p>{{ $kenaikanPangkat->pendidikan_terakhir ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Angka Kredit</h6>
                        <p>{{ $kenaikanPangkat->angka_kredit ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Penilaian Kinerja</h6>
                        <p>{{ $kenaikanPangkat->penilaian_kinerja ?? '-' }}</p>
                    </div>
                </div>
                @endif

                @if($kenaikanPangkat->catatan)
                <hr>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Catatan</h6>
                    <p>{{ $kenaikanPangkat->catatan }}</p>
                </div>
                @endif

                <!-- Dokumen -->
                @if($kenaikanPangkat->dokumen_sk || $kenaikanPangkat->dokumen_pak || $kenaikanPangkat->dokumen_skp)
                <hr>
                <h5 class="mb-3"><i class="bi bi-folder me-2"></i>Dokumen</h5>
                <div class="d-flex flex-wrap gap-2">
                    @if($kenaikanPangkat->dokumen_sk)
                    <a href="{{ Storage::url($kenaikanPangkat->dokumen_sk) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i>SK Pangkat
                    </a>
                    @endif
                    @if($kenaikanPangkat->dokumen_pak)
                    <a href="{{ Storage::url($kenaikanPangkat->dokumen_pak) }}" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i>PAK
                    </a>
                    @endif
                    @if($kenaikanPangkat->dokumen_skp)
                    <a href="{{ Storage::url($kenaikanPangkat->dokumen_skp) }}" target="_blank" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i>SKP
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Info Pegawai -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Informasi Pegawai
            </div>
            <div class="card-body">
                @if($kenaikanPangkat->dosen)
                <div class="text-center mb-3">
                    <div class="avatar avatar-xl bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($kenaikanPangkat->dosen->nama, 0, 1)) }}
                    </div>
                </div>
                <h5 class="text-center mb-1">{{ $kenaikanPangkat->dosen->nama_lengkap }}</h5>
                <p class="text-center text-muted mb-3">
                    <span class="badge bg-info">Dosen</span>
                </p>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">NIDN</td>
                        <td>{{ $kenaikanPangkat->dosen->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIP</td>
                        <td>{{ $kenaikanPangkat->dosen->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>{{ $kenaikanPangkat->dosen->jabatan_fungsional ?? '-' }}</td>
                    </tr>
                </table>
                @elseif($kenaikanPangkat->pegawai)
                <div class="text-center mb-3">
                    <div class="avatar avatar-xl bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($kenaikanPangkat->pegawai->nama, 0, 1)) }}
                    </div>
                </div>
                <h5 class="text-center mb-1">{{ $kenaikanPangkat->pegawai->nama }}</h5>
                <p class="text-center text-muted mb-3">
                    <span class="badge bg-secondary">Tendik</span>
                </p>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">NIP</td>
                        <td>{{ $kenaikanPangkat->pegawai->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>{{ $kenaikanPangkat->pegawai->jabatan ?? '-' }}</td>
                    </tr>
                </table>
                @endif
            </div>
        </div>

        <!-- Info Proses -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Riwayat Proses
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @if($kenaikanPangkat->diusulkanOleh)
                    <li class="mb-2 pb-2 border-bottom">
                        <small class="text-muted d-block">Diusulkan oleh</small>
                        <span>{{ $kenaikanPangkat->diusulkanOleh->name ?? '-' }}</span>
                    </li>
                    @endif
                    @if($kenaikanPangkat->diverifikasiOleh)
                    <li class="mb-2 pb-2 border-bottom">
                        <small class="text-muted d-block">Diverifikasi oleh</small>
                        <span>{{ $kenaikanPangkat->diverifikasiOleh->name ?? '-' }}</span>
                    </li>
                    @endif
                    @if($kenaikanPangkat->disetujuiOleh)
                    <li class="mb-2">
                        <small class="text-muted d-block">Disetujui oleh</small>
                        <span>{{ $kenaikanPangkat->disetujuiOleh->name ?? '-' }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Info Sistem -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Sistem
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="45%">Dibuat pada</td>
                        <td>{{ $kenaikanPangkat->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terakhir diubah</td>
                        <td>{{ $kenaikanPangkat->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>Aksi
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(in_array($kenaikanPangkat->status, ['draft', 'diusulkan']))
                    <a href="{{ route('kepegawaian.kenaikan-pangkat.edit', $kenaikanPangkat) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    @endif
                    
                    @if($kenaikanPangkat->status == 'diusulkan')
                    <form action="{{ route('kepegawaian.kenaikan-pangkat.verifikasi', $kenaikanPangkat) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-info w-100" onclick="return confirm('Verifikasi usulan ini?')">
                            <i class="bi bi-check2-square me-1"></i>Verifikasi
                        </button>
                    </form>
                    @endif

                    @if($kenaikanPangkat->status == 'verifikasi')
                    <form action="{{ route('kepegawaian.kenaikan-pangkat.setujui', $kenaikanPangkat) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui kenaikan pangkat ini?')">
                            <i class="bi bi-check-circle me-1"></i>Setujui
                        </button>
                    </form>
                    <form action="{{ route('kepegawaian.kenaikan-pangkat.tolak', $kenaikanPangkat) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Tolak kenaikan pangkat ini?')">
                            <i class="bi bi-x-circle me-1"></i>Tolak
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('kepegawaian.kenaikan-pangkat.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
