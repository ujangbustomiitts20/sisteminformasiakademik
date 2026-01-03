@extends('layouts.app')

@section('title', 'Detail Penugasan/Mutasi')

@section('content')
<div class="page-title">
    <h4>Detail Penugasan/Mutasi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.penugasan.index') }}">Penugasan/Mutasi</a></li>
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

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-text me-2"></i>Informasi Penugasan/Mutasi</span>
                <div>
                    @if($penugasan->status == 'aktif')
                    <span class="badge bg-success fs-6">Aktif</span>
                    @elseif($penugasan->status == 'selesai')
                    <span class="badge bg-info fs-6">Selesai</span>
                    @elseif($penugasan->status == 'dibatalkan')
                    <span class="badge bg-danger fs-6">Dibatalkan</span>
                    @else
                    <span class="badge bg-secondary fs-6">{{ ucfirst($penugasan->status) }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">No. SK</h6>
                        <p class="fw-bold">{{ $penugasan->no_sk }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Tanggal SK</h6>
                        <p>{{ $penugasan->tanggal_sk?->format('d F Y') ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Jenis</h6>
                        <p>
                            @if($penugasan->jenis == 'penugasan')
                            <span class="badge bg-primary">Penugasan</span>
                            @elseif($penugasan->jenis == 'mutasi')
                            <span class="badge bg-warning text-dark">Mutasi</span>
                            @elseif($penugasan->jenis == 'promosi')
                            <span class="badge bg-success">Promosi</span>
                            @elseif($penugasan->jenis == 'demosi')
                            <span class="badge bg-danger">Demosi</span>
                            @else
                            <span class="badge bg-info">{{ ucfirst($penugasan->jenis) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">TMT (Terhitung Mulai Tanggal)</h6>
                        <p>{{ $penugasan->tmt?->format('d F Y') ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Tanggal Selesai</h6>
                        <p>{{ $penugasan->tanggal_selesai?->format('d F Y') ?? 'Tidak ditentukan' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Durasi</h6>
                        <p>
                            @if($penugasan->tanggal_selesai)
                                {{ $penugasan->tmt->diffInDays($penugasan->tanggal_selesai) }} hari
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>

                @if($penugasan->jenis == 'penugasan')
                <hr>
                <h5 class="mb-3"><i class="bi bi-briefcase me-2"></i>Detail Penugasan</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Nama Tugas</h6>
                        <p class="fw-bold">{{ $penugasan->nama_tugas ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Lokasi Penugasan</h6>
                        <p>{{ $penugasan->lokasi_penugasan ?? '-' }}</p>
                    </div>
                </div>
                @if($penugasan->deskripsi_tugas)
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Deskripsi Tugas</h6>
                    <p>{{ $penugasan->deskripsi_tugas }}</p>
                </div>
                @endif
                @endif

                @if(in_array($penugasan->jenis, ['mutasi', 'promosi', 'demosi', 'rotasi']))
                <hr>
                <h5 class="mb-3"><i class="bi bi-arrow-left-right me-2"></i>Detail {{ ucfirst($penugasan->jenis) }}</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Unit Kerja Asal</h6>
                                <p class="fw-bold mb-1">{{ $penugasan->unitKerjaAsal->nama ?? '-' }}</p>
                                <small class="text-muted">Jabatan: {{ $penugasan->jabatan_asal ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success bg-opacity-10">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Unit Kerja Tujuan</h6>
                                <p class="fw-bold mb-1">{{ $penugasan->unitKerjaTujuan->nama ?? '-' }}</p>
                                <small class="text-muted">Jabatan: {{ $penugasan->jabatan_tujuan ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($penugasan->alasan)
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Alasan</h6>
                    <p>{{ $penugasan->alasan }}</p>
                </div>
                @endif

                @if($penugasan->catatan)
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Catatan</h6>
                    <p>{{ $penugasan->catatan }}</p>
                </div>
                @endif

                @if($penugasan->dokumen_sk)
                <hr>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Dokumen SK</h6>
                    <a href="{{ Storage::url($penugasan->dokumen_sk) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Lihat Dokumen SK
                    </a>
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
                @if($penugasan->dosen)
                <div class="text-center mb-3">
                    <div class="avatar avatar-xl bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($penugasan->dosen->nama, 0, 1)) }}
                    </div>
                </div>
                <h5 class="text-center mb-1">{{ $penugasan->dosen->nama_lengkap }}</h5>
                <p class="text-center text-muted mb-3">
                    <span class="badge bg-info">Dosen</span>
                </p>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">NIDN</td>
                        <td>{{ $penugasan->dosen->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIP</td>
                        <td>{{ $penugasan->dosen->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>{{ $penugasan->dosen->jabatan_fungsional ?? '-' }}</td>
                    </tr>
                </table>
                @elseif($penugasan->pegawai)
                <div class="text-center mb-3">
                    <div class="avatar avatar-xl bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($penugasan->pegawai->nama, 0, 1)) }}
                    </div>
                </div>
                <h5 class="text-center mb-1">{{ $penugasan->pegawai->nama }}</h5>
                <p class="text-center text-muted mb-3">
                    <span class="badge bg-secondary">Tendik</span>
                </p>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">NIP</td>
                        <td>{{ $penugasan->pegawai->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>{{ $penugasan->pegawai->jabatan ?? '-' }}</td>
                    </tr>
                </table>
                @endif
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
                        <td class="text-muted" width="45%">Dibuat oleh</td>
                        <td>{{ $penugasan->createdBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dibuat pada</td>
                        <td>{{ $penugasan->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terakhir diubah</td>
                        <td>{{ $penugasan->updated_at->format('d/m/Y H:i') }}</td>
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
                    <a href="{{ route('kepegawaian.penugasan.edit', $penugasan) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    
                    @if($penugasan->status == 'aktif')
                    <form action="{{ route('kepegawaian.penugasan.selesaikan', $penugasan) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan penugasan/mutasi ini?')">
                            <i class="bi bi-check-circle me-1"></i>Selesaikan
                        </button>
                    </form>
                    <form action="{{ route('kepegawaian.penugasan.batalkan', $penugasan) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Apakah Anda yakin ingin membatalkan penugasan/mutasi ini?')">
                            <i class="bi bi-x-circle me-1"></i>Batalkan
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('kepegawaian.penugasan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
