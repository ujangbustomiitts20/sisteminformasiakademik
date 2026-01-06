@extends('layouts.app')

@section('title', 'Detail Evaluasi Kinerja')

@section('content')
<div class="page-title">
    <h4>Detail Evaluasi Kinerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.evaluasi.index') }}">Evaluasi Kinerja</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informasi Evaluasi</h5>
                <span class="badge bg-{{ $evaluasi->predikat_color }} fs-6">{{ $evaluasi->predikat_label }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Nama Pegawai</label>
                        <p class="mb-0 fw-bold">{{ $evaluasi->nama_pegawai }}</p>
                        <small class="text-muted">{{ $evaluasi->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Periode Evaluasi</label>
                        <p class="mb-0 fw-bold">{{ $evaluasi->periode->nama ?? '-' }}</p>
                        <small class="text-muted">{{ $evaluasi->periode->tahun ?? '' }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Evaluasi</label>
                        <p class="mb-0">{{ $evaluasi->tanggal_evaluasi->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Penilai</label>
                        <p class="mb-0">{{ $evaluasi->penilai->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Rincian Penilaian per Kriteria</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kriteria</th>
                                <th class="text-center">Bobot</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center">Nilai Terbobot</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluasi->details as $detail)
                                <tr>
                                    <td>
                                        <strong>{{ $detail->kriteria->nama ?? '-' }}</strong>
                                        @if($detail->kriteria->deskripsi)
                                            <br><small class="text-muted">{{ $detail->kriteria->deskripsi }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $detail->kriteria->bobot ?? 0 }}%</td>
                                    <td class="text-center">{{ $detail->nilai }}</td>
                                    <td class="text-center fw-bold">{{ number_format($detail->nilai_terbobot, 2) }}</td>
                                    <td>{{ $detail->catatan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Nilai Akhir:</th>
                                <th class="text-center fs-5 text-primary">{{ number_format($evaluasi->nilai_akhir, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($evaluasi->catatan)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Catatan Umum</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $evaluasi->catatan }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <h1 class="display-2 mb-2 text-{{ $evaluasi->predikat_color }}">{{ number_format($evaluasi->nilai_akhir, 1) }}</h1>
                <span class="badge bg-{{ $evaluasi->predikat_color }} fs-5">{{ $evaluasi->predikat_label }}</span>
                <hr>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-{{ $evaluasi->predikat_color }}" role="progressbar" style="width: {{ $evaluasi->nilai_akhir }}%">
                        {{ number_format($evaluasi->nilai_akhir, 1) }}%
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('kepegawaian.evaluasi.edit', $evaluasi) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit Evaluasi
                    </a>
                    <button type="button" class="btn btn-success" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Cetak
                    </button>
                    <a href="{{ route('kepegawaian.evaluasi.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
