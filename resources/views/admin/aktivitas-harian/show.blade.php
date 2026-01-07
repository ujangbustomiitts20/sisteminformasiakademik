@extends('layouts.app')

@section('title', 'Detail Aktivitas Harian')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Detail Aktivitas Harian</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kepegawaian.aktivitas-harian.index') }}">Aktivitas Harian</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('kepegawaian.aktivitas-harian.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
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
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>Informasi Aktivitas</h6>
                <span class="badge bg-{{ $aktivitasHarian->status_badge }} fs-6">
                    {{ \App\Models\AktivitasHarian::STATUS[$aktivitasHarian->status] }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Tanggal</label>
                        <p class="fw-semibold mb-0">{{ $aktivitasHarian->tanggal->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Jam</label>
                        <p class="mb-0">
                            @if($aktivitasHarian->jam_mulai)
                                {{ $aktivitasHarian->jam_mulai_format }}
                                @if($aktivitasHarian->jam_selesai)
                                    - {{ $aktivitasHarian->jam_selesai_format }}
                                @endif
                                @if($aktivitasHarian->durasi)
                                    <span class="text-muted">({{ $aktivitasHarian->durasi }})</span>
                                @endif
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Uraian Kegiatan</label>
                    <p class="mb-0">{{ $aktivitasHarian->uraian_kegiatan }}</p>
                    @if($aktivitasHarian->uraianKegiatanSkp)
                        <small class="badge bg-light text-dark mt-1">
                            <i class="bi bi-link"></i> {{ $aktivitasHarian->uraianKegiatanSkp->kategori_label }}
                        </small>
                    @endif
                </div>
                @if($aktivitasHarian->output_hasil)
                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Output/Hasil</label>
                    <p class="mb-0">{{ $aktivitasHarian->output_hasil }}</p>
                </div>
                @endif
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1">Volume</label>
                        <p class="mb-0">
                            @if($aktivitasHarian->volume)
                                {{ number_format($aktivitasHarian->volume, 0) }} {{ $aktivitasHarian->satuan }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1">Lokasi</label>
                        <p class="mb-0">{{ $aktivitasHarian->lokasi ?: '-' }}</p>
                    </div>
                </div>
                @if($aktivitasHarian->keterangan)
                <hr>
                <div class="mb-0">
                    <label class="form-label text-muted small mb-1">Keterangan</label>
                    <p class="mb-0">{{ $aktivitasHarian->keterangan }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($aktivitasHarian->status === 'disetujui')
        <div class="card shadow-sm border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle text-success fs-4 me-3"></i>
                    <div>
                        <div class="fw-semibold text-success">Disetujui</div>
                        <small class="text-muted">
                            oleh {{ $aktivitasHarian->approvedBy?->name ?? '-' }}
                            pada {{ $aktivitasHarian->tanggal_disetujui?->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($aktivitasHarian->status === 'ditolak' && $aktivitasHarian->catatan_atasan)
        <div class="card shadow-sm border-danger">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <i class="bi bi-x-circle text-danger fs-4 me-3"></i>
                    <div>
                        <div class="fw-semibold text-danger">Ditolak</div>
                        <p class="mb-0 mt-1">{{ $aktivitasHarian->catatan_atasan }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Info Pegawai -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Pegawai</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px;">
                        <i class="bi bi-person-fill text-white fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $aktivitasHarian->nama_pegawai }}</div>
                        <small class="text-muted">{{ $aktivitasHarian->nidn_nip }}</small>
                    </div>
                </div>
                @if($aktivitasHarian->dosen?->programStudi)
                <div class="small text-muted">
                    <i class="bi bi-building me-1"></i>
                    {{ $aktivitasHarian->dosen->programStudi->nama }}
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        @if($aktivitasHarian->status === 'diajukan')
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Aksi</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.aktivitas-harian.approve', $aktivitasHarian) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui aktivitas ini?')">
                        <i class="bi bi-check-lg me-1"></i>Setujui
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#modalReject">
                    <i class="bi bi-x-lg me-1"></i>Tolak
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="modalReject" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.aktivitas-harian.reject', $aktivitasHarian) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Tolak Aktivitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan/Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_atasan" class="form-control" rows="3" required 
                                  placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg me-1"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
