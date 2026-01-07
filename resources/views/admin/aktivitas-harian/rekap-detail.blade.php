@extends('layouts.app')

@section('title', 'Detail Rekap Aktivitas')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Detail Rekap Aktivitas</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kepegawaian.aktivitas-harian.rekap') }}">Rekap</a></li>
                <li class="breadcrumb-item active">{{ $dosen->nama }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('kepegawaian.aktivitas-harian.rekap', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<!-- Info Pegawai -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 60px; height: 60px;">
                    <i class="bi bi-person-fill text-white fs-3"></i>
                </div>
            </div>
            <div class="col">
                <h5 class="mb-1">{{ $dosen->nama }}</h5>
                <p class="text-muted mb-0">
                    {{ $dosen->nidn ?? $dosen->nip ?? '-' }}
                    @if($dosen->programStudi)
                        • {{ $dosen->programStudi->nama }}
                    @endif
                </p>
            </div>
            <div class="col-auto text-end">
                <div class="fs-5 fw-bold text-primary">
                    {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-bold">{{ $stats->total_aktivitas ?? 0 }}</div>
                <div class="small">Total</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-bold">{{ $stats->disetujui ?? 0 }}</div>
                <div class="small">Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-warning text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-bold">{{ $stats->diajukan ?? 0 }}</div>
                <div class="small">Menunggu</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-secondary text-white shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-bold">{{ $stats->draft ?? 0 }}</div>
                <div class="small">Draft</div>
            </div>
        </div>
    </div>
</div>

<!-- Aktivitas per Tanggal -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Aktivitas Harian</h6>
    </div>
    <div class="card-body p-0">
        @forelse($aktivitasPerTanggal as $tanggal => $items)
        <div class="border-bottom">
            <div class="bg-light px-3 py-2 d-flex justify-content-between align-items-center">
                <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</strong>
                <span class="badge bg-primary">{{ $items->count() }} aktivitas</span>
            </div>
            <div class="px-3 py-2">
                @foreach($items as $item)
                <div class="d-flex align-items-start mb-2 {{ !$loop->last ? 'pb-2 border-bottom' : '' }}">
                    <div class="me-3 text-muted" style="min-width: 70px;">
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
                        @if($item->volume)
                            <br><small class="text-info">Volume: {{ number_format($item->volume, 0) }} {{ $item->satuan }}</small>
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
@endsection
