@extends('layouts.app')

@section('title', 'Bimbingan Tugas Akhir')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Bimbingan Tugas Akhir</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mahasiswa.tugas-akhir.index') }}">Tugas Akhir</a></li>
                    <li class="breadcrumb-item active">Bimbingan</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('mahasiswa.tugas-akhir.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
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
        <!-- Request Bimbingan Form -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Request Bimbingan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('mahasiswa.tugas-akhir.bimbingan.request', $tugasAkhir) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Dosen Pembimbing <span class="text-danger">*</span></label>
                            <select name="dosen_id" class="form-select" required>
                                <option value="">Pilih Dosen</option>
                                @if($tugasAkhir->pembimbing1)
                                <option value="{{ $tugasAkhir->pembimbing_1_id }}">
                                    {{ $tugasAkhir->pembimbing1->nama }} (Pembimbing 1)
                                </option>
                                @endif
                                @if($tugasAkhir->pembimbing2)
                                <option value="{{ $tugasAkhir->pembimbing_2_id }}">
                                    {{ $tugasAkhir->pembimbing2->nama }} (Pembimbing 2)
                                </option>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Materi Bimbingan <span class="text-danger">*</span></label>
                            <textarea name="materi_bimbingan" class="form-control" rows="3" required placeholder="Jelaskan materi/topik yang ingin dibahas..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send"></i> Request Bimbingan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistik -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Statistik Bimbingan</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalBimbingan = $bimbingans->count();
                        $selesai = $bimbingans->where('status', 'selesai')->count();
                        $dijadwalkan = $bimbingans->where('status', 'dijadwalkan')->count();
                        $dibatalkan = $bimbingans->where('status', 'dibatalkan')->count();
                        $avgProgress = $bimbingans->where('persentase_progress', '>', 0)->avg('persentase_progress') ?? 0;
                    @endphp
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h4 class="mb-0 text-primary">{{ $totalBimbingan }}</h4>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="mb-0 text-success">{{ $selesai }}</h4>
                            <small class="text-muted">Selesai</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0 text-warning">{{ $dijadwalkan }}</h4>
                            <small class="text-muted">Dijadwalkan</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0 text-info">{{ number_format($avgProgress, 0) }}%</h4>
                            <small class="text-muted">Avg Progress</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Bimbingan -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Riwayat Bimbingan</h6>
                    <a href="{{ route('mahasiswa.tugas-akhir.cetak-kartu-bimbingan', $tugasAkhir) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-printer"></i> Cetak Kartu
                    </a>
                </div>
                <div class="card-body">
                    @forelse($bimbingans as $bimbingan)
                    <div class="card mb-3 border-{{ $bimbingan->status == 'selesai' ? 'success' : ($bimbingan->status == 'dibatalkan' ? 'danger' : 'warning') }}">
                        <div class="card-header py-2 bg-{{ $bimbingan->status == 'selesai' ? 'success' : ($bimbingan->status == 'dibatalkan' ? 'danger' : 'warning') }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-calendar"></i> {{ $bimbingan->tanggal->format('d M Y') }}
                                    @if($bimbingan->waktu_mulai)
                                    - {{ $bimbingan->waktu_mulai }}
                                    @endif
                                </span>
                                <span class="badge bg-light text-dark">{{ ucfirst($bimbingan->status) }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="mb-1"><strong>Dengan:</strong> {{ $bimbingan->dosen->nama ?? '-' }}</p>
                                    @if($bimbingan->tempat)
                                    <p class="mb-1"><strong>Tempat:</strong> {{ $bimbingan->tempat }}</p>
                                    @endif
                                    <p class="mb-1"><strong>Materi:</strong> {{ $bimbingan->materi_bimbingan }}</p>
                                    
                                    @if($bimbingan->hasil_bimbingan)
                                    <hr class="my-2">
                                    <p class="mb-1"><strong>Hasil:</strong> {{ $bimbingan->hasil_bimbingan }}</p>
                                    @endif
                                    
                                    @if($bimbingan->catatan_dosen)
                                    <p class="mb-1 text-muted"><strong>Catatan Dosen:</strong> {{ $bimbingan->catatan_dosen }}</p>
                                    @endif
                                    
                                    @if($bimbingan->rencana_selanjutnya)
                                    <p class="mb-0"><strong>Rencana Selanjutnya:</strong> {{ $bimbingan->rencana_selanjutnya }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4 text-center">
                                    @if($bimbingan->persentase_progress)
                                    <h6>Progress</h6>
                                    <div class="progress mb-2" style="height: 25px;">
                                        <div class="progress-bar bg-success" style="width: {{ $bimbingan->persentase_progress }}%">
                                            {{ $bimbingan->persentase_progress }}%
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                        <h5 class="mt-3">Belum Ada Bimbingan</h5>
                        <p class="text-muted">Silakan request jadwal bimbingan dengan dosen pembimbing Anda.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
