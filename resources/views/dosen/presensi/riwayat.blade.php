@extends('layouts.app')

@section('title', 'Riwayat Presensi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Riwayat Presensi</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dosen.presensi.index') }}">Presensi</a></li>
                    <li class="breadcrumb-item active">Riwayat</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('dosen.presensi.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('dosen.presensi.riwayat') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                                @foreach($listBulan as $key => $nama)
                                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select">
                                @foreach($listTahun as $t)
                                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach($statusList as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistik -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistik {{ $listBulan[$bulan] }} {{ $tahun }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Hari Kerja</span>
                        <span class="badge bg-secondary">{{ $statistik['total_hari_kerja'] }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-check-circle text-success me-1"></i> Hadir</span>
                        <span class="badge bg-success">{{ $statistik['hadir'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-clock text-warning me-1"></i> Terlambat</span>
                        <span class="badge bg-warning text-dark">{{ $statistik['terlambat'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-thermometer-half text-info me-1"></i> Sakit</span>
                        <span class="badge bg-info">{{ $statistik['sakit'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-envelope text-primary me-1"></i> Izin</span>
                        <span class="badge bg-primary">{{ $statistik['izin'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-calendar-x text-secondary me-1"></i> Cuti</span>
                        <span class="badge bg-secondary">{{ $statistik['cuti'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><i class="bi bi-briefcase text-dark me-1"></i> Dinas Luar</span>
                        <span class="badge bg-dark">{{ $statistik['dinas_luar'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-x-circle text-danger me-1"></i> Alpha</span>
                        <span class="badge bg-danger">{{ $statistik['alpha'] }}</span>
                    </div>
                    <hr>
                    @php
                        $totalHadir = $statistik['hadir'] + $statistik['terlambat'];
                        $persentase = $statistik['total_hari_kerja'] > 0 
                            ? round(($totalHadir / $statistik['total_hari_kerja']) * 100, 1) 
                            : 0;
                    @endphp
                    <div class="text-center">
                        <h4 class="mb-1 {{ $persentase >= 80 ? 'text-success' : ($persentase >= 60 ? 'text-warning' : 'text-danger') }}">
                            {{ $persentase }}%
                        </h4>
                        <small class="text-muted">Tingkat Kehadiran</small>
                    </div>
                    <div class="progress mt-2" style="height: 8px;">
                        <div class="progress-bar {{ $persentase >= 80 ? 'bg-success' : ($persentase >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                             style="width: {{ $persentase }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat -->
        <div class="col-lg-9 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-calendar3 me-2"></i>
                        Riwayat Presensi - {{ $listBulan[$bulan] }} {{ $tahun }}
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th class="text-center">Jam Masuk</th>
                                    <th class="text-center">Jam Keluar</th>
                                    <th class="text-center">Durasi</th>
                                    <th class="text-center">Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presensi as $p)
                                <tr>
                                    <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                                    <td>{{ $p->tanggal->locale('id')->isoFormat('dddd') }}</td>
                                    <td class="text-center">
                                        @if($p->jam_masuk)
                                            <span class="{{ $p->status == 'terlambat' ? 'text-warning fw-bold' : 'text-success' }}">
                                                {{ substr($p->jam_masuk, 0, 5) }}
                                            </span>
                                            @if($p->lokasi_masuk)
                                                <i class="bi bi-geo-alt-fill text-muted ms-1" 
                                                   data-bs-toggle="tooltip" 
                                                   title="{{ $p->lokasi_masuk }}"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($p->jam_keluar)
                                            <span class="text-danger">{{ substr($p->jam_keluar, 0, 5) }}</span>
                                            @if($p->lokasi_keluar)
                                                <i class="bi bi-geo-alt-fill text-muted ms-1" 
                                                   data-bs-toggle="tooltip" 
                                                   title="{{ $p->lokasi_keluar }}"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($p->jam_masuk && $p->jam_keluar)
                                            <span class="badge bg-light text-dark">{{ $p->durasi_kerja }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{!! $p->status_badge !!}</td>
                                    <td>
                                        @if($p->keterangan)
                                            <small class="text-muted">{{ Str::limit($p->keterangan, 30) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2 mb-0">Tidak ada data presensi untuk periode ini</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($presensi->hasPages())
                <div class="card-footer bg-white">
                    {{ $presensi->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
