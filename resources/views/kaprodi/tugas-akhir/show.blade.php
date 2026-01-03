@extends('layouts.app')

@section('title', 'Detail Tugas Akhir')

@section('content')
<div class="page-title">
    <h4>Detail Tugas Akhir</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.tugas-akhir.index') }}">Tugas Akhir</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Tugas Akhir</h6>
            </div>
            <div class="card-body">
                <h5>{{ $tugasAkhir->judul }}</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" width="40%">Mahasiswa</td>
                                <td>{{ $tugasAkhir->mahasiswa->nama }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIM</td>
                                <td>{{ $tugasAkhir->mahasiswa->nim }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pembimbing 1</td>
                                <td>{{ $tugasAkhir->pembimbing1->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pembimbing 2</td>
                                <td>{{ $tugasAkhir->pembimbing2->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" width="40%">Status</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'diajukan' => 'warning',
                                            'judul_disetujui' => 'success',
                                            'judul_ditolak' => 'danger',
                                            'proposal_diajukan' => 'info',
                                            'proposal_disetujui' => 'success',
                                            'proposal_revisi' => 'warning',
                                            'penelitian' => 'primary',
                                            'sidang_diajukan' => 'info',
                                            'sidang_dijadwalkan' => 'primary',
                                            'lulus' => 'success',
                                            'lulus_revisi' => 'warning',
                                            'tidak_lulus' => 'danger',
                                            'selesai' => 'success',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$tugasAkhir->status] ?? 'secondary' }}">
                                        {{ ucfirst($tugasAkhir->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Pengajuan</td>
                                <td>{{ $tugasAkhir->created_at->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tgl Persetujuan Judul</td>
                                <td>{{ $tugasAkhir->tanggal_approval_judul ? $tugasAkhir->tanggal_approval_judul->format('d M Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($tugasAkhir->abstrak)
                <div class="mt-3">
                    <strong>Abstrak:</strong>
                    <p class="text-muted">{{ $tugasAkhir->abstrak }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Riwayat Bimbingan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Riwayat Bimbingan</h6>
            </div>
            <div class="card-body">
                @if($tugasAkhir->bimbingan && $tugasAkhir->bimbingan->count() > 0)
                <div class="timeline">
                    @foreach($tugasAkhir->bimbingan->sortByDesc('tanggal') as $bimbingan)
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-marker bg-primary rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 5px;"></div>
                            <div>
                                <small class="text-muted">{{ $bimbingan->tanggal ? $bimbingan->tanggal->format('d M Y') : '-' }}</small>
                                <p class="mb-0">{{ $bimbingan->materi_bimbingan ?? $bimbingan->hasil_bimbingan ?? '-' }}</p>
                                @if($bimbingan->catatan_dosen)
                                <small class="text-info">Catatan: {{ $bimbingan->catatan_dosen }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0">Belum ada riwayat bimbingan</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Approval Judul (jika status diajukan) -->
        @if($tugasAkhir->status == 'diajukan')
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-check2-circle me-2"></i>Approval Judul</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('kaprodi.tugas-akhir.approval', $tugasAkhir) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Keputusan</label>
                        <select name="status" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="disetujui">Setujui Judul</option>
                            <option value="ditolak">Tolak Judul</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan/saran untuk mahasiswa..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send me-2"></i>Submit
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Info Sidang -->
        @if($tugasAkhir->sidang)
        @php $sidang = $tugasAkhir->sidang; @endphp
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Info Sidang</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Tanggal Sidang</small>
                    <p class="mb-1">{{ $sidang->tanggal ? $sidang->tanggal->format('d M Y H:i') : '-' }}</p>
                    <small class="text-muted">Status</small>
                    <p class="mb-1">
                        <span class="badge bg-{{ $sidang->status == 'lulus' ? 'success' : ($sidang->status == 'tidak_lulus' ? 'danger' : 'warning') }}">
                            {{ ucfirst(str_replace('_', ' ', $sidang->status)) }}
                        </span>
                    </p>
                    @if($sidang->nilai_akhir)
                    <small class="text-muted">Nilai</small>
                    <p class="mb-0"><strong>{{ $sidang->nilai_akhir }} ({{ $sidang->grade ?? '-' }})</strong></p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Aksi</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('kaprodi.tugas-akhir.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
