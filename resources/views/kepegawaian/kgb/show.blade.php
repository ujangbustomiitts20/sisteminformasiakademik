@extends('layouts.app')

@section('title', 'Detail Kenaikan Gaji Berkala')

@section('content')
<div class="page-title">
    <h4>Detail Kenaikan Gaji Berkala (KGB)</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.kgb.index') }}">KGB</a></li>
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
                <span><i class="bi bi-currency-dollar me-2"></i>Informasi KGB</span>
                <div>
                    @if($kgb->status == 'pending')
                    <span class="badge bg-warning text-dark fs-6">Pending</span>
                    @elseif($kgb->status == 'diproses')
                    <span class="badge bg-info fs-6">Diproses</span>
                    @elseif($kgb->status == 'disetujui')
                    <span class="badge bg-success fs-6">Disetujui</span>
                    @elseif($kgb->status == 'ditolak')
                    <span class="badge bg-danger fs-6">Ditolak</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Info SK -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">No. SK</h6>
                        <p class="fw-bold">{{ $kgb->no_sk ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Tanggal SK</h6>
                        <p>{{ $kgb->tanggal_sk?->format('d F Y') ?? '-' }}</p>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3"><i class="bi bi-calendar-date me-2"></i>Informasi TMT</h5>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">TMT KGB Saat Ini</h6>
                        <p class="fw-bold text-primary">{{ $kgb->tmt_kgb?->format('d F Y') ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">TMT KGB Berikutnya</h6>
                        <p class="fw-bold text-success">{{ $kgb->tmt_kgb_berikutnya?->format('d F Y') ?? '-' }}</p>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3"><i class="bi bi-star me-2"></i>Golongan & Masa Kerja</h5>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Golongan/Ruang</h6>
                        <p class="fw-bold">{{ $kgb->golongan_ruang ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Masa Kerja Golongan</h6>
                        <p>{{ $kgb->masa_kerja_golongan_tahun ?? 0 }} Tahun {{ $kgb->masa_kerja_golongan_bulan ?? 0 }} Bulan</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Masa Kerja Total</h6>
                        <p>{{ $kgb->masa_kerja_total_tahun ?? 0 }} Tahun {{ $kgb->masa_kerja_total_bulan ?? 0 }} Bulan</p>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3"><i class="bi bi-cash-stack me-2"></i>Informasi Gaji</h5>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Gaji Pokok Lama</h6>
                                <h4 class="text-secondary mb-0">Rp {{ number_format($kgb->gaji_pokok_lama ?? 0, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success bg-opacity-10">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-2">Gaji Pokok Baru</h6>
                                <h4 class="text-success mb-0">Rp {{ number_format($kgb->gaji_pokok_baru ?? 0, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                @if($kgb->gaji_pokok_lama && $kgb->gaji_pokok_baru)
                <div class="alert alert-info">
                    <i class="bi bi-arrow-up-circle me-2"></i>
                    <strong>Kenaikan:</strong> 
                    Rp {{ number_format($kgb->gaji_pokok_baru - $kgb->gaji_pokok_lama, 0, ',', '.') }}
                    ({{ number_format((($kgb->gaji_pokok_baru - $kgb->gaji_pokok_lama) / $kgb->gaji_pokok_lama) * 100, 2) }}%)
                </div>
                @endif

                @if($kgb->catatan)
                <hr>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Catatan</h6>
                    <p>{{ $kgb->catatan }}</p>
                </div>
                @endif

                @if($kgb->dokumen_sk)
                <hr>
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Dokumen SK</h6>
                    <a href="{{ Storage::url($kgb->dokumen_sk) }}" target="_blank" class="btn btn-outline-primary btn-sm">
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
                @if($kgb->dosen)
                <div class="text-center mb-3">
                    <div class="avatar avatar-xl bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($kgb->dosen->nama, 0, 1)) }}
                    </div>
                </div>
                <h5 class="text-center mb-1">{{ $kgb->dosen->nama_lengkap }}</h5>
                <p class="text-center text-muted mb-3">
                    <span class="badge bg-info">Dosen</span>
                </p>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">NIDN</td>
                        <td>{{ $kgb->dosen->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIP</td>
                        <td>{{ $kgb->dosen->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>{{ $kgb->dosen->jabatan_fungsional ?? '-' }}</td>
                    </tr>
                </table>
                @elseif($kgb->pegawai)
                <div class="text-center mb-3">
                    <div class="avatar avatar-xl bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($kgb->pegawai->nama, 0, 1)) }}
                    </div>
                </div>
                <h5 class="text-center mb-1">{{ $kgb->pegawai->nama }}</h5>
                <p class="text-center text-muted mb-3">
                    <span class="badge bg-secondary">Tendik</span>
                </p>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">NIP</td>
                        <td>{{ $kgb->pegawai->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>{{ $kgb->pegawai->jabatan ?? '-' }}</td>
                    </tr>
                </table>
                @endif
            </div>
        </div>

        <!-- Info Proses -->
        @if($kgb->diprosesOleh)
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-person-check me-2"></i>Informasi Proses
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="45%">Diproses oleh</td>
                        <td>{{ $kgb->diprosesOleh->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Proses</td>
                        <td>{{ $kgb->tanggal_diproses?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        <!-- Info Sistem -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Sistem
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="45%">Input</td>
                        <td>
                            @if($kgb->is_otomatis)
                            <span class="badge bg-info">Otomatis</span>
                            @else
                            <span class="badge bg-secondary">Manual</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dibuat pada</td>
                        <td>{{ $kgb->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terakhir diubah</td>
                        <td>{{ $kgb->updated_at->format('d/m/Y H:i') }}</td>
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
                    @if(in_array($kgb->status, ['pending', 'diproses']))
                    <a href="{{ route('kepegawaian.kgb.edit', $kgb) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    @endif
                    
                    @if($kgb->status == 'pending')
                    <form action="{{ route('kepegawaian.kgb.proses', $kgb) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-info w-100" onclick="return confirm('Proses KGB ini?')">
                            <i class="bi bi-play-circle me-1"></i>Proses
                        </button>
                    </form>
                    @endif

                    @if($kgb->status == 'diproses')
                    <form action="{{ route('kepegawaian.kgb.setujui', $kgb) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui KGB ini?')">
                            <i class="bi bi-check-circle me-1"></i>Setujui
                        </button>
                    </form>
                    <form action="{{ route('kepegawaian.kgb.tolak', $kgb) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Tolak KGB ini?')">
                            <i class="bi bi-x-circle me-1"></i>Tolak
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('kepegawaian.kgb.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
