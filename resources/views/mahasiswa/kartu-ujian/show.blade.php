@extends('layouts.app')

@section('title', 'Detail Kartu Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Detail Kartu Ujian</h1>
            <p class="text-muted mb-0">{{ $kartuUjian->nomor_kartu }}</p>
        </div>
        <div>
            @if($kartuUjian->eligible && $kartuUjian->periodeUjian->isBisaCetakKartu())
            <a href="{{ route('mahasiswa.kartu-ujian.cetak', $kartuUjian->hashid) }}" class="btn btn-primary me-2">
                <i class="bi bi-printer me-1"></i>Cetak PDF
            </a>
            @endif
            <a href="{{ route('mahasiswa.kartu-ujian.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    @if(!$kartuUjian->eligible)
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Anda tidak eligible untuk mengikuti ujian!</strong><br>
        Alasan: {{ $kartuUjian->alasan_tidak_eligible ?? 'Tidak memenuhi syarat' }}
    </div>
    @endif

    <!-- Info Mahasiswa & Periode -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="120">NIM</td>
                            <td><strong>{{ $kartuUjian->mahasiswa->nim }}</strong></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td><strong>{{ $kartuUjian->mahasiswa->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td>Program Studi</td>
                            <td>{{ $kartuUjian->mahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Fakultas</td>
                            <td>{{ $kartuUjian->mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Info Periode Ujian</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="120">Periode</td>
                            <td><strong>{{ $kartuUjian->periodeUjian->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td>Jenis</td>
                            <td><span class="badge bg-info">{{ $kartuUjian->periodeUjian->jenis }}</span></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>{{ $kartuUjian->periodeUjian->tanggal_mulai->format('d M') }} - {{ $kartuUjian->periodeUjian->tanggal_selesai->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                @if($kartuUjian->eligible)
                                    <span class="badge bg-success">Eligible</span>
                                @else
                                    <span class="badge bg-danger">Tidak Eligible</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Kehadiran & Pembayaran -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card {{ $kartuUjian->persentase_kehadiran >= $kartuUjian->periodeUjian->minimal_kehadiran ? 'border-success' : 'border-danger' }}">
                <div class="card-body text-center">
                    <h6 class="text-muted">Persentase Kehadiran</h6>
                    <h2 class="{{ $kartuUjian->persentase_kehadiran >= $kartuUjian->periodeUjian->minimal_kehadiran ? 'text-success' : 'text-danger' }}">
                        {{ number_format($kartuUjian->persentase_kehadiran, 1) }}%
                    </h2>
                    <small class="text-muted">Minimal: {{ $kartuUjian->periodeUjian->minimal_kehadiran }}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card {{ $kartuUjian->pembayaran_lunas ? 'border-success' : 'border-danger' }}">
                <div class="card-body text-center">
                    <h6 class="text-muted">Status Pembayaran</h6>
                    @if($kartuUjian->pembayaran_lunas)
                        <h2 class="text-success"><i class="bi bi-check-circle"></i> Lunas</h2>
                    @else
                        <h2 class="text-danger"><i class="bi bi-x-circle"></i> Belum Lunas</h2>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Mata Kuliah -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Daftar Mata Kuliah Ujian</h5>
        </div>
        <div class="card-body">
            @if($kartuUjian->detailKartuUjian->isEmpty())
                <p class="text-center text-muted py-4">Tidak ada jadwal ujian yang terkait.</p>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Hari/Tanggal</th>
                            <th>Waktu</th>
                            <th>Ruangan</th>
                            <th>Kehadiran</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kartuUjian->detailKartuUjian as $i => $detail)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <strong>{{ $detail->jadwalUjian->mataKuliah->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $detail->jadwalUjian->mataKuliah->kode ?? '' }}</small>
                            </td>
                            <td>{{ $detail->jadwalUjian->mataKuliah->sks ?? '-' }}</td>
                            <td>{{ $detail->jadwalUjian->tanggal?->format('l, d M Y') ?? '-' }}</td>
                            <td>{{ $detail->jadwalUjian->waktu_mulai ?? '-' }} - {{ $detail->jadwalUjian->waktu_selesai ?? '-' }}</td>
                            <td>{{ $detail->jadwalUjian->ruangan->nama ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $detail->persentase_kehadiran >= $kartuUjian->periodeUjian->minimal_kehadiran ? 'success' : 'danger' }}">
                                    {{ number_format($detail->persentase_kehadiran, 1) }}%
                                </span>
                            </td>
                            <td>
                                @if($detail->eligible)
                                    <span class="badge bg-success"><i class="bi bi-check"></i></span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x"></i></span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
