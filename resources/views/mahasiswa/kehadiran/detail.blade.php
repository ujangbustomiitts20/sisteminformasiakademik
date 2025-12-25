@extends('layouts.app')

@section('title', 'Detail Kehadiran - ' . $mataKuliah->nama)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Detail Kehadiran</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mahasiswa.kehadiran') }}">Kehadiran</a></li>
                <li class="breadcrumb-item active">{{ $mataKuliah->kode }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('mahasiswa.kehadiran') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Info Mata Kuliah -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-book me-2"></i>Informasi Mata Kuliah
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="35%">Kode</td>
                        <td><code>{{ $mataKuliah->kode }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama MK</td>
                        <td><strong>{{ $mataKuliah->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">SKS</td>
                        <td>{{ $mataKuliah->sks }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>{{ $tahunAkademik->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($rekap)
        <!-- Ringkasan Kehadiran -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-pie-chart me-2"></i>Ringkasan Kehadiran
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h1 class="display-4 text-{{ $rekap->persentase_badge }} mb-0">
                        {{ number_format($rekap->persentase_kehadiran, 1) }}%
                    </h1>
                    <small class="text-muted">Persentase Kehadiran</small>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6 mb-2">
                        <div class="bg-success text-white rounded p-2">
                            <h5 class="mb-0">{{ $rekap->jumlah_hadir }}</h5>
                            <small>Hadir</small>
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <div class="bg-info text-white rounded p-2">
                            <h5 class="mb-0">{{ $rekap->jumlah_izin }}</h5>
                            <small>Izin</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-warning text-dark rounded p-2">
                            <h5 class="mb-0">{{ $rekap->jumlah_sakit }}</h5>
                            <small>Sakit</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-danger text-white rounded p-2">
                            <h5 class="mb-0">{{ $rekap->jumlah_alpa }}</h5>
                            <small>Alpa</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    @if($rekap->memenuhi_syarat_ujian)
                    <span class="badge bg-success fs-6">
                        <i class="bi bi-check-circle me-1"></i>Layak Mengikuti Ujian
                    </span>
                    @else
                    <span class="badge bg-danger fs-6">
                        <i class="bi bi-x-circle me-1"></i>Tidak Layak Ujian
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-8">
        <!-- Riwayat Kehadiran -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list me-2"></i>Riwayat Kehadiran
            </div>
            <div class="card-body p-0">
                @if($absensiList->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="8%">Pertemuan</th>
                                <th width="15%">Tanggal</th>
                                <th>Materi</th>
                                <th width="12%">Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absensiList as $absensi)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $absensi->pertemuan ?? ($absensi->pertemuanData->pertemuan_ke ?? '-') }}</span>
                                </td>
                                <td>
                                    {{ $absensi->tanggal ? $absensi->tanggal->format('d M Y') : '-' }}
                                </td>
                                <td>{{ Str::limit($absensi->materi ?? ($absensi->pertemuanData->materi ?? '-'), 50) }}</td>
                                <td>
                                    @php
                                        $statusBadge = match($absensi->status) {
                                            'Hadir' => 'success',
                                            'Izin' => 'info',
                                            'Sakit' => 'warning',
                                            'Alpa', 'Alpha' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusBadge }}">
                                        {{ $absensi->status }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $absensi->keterangan ?? '-' }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">Belum ada data kehadiran</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
