@extends('layouts.app')

@section('title', 'Tracking Cicilan Saya')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Cicilan Saya</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Cicilan Saya</li>
                </ol>
            </nav>
        </div>
    </div>

    @if($totalSisaCicilan > 0)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Total Sisa Cicilan:</strong> Rp {{ number_format($totalSisaCicilan, 0, ',', '.') }}
    </div>
    @endif

    @forelse($cicilan as $cic)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">{{ $cic->tagihan->jenis_tagihan }}</h5>
                <small class="text-muted">{{ $cic->skemaCicilan->nama ?? '-' }} | Mulai: {{ $cic->tanggal_mulai->format('d M Y') }}</small>
            </div>
            <div>
                {!! $cic->status_badge !!}
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <small class="text-muted">Total Cicilan</small>
                    <div class="fw-bold">Rp {{ number_format($cic->total_harus_dibayar, 0, ',', '.') }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Per Cicilan</small>
                    <div class="fw-bold">Rp {{ number_format($cic->nominal_per_cicilan, 0, ',', '.') }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Progress</small>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" style="width: {{ $cic->progress_persen }}%">
                            {{ $cic->cicilan_terbayar }}/{{ $cic->jumlah_cicilan }}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Sisa Bayar</small>
                    <div class="fw-bold text-danger">Rp {{ number_format($cic->getSisaTagihan(), 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Cicilan</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Nominal</th>
                            <th>Tanggal Bayar</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cic->detailCicilan as $detail)
                        <tr class="{{ $detail->isTerlambat() ? 'table-danger' : '' }}">
                            <td class="text-center">{{ $detail->cicilan_ke }}</td>
                            <td>
                                {{ $detail->jatuh_tempo->format('d M Y') }}
                                @if($detail->isTerlambat())
                                <span class="badge bg-danger">{{ $detail->getHariTerlambat() }} hari</span>
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
                            <td>{{ $detail->tanggal_bayar ? $detail->tanggal_bayar->format('d M Y') : '-' }}</td>
                            <td class="text-center">{!! $detail->status_badge !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <p class="text-muted mt-3">Anda tidak memiliki cicilan aktif</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
