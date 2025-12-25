@extends('layouts.app')

@section('title', 'Detail Cicilan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Cicilan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cicilan.index') }}">Cicilan</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if($cicilan->status == 'Aktif' && $cicilan->cicilan_terbayar == 0)
            <form action="{{ route('cicilan.batalkan', $cicilan) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan cicilan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-lg me-1"></i> Batalkan Cicilan
                </button>
            </form>
            @endif
            <a href="{{ route('cicilan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Info Cicilan -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Info Cicilan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <div>{!! $cicilan->status_badge !!}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Mahasiswa</label>
                        <div>
                            <strong>{{ $cicilan->tagihan->mahasiswa->nama ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $cicilan->tagihan->mahasiswa->nim ?? '-' }}</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Program Studi</label>
                        <div>{{ $cicilan->tagihan->mahasiswa->programStudi->nama ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Tagihan</label>
                        <div>{{ $cicilan->tagihan->jenis_tagihan ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Skema</label>
                        <div>{{ $cicilan->skemaCicilan->nama ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Tanggal Mulai</label>
                        <div>{{ $cicilan->tanggal_mulai->format('d M Y') }}</div>
                    </div>
                    @if($cicilan->tanggal_selesai)
                    <div class="mb-3">
                        <label class="text-muted small">Tanggal Selesai</label>
                        <div>{{ $cicilan->tanggal_selesai->format('d M Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rincian Pembayaran -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Rincian Pembayaran</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Total Tagihan Awal</td>
                            <td class="text-end">Rp {{ number_format($cicilan->total_tagihan_awal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Biaya Admin</td>
                            <td class="text-end">Rp {{ number_format($cicilan->biaya_admin, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Bunga</td>
                            <td class="text-end">Rp {{ number_format($cicilan->total_bunga, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-primary">
                            <td><strong>Total Harus Dibayar</strong></td>
                            <td class="text-end"><strong>Rp {{ number_format($cicilan->total_harus_dibayar, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr class="table-info">
                            <td><strong>Nominal Per Cicilan</strong></td>
                            <td class="text-end"><strong>Rp {{ number_format($cicilan->nominal_per_cicilan, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Progress -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Progress</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="display-4 fw-bold text-{{ $cicilan->status == 'Lunas' ? 'success' : 'primary' }}">
                            {{ $cicilan->cicilan_terbayar }}/{{ $cicilan->jumlah_cicilan }}
                        </div>
                        <p class="text-muted">Cicilan Terbayar</p>
                    </div>
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-success progress-bar-striped" style="width: {{ $cicilan->progress_persen }}%">
                            {{ $cicilan->progress_persen }}%
                        </div>
                    </div>
                    @php
                        $sisaBayar = $cicilan->detailCicilan->whereIn('status', ['Belum Bayar', 'Terlambat'])->sum('nominal');
                        $sudahBayar = $cicilan->detailCicilan->whereIn('status', ['Dibayar', 'Dibayar Terlambat'])->sum('nominal');
                    @endphp
                    <div class="row text-start">
                        <div class="col-6">
                            <small class="text-muted">Sudah Dibayar</small>
                            <div class="fw-bold text-success">Rp {{ number_format($sudahBayar, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Sisa</small>
                            <div class="fw-bold text-danger">Rp {{ number_format($sisaBayar, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Cicilan -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Jadwal Cicilan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Cicilan Ke</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-end">Denda</th>
                            <th class="text-end">Total</th>
                            <th>Tanggal Bayar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cicilan->detailCicilan as $detail)
                        <tr class="{{ $detail->isTerlambat() ? 'table-danger' : '' }}">
                            <td class="text-center">
                                <span class="badge bg-secondary rounded-pill">{{ $detail->cicilan_ke }}</span>
                            </td>
                            <td>
                                {{ $detail->jatuh_tempo->format('d M Y') }}
                                @if($detail->isTerlambat())
                                <br><small class="text-danger">{{ $detail->getHariTerlambat() }} hari terlambat</small>
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">
                                @if($detail->denda > 0)
                                Rp {{ number_format($detail->denda, 0, ',', '.') }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-end fw-bold">Rp {{ number_format($detail->nominal + $detail->denda, 0, ',', '.') }}</td>
                            <td>
                                @if($detail->tanggal_bayar)
                                {{ $detail->tanggal_bayar->format('d M Y') }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-center">{!! $detail->status_badge !!}</td>
                            <td class="text-center">
                                @if(in_array($detail->status, ['Belum Bayar', 'Terlambat']) || ($detail->status == 'Belum Bayar' && $detail->isTerlambat()))
                                <a href="{{ route('cicilan.bayar', $detail) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-cash me-1"></i> Bayar
                                </a>
                                @elseif($detail->transaksi_pembayaran_id)
                                <a href="{{ route('transaksi-pembayaran.show', $detail->transaksi_pembayaran_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-receipt"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
