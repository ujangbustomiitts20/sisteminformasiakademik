@extends('layouts.app')

@section('title', 'Jadwal Mengajar')

@push('styles')
<style>
    .schedule-cell {
        min-height: 60px;
        vertical-align: top;
        transition: background-color 0.2s ease;
    }
    .schedule-item {
        padding: 8px;
        border-radius: 8px;
        font-size: 0.8rem;
        border-left: 3px solid;
    }
    .schedule-item.primary { background-color: rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
    .schedule-item.success { background-color: rgba(25, 135, 84, 0.1); border-color: #198754; }
    .schedule-item.info { background-color: rgba(13, 202, 240, 0.1); border-color: #0dcaf0; }
    .schedule-item.warning { background-color: rgba(255, 193, 7, 0.15); border-color: #ffc107; }
    .schedule-item.danger { background-color: rgba(220, 53, 69, 0.1); border-color: #dc3545; }
    .schedule-item.secondary { background-color: rgba(108, 117, 125, 0.1); border-color: #6c757d; }
    .stat-mini {
        padding: 1rem;
        border-radius: 10px;
        text-align: center;
    }
    .stat-mini .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
    }
    .time-slot {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
    }
    .today-column {
        background-color: rgba(13, 110, 253, 0.05) !important;
    }
    .today-header {
        background-color: rgba(13, 110, 253, 0.15) !important;
        font-weight: 600;
    }
    .card {
        border-radius: 12px;
        border: none;
    }
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    @media print {
        .no-print { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .today-column, .today-header { background-color: transparent !important; }
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="d-flex justify-content-between align-items-start mb-4 no-print">
    <div>
        <h4 class="fw-bold text-dark mb-1">Jadwal Mengajar</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">Jadwal Mengajar</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <!-- Filter Tahun Akademik -->
        <form method="GET" action="{{ route('jadwal.dosen') }}" class="d-flex align-items-center">
            <select name="tahun_akademik_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                @foreach($tahunAkademikList as $ta)
                <option value="{{ $ta->id }}" {{ $tahunAkademik?->id == $ta->id ? 'selected' : '' }}>
                    {{ $ta->tahun }} {{ $ta->semester }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                </option>
                @endforeach
            </select>
        </form>
        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak
        </button>
    </div>
</div>

<!-- Statistik Ringkas -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body stat-mini">
                <div class="stat-number text-primary">{{ $jadwal->count() }}</div>
                <div class="text-muted small">Mata Kuliah</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body stat-mini">
                <div class="stat-number text-success">{{ $jadwal->sum(fn($j) => $j->mataKuliah->sks) }}</div>
                <div class="text-muted small">Total SKS</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body stat-mini">
                <div class="stat-number text-info">{{ $jadwal->sum(fn($j) => $j->krs->where('status', 'Disetujui')->count()) }}</div>
                <div class="text-muted small">Total Mahasiswa</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body stat-mini">
                <span class="badge bg-primary fs-6 px-3 py-2">{{ $tahunAkademik->nama ?? '-' }}</span>
                <div class="text-muted small mt-2">Tahun Akademik</div>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal Mingguan -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <i class="bi bi-calendar-week text-primary me-2"></i>
                <strong>Jadwal Mingguan</strong>
                <small class="text-muted ms-2">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</small>
            </div>
            <div class="d-flex gap-1 flex-wrap no-print">
                @php
                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                @endphp
                @foreach($jadwal as $index => $j)
                <span class="badge bg-{{ $colors[$index % count($colors)] }} bg-opacity-10 text-{{ $colors[$index % count($colors)] }}" style="font-size: 0.7rem;">
                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ Str::limit($j->mataKuliah->nama, 12) }}
                </span>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($jadwal->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    @php
                        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        $hariIndonesia = [
                            'Monday' => 'Senin',
                            'Tuesday' => 'Selasa', 
                            'Wednesday' => 'Rabu',
                            'Thursday' => 'Kamis',
                            'Friday' => 'Jumat',
                            'Saturday' => 'Sabtu',
                            'Sunday' => 'Minggu'
                        ];
                        $hariIni = $hariIndonesia[now()->format('l')] ?? '';
                    @endphp
                    <tr class="table-light">
                        <th width="70" class="text-center">Jam</th>
                        @foreach($hariList as $h)
                        <th class="text-center {{ $h == $hariIni ? 'today-header text-primary' : '' }}">
                            {{ $h }}
                            @if($h == $hariIni)
                            <br><small class="badge bg-primary">Hari ini</small>
                            @endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $jamSlots = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
                        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                        $colorMap = [];
                        foreach($jadwal as $idx => $jd) {
                            $colorMap[$jd->id] = $colors[$idx % count($colors)];
                        }
                    @endphp
                    @foreach($jamSlots as $jam)
                    <tr>
                        <td class="text-center align-middle bg-light">
                            <span class="time-slot">{{ $jam }}</span>
                        </td>
                        @foreach($hariList as $h)
                        @php
                            $mk = $jadwal->first(function($j) use ($h, $jam) {
                                $mulai = \Carbon\Carbon::parse($j->jam_mulai)->format('H:i');
                                $selesai = \Carbon\Carbon::parse($j->jam_selesai)->format('H:i');
                                return $j->hari == $h && $mulai <= $jam && $selesai > $jam;
                            });
                            $isStart = $mk && \Carbon\Carbon::parse($mk->jam_mulai)->format('H:i') == $jam;
                        @endphp
                        <td class="schedule-cell p-1 {{ $h == $hariIni ? 'today-column' : '' }}">
                            @if($isStart)
                            <div class="schedule-item {{ $colorMap[$mk->id] ?? 'primary' }}">
                                <div class="fw-semibold text-dark mb-1">{{ Str::limit($mk->mataKuliah->nama, 20) }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    <i class="bi bi-geo-alt"></i> {{ $mk->ruangan->nama ?? '-' }}<br>
                                    <i class="bi bi-people"></i> Kelas {{ $mk->kelas }}
                                </div>
                            </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <div class="text-muted mb-2">
                <i class="bi bi-calendar-x" style="font-size: 3.5rem; opacity: 0.5;"></i>
            </div>
            <p class="text-muted mb-0">Belum ada jadwal mengajar</p>
        </div>
        @endif
    </div>
</div>

<!-- Daftar Detail Jadwal -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <i class="bi bi-list-ul text-primary me-2"></i>
        <strong>Detail Jadwal Mengajar</strong>
    </div>
    <div class="card-body p-0">
        @if($jadwal->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Mata Kuliah</th>
                        <th class="text-center">Kelas</th>
                        <th class="text-center">SKS</th>
                        <th>Jadwal</th>
                        <th>Ruangan</th>
                        <th class="text-center">Mahasiswa</th>
                        <th width="120" class="text-center no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal as $index => $jdwl)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $jdwl->mataKuliah->nama }}</div>
                            <small class="text-muted"><code>{{ $jdwl->mataKuliah->kode }}</code></small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $jdwl->kelas }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary">{{ $jdwl->mataKuliah->sks }} SKS</span>
                        </td>
                        <td>
                            <div><i class="bi bi-calendar3 text-muted me-1"></i>{{ $jdwl->hari }}</div>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($jdwl->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jdwl->jam_selesai)->format('H:i') }}
                            </small>
                        </td>
                        <td>
                            <i class="bi bi-geo-alt text-muted me-1"></i>{{ $jdwl->ruangan->nama ?? '-' }}
                        </td>
                        <td class="text-center">
                            @php
                                $enrolled = $jdwl->krs->where('status', 'Disetujui')->count();
                                $percentage = $jdwl->kuota > 0 ? ($enrolled / $jdwl->kuota) * 100 : 0;
                                $badgeClass = $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success');
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }}">
                                {{ $enrolled }} / {{ $jdwl->kuota }}
                            </span>
                        </td>
                        <td class="text-center no-print">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('absensi.show', $jdwl) }}" class="btn btn-outline-success" title="Absensi">
                                    <i class="bi bi-clipboard-check"></i>
                                </a>
                                <a href="{{ route('nilai.input', $jdwl) }}" class="btn btn-outline-primary" title="Input Nilai">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="{{ route('pertemuan.index', $jdwl) }}" class="btn btn-outline-info" title="Pertemuan">
                                    <i class="bi bi-collection"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light fw-semibold">
                        <td colspan="3" class="text-end">Total:</td>
                        <td class="text-center">{{ $jadwal->sum(fn($j) => $j->mataKuliah->sks) }} SKS</td>
                        <td colspan="2"></td>
                        <td class="text-center">{{ $jadwal->sum(fn($j) => $j->krs->where('status', 'Disetujui')->count()) }} Mhs</td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <div class="text-muted mb-2">
                <i class="bi bi-journal-x" style="font-size: 3.5rem; opacity: 0.5;"></i>
            </div>
            <p class="text-muted mb-0">Belum ada jadwal mengajar</p>
        </div>
        @endif
    </div>
</div>
@endsection
