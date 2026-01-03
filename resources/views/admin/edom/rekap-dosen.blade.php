@extends('layouts.app')

@section('title', 'Rekap EDOM Dosen')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Rekap Evaluasi: {{ $dosen->nama }}</h1>
            <p class="text-muted mb-0">{{ $edom->nama }} - {{ $edom->tahunAkademik->nama ?? '' }}</p>
        </div>
        <a href="{{ route('admin.edom.show', $edom->hashid) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        @php
            $avgTotal = $rekap->avg('rata_rata_total');
            $totalResponden = $rekap->sum('jumlah_responden');
        @endphp
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Rata-rata Total</h6>
                    <h2 class="mb-0">{{ number_format($avgTotal, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Total Responden</h6>
                    <h2 class="mb-0">{{ $totalResponden }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Jumlah Kelas</h6>
                    <h2 class="mb-0">{{ $rekap->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Per Mata Kuliah -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Nilai per Mata Kuliah</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Kuliah</th>
                            <th class="text-center">Pedagogik</th>
                            <th class="text-center">Profesional</th>
                            <th class="text-center">Kepribadian</th>
                            <th class="text-center">Sosial</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Responden</th>
                            <th>Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekap as $i => $r)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $r->jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                            <td class="text-center">{{ number_format($r->rata_rata_pedagogik, 2) }}</td>
                            <td class="text-center">{{ number_format($r->rata_rata_profesional, 2) }}</td>
                            <td class="text-center">{{ number_format($r->rata_rata_kepribadian, 2) }}</td>
                            <td class="text-center">{{ number_format($r->rata_rata_sosial, 2) }}</td>
                            <td class="text-center"><strong>{{ number_format($r->rata_rata_total, 2) }}</strong></td>
                            <td class="text-center">{{ $r->jumlah_responden }}</td>
                            <td><span class="badge bg-{{ $r->kategori_badge }}">{{ $r->kategori }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Komentar dan Saran -->
    @if($komentar->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-chat-quote me-2"></i>Komentar dan Saran Mahasiswa</h5>
        </div>
        <div class="card-body">
            @foreach($komentar as $k)
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">
                        {{ $k->jadwalKuliah->mataKuliah->nama ?? 'Mata Kuliah' }} - 
                        {{ $k->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
                @if($k->komentar)
                <p class="mb-2"><strong>Komentar:</strong> {{ $k->komentar }}</p>
                @endif
                @if($k->saran)
                <p class="mb-0"><strong>Saran:</strong> {{ $k->saran }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
