@extends('layouts.app')

@section('title', 'Rekap Aktivitas Bulanan')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Rekap Aktivitas Bulanan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dosen.aktivitas-harian.index') }}">Aktivitas Harian</a></li>
                <li class="breadcrumb-item active">Rekap</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('dosen.aktivitas-harian.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<!-- Filter Bulan -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="bulan" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold">{{ $stats->total_aktivitas ?? 0 }}</div>
                <div class="small">Total Aktivitas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold">{{ $stats->disetujui ?? 0 }}</div>
                <div class="small">Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-warning text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold">{{ $stats->diajukan ?? 0 }}</div>
                <div class="small">Menunggu</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-secondary text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold">{{ $stats->draft ?? 0 }}</div>
                <div class="small">Draft</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Aktivitas per Tanggal -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">
                    <i class="bi bi-calendar3 me-2"></i>
                    Aktivitas Bulan {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                </h6>
            </div>
            <div class="card-body p-0">
                @forelse($aktivitasPerTanggal as $tanggal => $items)
                <div class="border-bottom">
                    <div class="bg-light px-3 py-2">
                        <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</strong>
                        <span class="badge bg-primary ms-2">{{ $items->count() }} aktivitas</span>
                    </div>
                    <div class="px-3 py-2">
                        @foreach($items as $item)
                        <div class="d-flex align-items-start mb-2 {{ !$loop->last ? 'pb-2 border-bottom' : '' }}">
                            <div class="me-3 text-muted" style="min-width: 80px;">
                                @if($item->jam_mulai)
                                    {{ $item->jam_mulai_format }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div>{{ $item->uraian_kegiatan }}</div>
                                @if($item->output_hasil)
                                    <small class="text-muted"><i class="bi bi-arrow-right"></i> {{ $item->output_hasil }}</small>
                                @endif
                            </div>
                            <div class="ms-2">
                                <span class="badge bg-{{ $item->status_badge }}">{{ $item->status }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Tidak ada aktivitas pada bulan ini
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Aktivitas per Kategori -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Per Kategori SKP</h6>
            </div>
            <div class="card-body">
                @forelse($aktivitasPerKategori as $kategori => $items)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ \App\Models\UraianKegiatanSkp::KATEGORI[$kategori] ?? ucfirst($kategori) }}</span>
                    <span class="badge bg-primary">{{ $items->count() }}</span>
                </div>
                @empty
                <p class="text-muted text-center mb-0">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
