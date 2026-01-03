@extends('layouts.app')

@section('title', 'Detail Rekap Presensi')

@section('content')
<div class="page-title">
    <h4>Detail Rekap Presensi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.presensi.index') }}">Presensi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.presensi.rekap') }}">Rekap Bulanan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Info Pegawai & Summary -->
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Informasi Pegawai
            </div>
            <div class="card-body text-center">
                <div class="avatar avatar-xl bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    @if($rekap->dosen)
                    {{ strtoupper(substr($rekap->dosen->nama, 0, 1)) }}
                    @elseif($rekap->pegawai)
                    {{ strtoupper(substr($rekap->pegawai->nama, 0, 1)) }}
                    @endif
                </div>
                
                @if($rekap->dosen)
                <h5 class="mb-1">{{ $rekap->dosen->nama_lengkap }}</h5>
                <p class="text-muted mb-2">
                    <span class="badge bg-info">Dosen</span>
                </p>
                <p class="small text-muted mb-0">NIDN: {{ $rekap->dosen->nidn ?? '-' }}</p>
                <p class="small text-muted">NIP: {{ $rekap->dosen->nip ?? '-' }}</p>
                @elseif($rekap->pegawai)
                <h5 class="mb-1">{{ $rekap->pegawai->nama }}</h5>
                <p class="text-muted mb-2">
                    <span class="badge bg-secondary">Tendik</span>
                </p>
                <p class="small text-muted mb-0">NIP: {{ $rekap->pegawai->nip ?? '-' }}</p>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-calendar-month me-2"></i>Periode
            </div>
            <div class="card-body text-center">
                <h4 class="text-primary mb-0">{{ $namaBulan[$rekap->bulan] ?? '' }} {{ $rekap->tahun }}</h4>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Ringkasan Kehadiran
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Persentase Kehadiran</span>
                    <span class="badge {{ $rekap->persentase_kehadiran >= 90 ? 'bg-success' : ($rekap->persentase_kehadiran >= 75 ? 'bg-warning text-dark' : 'bg-danger') }} fs-6">
                        {{ number_format($rekap->persentase_kehadiran, 1) }}%
                    </span>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar {{ $rekap->persentase_kehadiran >= 90 ? 'bg-success' : ($rekap->persentase_kehadiran >= 75 ? 'bg-warning' : 'bg-danger') }}" 
                         style="width: {{ $rekap->persentase_kehadiran }}%"></div>
                </div>
                
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td><span class="badge bg-light text-success">●</span> Hadir</td>
                        <td class="text-end fw-bold">{{ $rekap->hadir ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-light text-warning">●</span> Terlambat</td>
                        <td class="text-end fw-bold">{{ $rekap->terlambat ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-light text-info">●</span> Izin</td>
                        <td class="text-end fw-bold">{{ $rekap->izin ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-light text-secondary">●</span> Sakit</td>
                        <td class="text-end fw-bold">{{ $rekap->sakit ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-light text-primary">●</span> Cuti</td>
                        <td class="text-end fw-bold">{{ $rekap->cuti ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-light text-dark">●</span> Dinas Luar</td>
                        <td class="text-end fw-bold">{{ $rekap->dinas_luar ?? 0 }} hari</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-light text-danger">●</span> Alpha</td>
                        <td class="text-end fw-bold">{{ $rekap->alpha ?? 0 }} hari</td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Total Hari Kerja</strong></td>
                        <td class="text-end fw-bold">{{ $rekap->total_hari_kerja ?? 0 }} hari</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($jamKerja)
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock me-2"></i>Jam Kerja
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td>Jam Masuk</td>
                        <td class="text-end fw-bold">{{ \Carbon\Carbon::parse($jamKerja->jam_masuk)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Jam Keluar</td>
                        <td class="text-end fw-bold">{{ \Carbon\Carbon::parse($jamKerja->jam_keluar)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Toleransi</td>
                        <td class="text-end fw-bold">{{ $jamKerja->toleransi_terlambat }} menit</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Detail Presensi Harian -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-check me-2"></i>Detail Presensi Harian - {{ $namaBulan[$rekap->bulan] ?? '' }} {{ $rekap->tahun }}</span>
                <a href="{{ route('kepegawaian.presensi.rekap', ['bulan' => $rekap->bulan, 'tahun' => $rekap->tahun]) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th class="text-center">Jam Masuk</th>
                                <th class="text-center">Jam Keluar</th>
                                <th class="text-center">Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presensiHarian as $index => $presensi)
                            @php
                                $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                $hari = $namaHari[$presensi->tanggal->dayOfWeek];
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $presensi->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $hari }}</td>
                                <td class="text-center">
                                    @if($presensi->jam_masuk)
                                    {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($presensi->jam_keluar)
                                    {{ \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @switch($presensi->status)
                                        @case('hadir')
                                            <span class="badge bg-success">Hadir</span>
                                            @break
                                        @case('terlambat')
                                            <span class="badge bg-warning text-dark">Terlambat</span>
                                            @break
                                        @case('izin')
                                            <span class="badge bg-info">Izin</span>
                                            @break
                                        @case('sakit')
                                            <span class="badge bg-secondary">Sakit</span>
                                            @break
                                        @case('cuti')
                                            <span class="badge bg-primary">Cuti</span>
                                            @break
                                        @case('dinas_luar')
                                            <span class="badge bg-dark">Dinas Luar</span>
                                            @break
                                        @case('alpha')
                                            <span class="badge bg-danger">Alpha</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">{{ ucfirst($presensi->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <small>{{ $presensi->keterangan ?? '-' }}</small>
                                    @if($presensi->is_manual)
                                    <br><small class="text-muted"><i class="bi bi-pencil"></i> Input Manual</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0 mt-2">Tidak ada data presensi untuk periode ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
