@extends('layouts.app')

@section('title', 'Hasil EDOM')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Hasil Evaluasi Dosen (EDOM)</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Hasil EDOM</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Filter Periode -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="col-form-label">Periode EDOM:</label>
            </div>
            <div class="col-auto">
                <select name="periode_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($periodeList as $periode)
                        <option value="{{ $periode->id }}" {{ $periodeAktif?->id == $periode->id ? 'selected' : '' }}>
                            {{ $periode->nama }} {{ $periode->status == 'aktif' ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($periodeAktif)
<!-- Statistik -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total_mk'] }}</div>
                        <div class="small">Mata Kuliah Dievaluasi</div>
                    </div>
                    <i class="bi bi-book fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-{{ $stats['kategori']['badge'] }} text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($stats['rata_rata_total'], 2) }}</div>
                        <div class="small">Rata-rata Nilai</div>
                    </div>
                    <i class="bi bi-star fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total_responden'] }}</div>
                        <div class="small">Total Responden</div>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-{{ $stats['kategori']['badge'] }} text-white shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['kategori']['label'] }}</div>
                        <div class="small">Kategori</div>
                    </div>
                    <i class="bi bi-award fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Per Aspek -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Nilai Per Aspek Kompetensi</h6>
    </div>
    <div class="card-body">
        <div class="row">
            @php
                $aspekList = [
                    'pedagogik' => ['label' => 'Pedagogik', 'color' => 'primary', 'icon' => 'book'],
                    'profesional' => ['label' => 'Profesional', 'color' => 'success', 'icon' => 'briefcase'],
                    'kepribadian' => ['label' => 'Kepribadian', 'color' => 'warning', 'icon' => 'person-heart'],
                    'sosial' => ['label' => 'Sosial', 'color' => 'info', 'icon' => 'people'],
                ];
            @endphp
            @foreach($aspekList as $key => $info)
            <div class="col-md-3 mb-3">
                <div class="text-center">
                    <div class="display-6 fw-bold text-{{ $info['color'] }}">
                        {{ number_format($aspek[$key], 2) }}
                    </div>
                    <div class="progress my-2" style="height: 8px;">
                        <div class="progress-bar bg-{{ $info['color'] }}" style="width: {{ ($aspek[$key] / 5) * 100 }}%"></div>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-{{ $info['icon'] }} me-1"></i>{{ $info['label'] }}
                    </small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Detail Per Mata Kuliah -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Detail Per Mata Kuliah</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th class="text-center">Responden</th>
                        <th class="text-center">Pedagogik</th>
                        <th class="text-center">Profesional</th>
                        <th class="text-center">Kepribadian</th>
                        <th class="text-center">Sosial</th>
                        <th class="text-center">Rata-rata</th>
                        <th class="text-center">Kategori</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapEdom as $rekap)
                    <tr>
                        <td>
                            <strong>{{ $rekap->jadwalKuliah->mataKuliah->nama ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $rekap->jadwalKuliah->mataKuliah->kode ?? '' }}</small>
                        </td>
                        <td>{{ $rekap->jadwalKuliah->kelas->nama ?? '-' }}</td>
                        <td class="text-center">{{ $rekap->jumlah_responden }}</td>
                        <td class="text-center">{{ number_format($rekap->rata_rata_pedagogik, 2) }}</td>
                        <td class="text-center">{{ number_format($rekap->rata_rata_profesional, 2) }}</td>
                        <td class="text-center">{{ number_format($rekap->rata_rata_kepribadian, 2) }}</td>
                        <td class="text-center">{{ number_format($rekap->rata_rata_sosial, 2) }}</td>
                        <td class="text-center fw-bold">{{ number_format($rekap->rata_rata_total, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $rekap->kategori_badge }}">{{ $rekap->kategori }}</span>
                        </td>
                        <td>
                            <a href="{{ route('dosen.edom.show', $rekap) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data evaluasi untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Belum ada periode EDOM yang tersedia.
</div>
@endif
@endsection
