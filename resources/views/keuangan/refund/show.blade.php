@extends('layouts.app')

@section('title', 'Detail Refund')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Refund</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('keuangan.refund.index') }}">Refund</a></li>
                    <li class="breadcrumb-item active">{{ $refund->nomor_refund }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            @if($refund->canCancel())
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="bi bi-x-lg me-1"></i>Batalkan
                </button>
            @endif
            @if($refund->status === \App\Models\Refund::STATUS_PENDING)
                <a href="{{ route('keuangan.refund.edit', $refund) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            @endif
            <a href="{{ route('keuangan.refund.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Info Refund -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Refund</h5>
                    {!! $refund->status_badge !!}
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="40%">No. Refund</td>
                                    <td><strong>{{ $refund->nomor_refund }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jenis Refund</td>
                                    <td>{{ $refund->jenis_label }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Pengajuan</td>
                                    <td>{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Metode Refund</td>
                                    <td>{{ $refund->metode_label }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="40%">Jumlah Pengajuan</td>
                                    <td><strong class="text-primary">Rp {{ number_format($refund->jumlah_pengajuan, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jumlah Disetujui</td>
                                    <td>
                                        @if($refund->jumlah_disetujui)
                                            <strong class="text-success">Rp {{ number_format($refund->jumlah_disetujui, 0, ',', '.') }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($refund->tanggal_refund)
                                <tr>
                                    <td class="text-muted">Tanggal Refund</td>
                                    <td>{{ $refund->tanggal_refund->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                                @if($refund->nomor_referensi_refund)
                                <tr>
                                    <td class="text-muted">No. Referensi</td>
                                    <td>{{ $refund->nomor_referensi_refund }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Alasan Pengajuan:</h6>
                        <p class="mb-0">{{ $refund->alasan }}</p>
                    </div>

                    @if($refund->catatan_admin)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Catatan Admin:</h6>
                        <p class="mb-0">{{ $refund->catatan_admin }}</p>
                    </div>
                    @endif

                    @if($refund->alasan_penolakan)
                    <div class="alert alert-danger mb-0">
                        <h6 class="mb-2"><i class="bi bi-exclamation-triangle me-1"></i>Alasan Penolakan:</h6>
                        <p class="mb-0">{{ $refund->alasan_penolakan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Info Mahasiswa -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h5>
                </div>
                <div class="card-body">
                    @if($refund->mahasiswa)
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="35%">NIM</td>
                                    <td><strong>{{ $refund->mahasiswa->nim }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama</td>
                                    <td>{{ $refund->mahasiswa->nama }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="35%">Program Studi</td>
                                    <td>{{ $refund->mahasiswa->prodi?->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Angkatan</td>
                                    <td>{{ $refund->mahasiswa->angkatan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @else
                    <p class="text-muted mb-0">Data mahasiswa tidak tersedia</p>
                    @endif
                </div>
            </div>

            <!-- Info Bank (if transfer) -->
            @if($refund->metode_refund === 'transfer')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-bank me-2"></i>Informasi Rekening</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">Nama Bank</small>
                            <div class="fw-bold">{{ $refund->nama_bank }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Nomor Rekening</small>
                            <div class="fw-bold">{{ $refund->nomor_rekening }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Nama Pemilik</small>
                            <div class="fw-bold">{{ $refund->nama_pemilik_rekening }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Transaksi Terkait -->
            @if($refund->transaksiPembayaran)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Transaksi Terkait</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">No. Transaksi</small>
                            <div class="fw-bold">{{ $refund->transaksiPembayaran->no_transaksi }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Tanggal Bayar</small>
                            <div>{{ $refund->transaksiPembayaran->tanggal_bayar?->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Jumlah Bayar</small>
                            <div class="fw-bold text-primary">Rp {{ number_format($refund->transaksiPembayaran->jumlah, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Riwayat Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Status</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($refund->histories as $history)
                                <tr>
                                    <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($history->status_lama)
                                            <span class="badge bg-secondary">{{ $history->status_lama_label }}</span>
                                            <i class="bi bi-arrow-right mx-1"></i>
                                        @endif
                                        <span class="badge bg-primary">{{ $history->status_baru_label }}</span>
                                    </td>
                                    <td>{{ $history->keterangan ?? '-' }}</td>
                                    <td>{{ $history->user?->name ?? 'System' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada riwayat</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Action Panel -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Aksi</h5>
                </div>
                <div class="card-body">
                    @if($refund->canProcess())
                        <form action="{{ route('keuangan.refund.process', $refund) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="catatan" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-gear me-1"></i>Proses Pengajuan
                            </button>
                        </form>
                        <hr>
                    @endif

                    @if($refund->canApprove())
                        <form action="{{ route('keuangan.refund.approve', $refund) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Jumlah Disetujui <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah_disetujui" class="form-control" 
                                        value="{{ $refund->jumlah_pengajuan }}" max="{{ $refund->jumlah_pengajuan }}" required>
                                </div>
                                <small class="text-muted">Maks: Rp {{ number_format($refund->jumlah_pengajuan, 0, ',', '.') }}</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="catatan" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-lg me-1"></i>Setujui Refund
                            </button>
                        </form>
                        <hr>
                    @endif

                    @if($refund->canReject())
                        <form action="{{ route('keuangan.refund.reject', $refund) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="alasan_penolakan" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-lg me-1"></i>Tolak Refund
                            </button>
                        </form>
                        <hr>
                    @endif

                    @if($refund->canComplete())
                        <form action="{{ route('keuangan.refund.complete', $refund) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">No. Referensi Transfer</label>
                                <input type="text" name="nomor_referensi_refund" class="form-control" placeholder="Opsional">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bukti Transfer</label>
                                <input type="file" name="bukti_refund" class="form-control" accept="image/*,.pdf">
                                <small class="text-muted">Format: JPG, PNG, PDF. Maks: 2MB</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check2-all me-1"></i>Selesaikan Refund
                            </button>
                        </form>
                    @endif

                    @if($refund->status === \App\Models\Refund::STATUS_SELESAI)
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            Refund telah selesai pada {{ $refund->diselesaikan_at?->format('d/m/Y H:i') }}
                        </div>
                    @endif

                    @if($refund->status === \App\Models\Refund::STATUS_DITOLAK)
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle me-2"></i>
                            Refund telah ditolak
                        </div>
                    @endif

                    @if($refund->status === \App\Models\Refund::STATUS_DIBATALKAN)
                        <div class="alert alert-secondary mb-0">
                            <i class="bi bi-slash-circle me-2"></i>
                            Refund telah dibatalkan
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dokumen -->
            @if($refund->dokumen_pendukung || $refund->bukti_refund)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-paperclip me-2"></i>Dokumen</h5>
                </div>
                <div class="card-body">
                    @if($refund->dokumen_pendukung)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Dokumen Pendukung</small>
                        <a href="{{ Storage::url($refund->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark me-1"></i>Lihat Dokumen
                        </a>
                    </div>
                    @endif
                    @if($refund->bukti_refund)
                    <div>
                        <small class="text-muted d-block mb-1">Bukti Transfer</small>
                        <a href="{{ Storage::url($refund->bukti_refund) }}" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-check me-1"></i>Lihat Bukti
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Info Petugas -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Petugas</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Diajukan oleh</td>
                            <td>{{ $refund->createdBy?->name ?? '-' }}</td>
                        </tr>
                        @if($refund->diprosesOleh)
                        <tr>
                            <td class="text-muted">Diproses oleh</td>
                            <td>{{ $refund->diprosesOleh->name }}</td>
                        </tr>
                        @endif
                        @if($refund->disetujuiOleh)
                        <tr>
                            <td class="text-muted">Disetujui oleh</td>
                            <td>{{ $refund->disetujuiOleh->name }}</td>
                        </tr>
                        @endif
                        @if($refund->diselesaikanOleh)
                        <tr>
                            <td class="text-muted">Diselesaikan oleh</td>
                            <td>{{ $refund->diselesaikanOleh->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('keuangan.refund.cancel', $refund) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Batalkan Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan pengajuan refund ini?</p>
                    <p class="mb-0"><strong>{{ $refund->nomor_refund }}</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
