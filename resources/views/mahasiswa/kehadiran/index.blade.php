@extends('layouts.app')

@section('title', 'Rekap Kehadiran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Rekap Kehadiran</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kehadiran</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Filter Semester -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label mb-0">Pilih Semester</label>
            </div>
            <div class="col-md-4">
                <select name="tahun_akademik" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih Semester --</option>
                    @foreach($tahunAkademiks as $ta)
                    <option value="{{ $ta->id }}" {{ $tahunAkademik && $tahunAkademik->id == $ta->id ? 'selected' : '' }}>
                        {{ $ta->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($stats)
<!-- Statistik Kehadiran -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Total Pertemuan</small>
                        <h4 class="mb-0">{{ $stats['total_pertemuan'] }}</h4>
                    </div>
                    <i class="bi bi-calendar-check" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Hadir</small>
                        <h4 class="mb-0">{{ $stats['total_hadir'] }}</h4>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Izin/Sakit</small>
                        <h4 class="mb-0">{{ $stats['total_izin'] + $stats['total_sakit'] }}</h4>
                    </div>
                    <i class="bi bi-exclamation-circle" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Alpa</small>
                        <h4 class="mb-0">{{ $stats['total_alpa'] }}</h4>
                    </div>
                    <i class="bi bi-x-circle" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Persentase Kehadiran Total -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Persentase Kehadiran Total</h6>
            <span class="badge bg-{{ $stats['persentase_kehadiran'] >= 80 ? 'success' : ($stats['persentase_kehadiran'] >= 70 ? 'warning' : 'danger') }} fs-6">
                {{ number_format($stats['persentase_kehadiran'], 1) }}%
            </span>
        </div>
        <div class="progress" style="height: 20px;">
            <div class="progress-bar bg-{{ $stats['persentase_kehadiran'] >= 80 ? 'success' : ($stats['persentase_kehadiran'] >= 70 ? 'warning' : 'danger') }}" 
                 style="width: {{ $stats['persentase_kehadiran'] }}%">
            </div>
        </div>
        <div class="mt-2 d-flex justify-content-between small text-muted">
            <span>
                <i class="bi bi-check-circle text-success"></i> Memenuhi syarat: {{ $stats['memenuhi_syarat'] }} MK
            </span>
            <span>
                <i class="bi bi-x-circle text-danger"></i> Tidak memenuhi: {{ $stats['tidak_memenuhi'] }} MK
            </span>
        </div>
    </div>
</div>
@endif

<!-- Rekap Per Mata Kuliah -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-check me-2"></i>Rekap Kehadiran Per Mata Kuliah
    </div>
    <div class="card-body p-0">
        @if($rekapList->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Mata Kuliah</th>
                        <th class="text-center" width="8%">Total</th>
                        <th class="text-center" width="8%">Hadir</th>
                        <th class="text-center" width="8%">Izin</th>
                        <th class="text-center" width="8%">Sakit</th>
                        <th class="text-center" width="8%">Alpa</th>
                        <th class="text-center" width="12%">Persentase</th>
                        <th class="text-center" width="10%">Status</th>
                        <th width="8%">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapList as $index => $rekap)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $rekap->mataKuliah->nama ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $rekap->mataKuliah->kode ?? '' }}</small>
                        </td>
                        <td class="text-center">{{ $rekap->total_pertemuan }}</td>
                        <td class="text-center text-success">{{ $rekap->jumlah_hadir }}</td>
                        <td class="text-center text-info">{{ $rekap->jumlah_izin }}</td>
                        <td class="text-center text-warning">{{ $rekap->jumlah_sakit }}</td>
                        <td class="text-center text-danger">{{ $rekap->jumlah_alpa }}</td>
                        <td class="text-center">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $rekap->persentase_badge }}" 
                                     style="width: {{ $rekap->persentase_kehadiran }}%">
                                    {{ number_format($rekap->persentase_kehadiran, 1) }}%
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($rekap->memenuhi_syarat_ujian)
                            <span class="badge bg-success">
                                <i class="bi bi-check"></i> Layak Ujian
                            </span>
                            @else
                            <span class="badge bg-danger">
                                <i class="bi bi-x"></i> Tidak Layak
                            </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('mahasiswa.kehadiran.detail', ['mataKuliah' => $rekap->mataKuliah->hashid, 'tahun_akademik' => $tahunAkademik?->id]) }}" 
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">
                @if($tahunAkademik)
                Belum ada data kehadiran untuk semester ini
                @else
                Silakan pilih semester terlebih dahulu
                @endif
            </p>
        </div>
        @endif
    </div>
</div>

<!-- Info -->
<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Catatan:</strong> Mahasiswa wajib memiliki kehadiran minimal <strong>75%</strong> untuk dapat mengikuti ujian akhir semester.
</div>
@endsection
