@extends('layouts.app')

@section('title', 'Detail Kegiatan Lapangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Kegiatan Lapangan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mahasiswa.kegiatan-lapangan.index') }}">Kegiatan Lapangan</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('mahasiswa.kegiatan-lapangan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-body text-center py-4">
                    @php
                        $statusConfig = [
                            'menunggu' => ['class' => 'warning', 'icon' => 'hourglass-split', 'label' => 'Menunggu Verifikasi'],
                            'disetujui' => ['class' => 'success', 'icon' => 'check-circle', 'label' => 'Disetujui'],
                            'ditolak' => ['class' => 'danger', 'icon' => 'x-circle', 'label' => 'Ditolak'],
                            'aktif' => ['class' => 'primary', 'icon' => 'play-circle', 'label' => 'Sedang Berjalan'],
                            'selesai' => ['class' => 'info', 'icon' => 'check-all', 'label' => 'Selesai'],
                            'batal' => ['class' => 'secondary', 'icon' => 'x-octagon', 'label' => 'Dibatalkan'],
                        ];
                        $config = $statusConfig[$pendaftaran->status] ?? ['class' => 'secondary', 'icon' => 'question-circle', 'label' => ucfirst($pendaftaran->status)];
                    @endphp
                    <div class="display-4 text-{{ $config['class'] }} mb-3">
                        <i class="bi bi-{{ $config['icon'] }}"></i>
                    </div>
                    <h4 class="mb-1">{{ $config['label'] }}</h4>
                    <p class="text-muted mb-0">No: {{ $pendaftaran->nomor_pendaftaran ?? '-' }}</p>
                </div>
            </div>

            <!-- Info Kegiatan -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-briefcase me-2"></i>Informasi Kegiatan</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">{{ $pendaftaran->jenisKegiatan->nama ?? '-' }}</h6>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Periode</td>
                            <td>{{ $pendaftaran->periodeKegiatan->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Akademik</td>
                            <td>{{ $pendaftaran->periodeKegiatan->tahun_akademik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Semester</td>
                            <td>{{ $pendaftaran->periodeKegiatan->semester ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Durasi</td>
                            <td>
                                @if($pendaftaran->tanggal_mulai && $pendaftaran->tanggal_selesai)
                                {{ $pendaftaran->tanggal_mulai->format('d M Y') }} - {{ $pendaftaran->tanggal_selesai->format('d M Y') }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Info Mitra -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-building me-2"></i>Mitra / Lokasi</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $pendaftaran->mitraKegiatan->nama ?? $pendaftaran->nama_instansi ?? '-' }}</h6>
                    <p class="text-muted mb-2">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $pendaftaran->mitraKegiatan->alamat ?? $pendaftaran->alamat_instansi ?? '-' }}
                    </p>
                    @if($pendaftaran->mitraKegiatan->nama_kontak ?? $pendaftaran->nama_pembimbing_lapangan)
                    <hr>
                    <small class="text-muted d-block">Pembimbing Lapangan</small>
                    <strong>{{ $pendaftaran->nama_pembimbing_lapangan ?? $pendaftaran->mitraKegiatan->nama_kontak ?? '-' }}</strong>
                    @if($pendaftaran->telepon_pembimbing_lapangan)
                    <br><small><i class="bi bi-telephone"></i> {{ $pendaftaran->telepon_pembimbing_lapangan }}</small>
                    @endif
                    @endif
                </div>
            </div>

            <!-- Dosen Pembimbing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-person-badge me-2"></i>Dosen Pembimbing</h5>
                </div>
                <div class="card-body">
                    @if($pendaftaran->dosenPembimbing)
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-fill fs-4"></i>
                        </div>
                        <div>
                            <strong>{{ $pendaftaran->dosenPembimbing->nama }}</strong><br>
                            <small class="text-muted">{{ $pendaftaran->dosenPembimbing->nidn ?? '-' }}</small>
                        </div>
                    </div>
                    @else
                    <p class="text-muted mb-0 text-center py-2">Belum ditentukan</p>
                    @endif
                </div>
            </div>

            <!-- Dokumen -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-file-earmark me-2"></i>Dokumen</h5>
                </div>
                <div class="card-body">
                    @if($pendaftaran->file_proposal)
                    <a href="{{ Storage::url($pendaftaran->file_proposal) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2 w-100">
                        <i class="bi bi-file-pdf me-1"></i> Proposal
                    </a>
                    @endif
                    @if($pendaftaran->file_surat_pengantar)
                    <a href="{{ Storage::url($pendaftaran->file_surat_pengantar) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2 w-100">
                        <i class="bi bi-file-pdf me-1"></i> Surat Pengantar
                    </a>
                    @endif
                    @if($pendaftaran->file_surat_balasan)
                    <a href="{{ Storage::url($pendaftaran->file_surat_balasan) }}" target="_blank" class="btn btn-outline-success btn-sm mb-2 w-100">
                        <i class="bi bi-file-pdf me-1"></i> Surat Balasan
                    </a>
                    @endif
                    @if($pendaftaran->file_laporan)
                    <a href="{{ Storage::url($pendaftaran->file_laporan) }}" target="_blank" class="btn btn-outline-info btn-sm mb-2 w-100">
                        <i class="bi bi-file-pdf me-1"></i> Laporan Akhir
                    </a>
                    @endif
                    
                    @if($pendaftaran->status == 'aktif' && !$pendaftaran->file_laporan)
                    <hr>
                    <form action="{{ route('mahasiswa.kegiatan-lapangan.upload-laporan', $pendaftaran->hashid) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Upload Laporan Akhir</label>
                            <input type="file" name="file_laporan" class="form-control form-control-sm" accept=".pdf" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-upload me-1"></i> Upload
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Progress / Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-signpost-split me-2"></i>Progress Kegiatan</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 25px;">
                        @php
                            $progressPercent = match($pendaftaran->status) {
                                'menunggu' => 20,
                                'disetujui' => 40,
                                'aktif' => 70,
                                'selesai' => 100,
                                default => 0,
                            };
                        @endphp
                        <div class="progress-bar bg-{{ $config['class'] }}" style="width: {{ $progressPercent }}%">
                            {{ $progressPercent }}%
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col">
                            <div class="p-2 {{ $progressPercent >= 20 ? 'bg-success text-white' : 'bg-light' }} rounded">
                                <i class="bi bi-send d-block"></i>
                                <small>Mendaftar</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 {{ $progressPercent >= 40 ? 'bg-success text-white' : 'bg-light' }} rounded">
                                <i class="bi bi-check-circle d-block"></i>
                                <small>Disetujui</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 {{ $progressPercent >= 70 ? 'bg-success text-white' : 'bg-light' }} rounded">
                                <i class="bi bi-play-circle d-block"></i>
                                <small>Berjalan</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 {{ $progressPercent >= 100 ? 'bg-success text-white' : 'bg-light' }} rounded">
                                <i class="bi bi-trophy d-block"></i>
                                <small>Selesai</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Kegiatan -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-journal-text me-2"></i>Log Kegiatan Harian</h5>
                    @if($pendaftaran->status == 'aktif')
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahLogModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Log
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($pendaftaran->logKegiatan ?? [] as $log)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $log->tanggal->format('d M Y') }}</strong>
                                <span class="badge bg-secondary ms-2">{{ $log->jam_mulai ?? '08:00' }} - {{ $log->jam_selesai ?? '17:00' }}</span>
                            </div>
                            @if($log->is_verified)
                            <span class="badge bg-success"><i class="bi bi-check"></i> Terverifikasi</span>
                            @else
                            <span class="badge bg-warning">Pending</span>
                            @endif
                        </div>
                        <p class="mb-0">{{ $log->kegiatan ?? $log->deskripsi }}</p>
                        @if($log->catatan_pembimbing)
                        <hr class="my-2">
                        <small class="text-muted">
                            <i class="bi bi-chat-left-text me-1"></i> {{ $log->catatan_pembimbing }}
                        </small>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-journal display-4 d-block mb-2"></i>
                        Belum ada log kegiatan
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Penilaian -->
            @if($pendaftaran->status == 'selesai' && ($pendaftaran->nilai_pembimbing || $pendaftaran->nilai_lapangan))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-award me-2"></i>Penilaian</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h6>Nilai Pembimbing</h6>
                            <h2 class="text-primary">{{ $pendaftaran->nilai_pembimbing ?? '-' }}</h2>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6>Nilai Lapangan</h6>
                            <h2 class="text-info">{{ $pendaftaran->nilai_lapangan ?? '-' }}</h2>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6>Nilai Akhir</h6>
                            <h2 class="text-success">{{ $pendaftaran->nilai_akhir ?? '-' }}</h2>
                            @if($pendaftaran->nilai_huruf)
                            <span class="badge bg-success fs-5">{{ $pendaftaran->nilai_huruf }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Pendaftaran Dibuat</h6>
                                <small class="text-muted">{{ $pendaftaran->created_at->format('d M Y H:i') }}</small>
                            </div>
                        </div>
                        
                        @if($pendaftaran->status != 'menunggu')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $pendaftaran->status == 'ditolak' ? 'danger' : 'success' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">{{ $pendaftaran->status == 'ditolak' ? 'Ditolak' : 'Disetujui' }}</h6>
                                @if($pendaftaran->tanggal_verifikasi)
                                <small class="text-muted">{{ $pendaftaran->tanggal_verifikasi->format('d M Y H:i') }}</small>
                                @endif
                                @if($pendaftaran->catatan_admin)
                                <p class="mb-0 mt-1"><small>{{ $pendaftaran->catatan_admin }}</small></p>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if(in_array($pendaftaran->status, ['aktif', 'selesai']))
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Kegiatan Dimulai</h6>
                                @if($pendaftaran->tanggal_mulai)
                                <small class="text-muted">{{ $pendaftaran->tanggal_mulai->format('d M Y') }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($pendaftaran->status == 'selesai')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Kegiatan Selesai</h6>
                                @if($pendaftaran->tanggal_selesai)
                                <small class="text-muted">{{ $pendaftaran->tanggal_selesai->format('d M Y') }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Log -->
@if($pendaftaran->status == 'aktif')
<div class="modal fade" id="tambahLogModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('mahasiswa.kegiatan-lapangan.log.store', $pendaftaran->hashid) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Log Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="08:00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="17:00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kegiatan</label>
                        <textarea name="kegiatan" class="form-control" rows="4" required placeholder="Jelaskan kegiatan yang dilakukan hari ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -25px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.timeline-content {
    padding-left: 10px;
}
</style>
@endsection
