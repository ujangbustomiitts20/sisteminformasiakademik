@extends('layouts.app')

@section('title', 'Detail EDOM')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Detail Evaluasi</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dosen.edom.index') }}">Hasil EDOM</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('dosen.edom.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Info Mata Kuliah -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-book me-2"></i>Informasi Mata Kuliah</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Mata Kuliah</label>
                        <p class="fw-semibold mb-0">{{ $rekapEdom->jadwalKuliah->mataKuliah->nama ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small">Kode</label>
                        <p class="fw-semibold mb-0">{{ $rekapEdom->jadwalKuliah->mataKuliah->kode ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small">Kelas</label>
                        <p class="fw-semibold mb-0">{{ $rekapEdom->jadwalKuliah->kelas->nama ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Periode EDOM</label>
                        <p class="fw-semibold mb-0">{{ $rekapEdom->periodeEdom->nama ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small">Jumlah Responden</label>
                        <p class="fw-semibold mb-0">{{ $rekapEdom->jumlah_responden }} mahasiswa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nilai Per Aspek -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Nilai Per Aspek Kompetensi</h6>
            </div>
            <div class="card-body">
                @php
                    $aspekList = [
                        ['field' => 'rata_rata_pedagogik', 'label' => 'Kompetensi Pedagogik', 'color' => 'primary', 'desc' => 'Kemampuan mengelola pembelajaran'],
                        ['field' => 'rata_rata_profesional', 'label' => 'Kompetensi Profesional', 'color' => 'success', 'desc' => 'Penguasaan materi pembelajaran'],
                        ['field' => 'rata_rata_kepribadian', 'label' => 'Kompetensi Kepribadian', 'color' => 'warning', 'desc' => 'Sikap dan kepribadian dosen'],
                        ['field' => 'rata_rata_sosial', 'label' => 'Kompetensi Sosial', 'color' => 'info', 'desc' => 'Kemampuan berkomunikasi dan berinteraksi'],
                    ];
                @endphp
                
                @foreach($aspekList as $aspek)
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <strong>{{ $aspek['label'] }}</strong>
                            <br><small class="text-muted">{{ $aspek['desc'] }}</small>
                        </div>
                        <span class="badge bg-{{ $aspek['color'] }} fs-6">{{ number_format($rekapEdom->{$aspek['field']}, 2) }}</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-{{ $aspek['color'] }}" style="width: {{ ($rekapEdom->{$aspek['field']} / 5) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Komentar/Saran Mahasiswa -->
        @if($komentar->isNotEmpty())
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-chat-quote me-2"></i>Komentar & Saran Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-shield-check me-2"></i>
                    <small>Identitas mahasiswa dirahasiakan untuk menjaga objektivitas evaluasi.</small>
                </div>
                
                @foreach($komentar as $index => $komen)
                <div class="border-start border-3 border-primary ps-3 mb-3">
                    <p class="mb-1">{{ $komen }}</p>
                    <small class="text-muted">— Mahasiswa #{{ $index + 1 }}</small>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Ringkasan -->
        <div class="card shadow-sm border-{{ $rekapEdom->kategori_badge }} mb-4">
            <div class="card-header bg-{{ $rekapEdom->kategori_badge }} text-white py-3">
                <h6 class="mb-0"><i class="bi bi-award me-2"></i>Ringkasan</h6>
            </div>
            <div class="card-body text-center">
                <div class="display-3 fw-bold text-{{ $rekapEdom->kategori_badge }} mb-2">
                    {{ number_format($rekapEdom->rata_rata_total, 2) }}
                </div>
                <span class="badge bg-{{ $rekapEdom->kategori_badge }} fs-5 mb-3">{{ $rekapEdom->kategori }}</span>
                
                <div class="progress mb-3" style="height: 15px;">
                    <div class="progress-bar bg-{{ $rekapEdom->kategori_badge }}" style="width: {{ ($rekapEdom->rata_rata_total / 5) * 100 }}%"></div>
                </div>
                
                <small class="text-muted">Skala nilai: 1 - 5</small>
            </div>
        </div>

        <!-- Skala Penilaian -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Skala Penilaian</h6>
            </div>
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span><span class="badge bg-success me-2">≥ 4.5</span> Sangat Baik</span>
                    <i class="bi bi-star-fill text-success"></i>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span><span class="badge bg-primary me-2">≥ 3.5</span> Baik</span>
                    <i class="bi bi-star-fill text-primary"></i>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span><span class="badge bg-warning me-2">≥ 2.5</span> Cukup</span>
                    <i class="bi bi-star-half text-warning"></i>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span><span class="badge bg-danger me-2">≥ 1.5</span> Kurang</span>
                    <i class="bi bi-star text-danger"></i>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span><span class="badge bg-dark me-2">&lt; 1.5</span> Sangat Kurang</span>
                    <i class="bi bi-x-circle text-dark"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
