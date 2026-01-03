@extends('layouts.app')

@section('title', 'Detail Tugas Akhir')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Tugas Akhir</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mahasiswa.tugas-akhir.index') }}">Tugas Akhir</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('mahasiswa.tugas-akhir.index') }}" class="btn btn-secondary">
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
                            'diajukan' => ['class' => 'warning', 'icon' => 'hourglass-split', 'label' => 'Menunggu Persetujuan'],
                            'disetujui' => ['class' => 'success', 'icon' => 'check-circle', 'label' => 'Disetujui'],
                            'ditolak' => ['class' => 'danger', 'icon' => 'x-circle', 'label' => 'Ditolak'],
                            'bimbingan' => ['class' => 'info', 'icon' => 'journal-text', 'label' => 'Dalam Bimbingan'],
                            'seminar' => ['class' => 'primary', 'icon' => 'easel', 'label' => 'Seminar Proposal'],
                            'revisi_seminar' => ['class' => 'warning', 'icon' => 'pencil-square', 'label' => 'Revisi Seminar'],
                            'sidang' => ['class' => 'info', 'icon' => 'mortarboard', 'label' => 'Sidang'],
                            'revisi_sidang' => ['class' => 'warning', 'icon' => 'pencil-square', 'label' => 'Revisi Sidang'],
                            'lulus' => ['class' => 'success', 'icon' => 'trophy', 'label' => 'Lulus'],
                        ];
                        $config = $statusConfig[$tugasAkhir->status] ?? ['class' => 'secondary', 'icon' => 'question-circle', 'label' => ucfirst($tugasAkhir->status)];
                    @endphp
                    <div class="display-4 text-{{ $config['class'] }} mb-3">
                        <i class="bi bi-{{ $config['icon'] }}"></i>
                    </div>
                    <h4 class="mb-1">{{ $config['label'] }}</h4>
                    <p class="text-muted mb-0">No: {{ $tugasAkhir->nomor_ta ?? '-' }}</p>
                </div>
            </div>

            <!-- Info TA -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Informasi TA</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Tanggal Pengajuan</td>
                            <td>{{ $tugasAkhir->tanggal_pengajuan?->format('d M Y') ?? $tugasAkhir->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Akademik</td>
                            <td>{{ $tugasAkhir->tahun_akademik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Semester</td>
                            <td>{{ $tugasAkhir->semester ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bidang Kajian</td>
                            <td>{{ $tugasAkhir->bidang_kajian ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metodologi</td>
                            <td>{{ $tugasAkhir->metodologi ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Pembimbing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-person-badge me-2"></i>Dosen Pembimbing</h5>
                </div>
                <div class="card-body">
                    @if($tugasAkhir->bimbinganTAs && $tugasAkhir->bimbinganTAs->count() > 0)
                        @foreach($tugasAkhir->bimbinganTAs as $bimbingan)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <strong>{{ $bimbingan->dosen->nama ?? '-' }}</strong><br>
                                <small class="text-muted">Pembimbing {{ $bimbingan->urutan ?? '-' }}</small>
                            </div>
                        </div>
                        @endforeach
                    @elseif($tugasAkhir->pembimbing1 || $tugasAkhir->pembimbing2)
                        @if($tugasAkhir->pembimbing1)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <strong>{{ $tugasAkhir->pembimbing1->nama ?? '-' }}</strong><br>
                                <small class="text-muted">Pembimbing 1</small>
                            </div>
                        </div>
                        @endif
                        @if($tugasAkhir->pembimbing2)
                        <div class="d-flex align-items-center">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <strong>{{ $tugasAkhir->pembimbing2->nama ?? '-' }}</strong><br>
                                <small class="text-muted">Pembimbing 2</small>
                            </div>
                        </div>
                        @endif
                    @else
                        <p class="text-muted text-center mb-0">Belum ditentukan</p>
                    @endif
                </div>
            </div>

            <!-- Dokumen -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-file-earmark me-2"></i>Dokumen</h5>
                </div>
                <div class="card-body">
                    @if($tugasAkhir->file_proposal)
                    <a href="{{ Storage::url($tugasAkhir->file_proposal) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2 w-100">
                        <i class="bi bi-file-pdf me-1"></i> Proposal
                    </a>
                    @endif
                    @if($tugasAkhir->file_draft)
                    <a href="{{ Storage::url($tugasAkhir->file_draft) }}" target="_blank" class="btn btn-outline-info btn-sm mb-2 w-100">
                        <i class="bi bi-file-pdf me-1"></i> Draft TA
                    </a>
                    @endif
                    @if($tugasAkhir->file_final)
                    <a href="{{ Storage::url($tugasAkhir->file_final) }}" target="_blank" class="btn btn-outline-success btn-sm w-100">
                        <i class="bi bi-file-pdf me-1"></i> Dokumen Final
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Judul dan Abstrak -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-journal-text me-2"></i>Judul & Abstrak</h5>
                </div>
                <div class="card-body">
                    <h5 class="text-primary mb-3">{{ $tugasAkhir->judul }}</h5>
                    @if($tugasAkhir->abstrak)
                    <div class="bg-light p-3 rounded">
                        <h6 class="mb-2">Abstrak</h6>
                        <p class="mb-0" style="text-align: justify;">{{ $tugasAkhir->abstrak }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Progress Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-signpost-split me-2"></i>Progress</h5>
                </div>
                <div class="card-body">
                    @php
                        $steps = [
                            'diajukan' => 'Pengajuan',
                            'disetujui' => 'Disetujui',
                            'bimbingan' => 'Bimbingan',
                            'seminar' => 'Seminar',
                            'sidang' => 'Sidang',
                            'lulus' => 'Lulus',
                        ];
                        $currentStep = array_search($tugasAkhir->status, array_keys($steps));
                        if ($currentStep === false) $currentStep = 0;
                    @endphp
                    <div class="row text-center">
                        @foreach($steps as $key => $label)
                        @php $stepIndex = array_search($key, array_keys($steps)); @endphp
                        <div class="col">
                            <div class="p-2 {{ $stepIndex <= $currentStep ? 'bg-success text-white' : 'bg-light' }} rounded mb-2">
                                @if($stepIndex < $currentStep)
                                <i class="bi bi-check-lg d-block"></i>
                                @elseif($stepIndex == $currentStep)
                                <i class="bi bi-circle-fill d-block"></i>
                                @else
                                <i class="bi bi-circle d-block"></i>
                                @endif
                            </div>
                            <small class="{{ $stepIndex <= $currentStep ? 'fw-bold' : 'text-muted' }}">{{ $label }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Riwayat Bimbingan -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-chat-left-dots me-2"></i>Riwayat Bimbingan</h5>
                    @if(in_array($tugasAkhir->status, ['bimbingan', 'disetujui']))
                    <a href="{{ route('mahasiswa.tugas-akhir.bimbingan.create', $tugasAkhir->hashid) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Ajukan Bimbingan
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($tugasAkhir->logBimbingan ?? [] as $log)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $log->tanggal?->format('d M Y') }}</strong>
                                <span class="badge bg-secondary ms-2">{{ $log->dosen->nama ?? 'Pembimbing' }}</span>
                            </div>
                            <span class="badge bg-{{ $log->status == 'selesai' ? 'success' : 'warning' }}">
                                {{ ucfirst($log->status ?? 'pending') }}
                            </span>
                        </div>
                        <p class="mb-0">{{ $log->catatan ?? $log->deskripsi }}</p>
                        @if($log->progress)
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar" style="width: {{ $log->progress }}%"></div>
                        </div>
                        <small class="text-muted">Progress: {{ $log->progress }}%</small>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-chat-left display-4 d-block mb-2"></i>
                        Belum ada riwayat bimbingan
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Seminar & Sidang Info -->
            @if($tugasAkhir->seminar || $tugasAkhir->sidang)
            <div class="row">
                @if($tugasAkhir->seminar)
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0"><i class="bi bi-easel me-2"></i>Seminar Proposal</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Tanggal</td>
                                    <td>{{ $tugasAkhir->seminar->tanggal?->format('d M Y') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Waktu</td>
                                    <td>{{ $tugasAkhir->seminar->waktu_mulai ?? '-' }} - {{ $tugasAkhir->seminar->waktu_selesai ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Ruangan</td>
                                    <td>{{ $tugasAkhir->seminar->ruangan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td><span class="badge bg-{{ $tugasAkhir->seminar->status == 'lulus' ? 'success' : 'warning' }}">{{ ucfirst($tugasAkhir->seminar->status ?? '-') }}</span></td>
                                </tr>
                                @if($tugasAkhir->seminar->nilai)
                                <tr>
                                    <td class="text-muted">Nilai</td>
                                    <td><strong>{{ $tugasAkhir->seminar->nilai }}</strong></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if($tugasAkhir->sidang)
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0"><i class="bi bi-mortarboard me-2"></i>Sidang TA</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Tanggal</td>
                                    <td>{{ $tugasAkhir->sidang->tanggal?->format('d M Y') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Waktu</td>
                                    <td>{{ $tugasAkhir->sidang->waktu_mulai ?? '-' }} - {{ $tugasAkhir->sidang->waktu_selesai ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Ruangan</td>
                                    <td>{{ $tugasAkhir->sidang->ruangan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td><span class="badge bg-{{ $tugasAkhir->sidang->status == 'lulus' ? 'success' : 'warning' }}">{{ ucfirst($tugasAkhir->sidang->status ?? '-') }}</span></td>
                                </tr>
                                @if($tugasAkhir->sidang->nilai_akhir)
                                <tr>
                                    <td class="text-muted">Nilai Akhir</td>
                                    <td><strong class="text-success fs-4">{{ $tugasAkhir->sidang->nilai_akhir }}</strong></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Catatan -->
            @if($tugasAkhir->catatan || $tugasAkhir->alasan_penolakan)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-chat-left-text me-2"></i>Catatan</h5>
                </div>
                <div class="card-body">
                    @if($tugasAkhir->alasan_penolakan)
                    <div class="alert alert-danger mb-0">
                        <strong>Alasan Penolakan:</strong><br>
                        {{ $tugasAkhir->alasan_penolakan }}
                    </div>
                    @elseif($tugasAkhir->catatan)
                    <p class="mb-0">{{ $tugasAkhir->catatan }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
