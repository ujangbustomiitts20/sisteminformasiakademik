@extends('layouts.app')

@section('title', 'Dashboard Keuangan')

@section('content')
<div class="page-title">
    <h4>Dashboard Keuangan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Keuangan</li>
        </ol>
    </nav>
</div>

@if($mahasiswa)
<!-- Stats Keuangan -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-danger position-relative">
            <i class="bi bi-receipt stat-icon"></i>
            <div class="stat-value h4 mb-1">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label opacity-75">Total Tagihan Belum Lunas</div>
            @if(($tagihanJatuhTempo ?? 0) > 0)
            <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">{{ $tagihanJatuhTempo }} jatuh tempo</span>
            @endif
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-success position-relative">
            <i class="bi bi-cash-stack stat-icon"></i>
            <div class="stat-value h4 mb-1">Rp {{ number_format($totalDibayarTahunIni ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label opacity-75">Dibayar Tahun Ini</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-primary position-relative">
            <i class="bi bi-tags stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ ($potonganAktif ?? collect())->count() }}</div>
            <div class="stat-label opacity-75">Potongan Aktif</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-warning position-relative">
            <i class="bi bi-list-check stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ ($cicilanAktif ?? collect())->count() }}</div>
            <div class="stat-label opacity-75">Cicilan Berjalan</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tagihan Belum Lunas -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>Tagihan Belum Lunas</span>
                <a href="{{ route('tagihan.mahasiswa') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if(isset($tagihanBelumLunas) && $tagihanBelumLunas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Tagihan</th>
                                <th>Periode</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Sisa Tagihan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tagihanBelumLunas as $tagihan)
                            <tr>
                                <td>
                                    <strong>{{ $tagihan->jenis_tagihan }}</strong>
                                    <br><small class="text-muted">{{ $tagihan->no_tagihan }}</small>
                                </td>
                                <td>{{ $tagihan->tahunAkademik->nama_lengkap ?? '-' }}</td>
                                <td>
                                    @if($tagihan->tanggal_jatuh_tempo)
                                        @php $jatuhTempo = \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo); @endphp
                                        @if($jatuhTempo->isPast())
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            {{ $jatuhTempo->format('d/m/Y') }}
                                        </span>
                                        @elseif($jatuhTempo->diffInDays(now()) <= 7)
                                        <span class="badge bg-warning text-dark">
                                            {{ $jatuhTempo->format('d/m/Y') }}
                                        </span>
                                        @else
                                        <span class="text-muted">{{ $jatuhTempo->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pembayaran.bayar', $tagihan) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-credit-card me-1"></i>Bayar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-0 mt-2">Tidak ada tagihan yang belum lunas</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Promo & Potongan -->
    <div class="col-lg-4">
        <!-- Promo Tersedia -->
        @if(isset($promoTersedia) && $promoTersedia->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="bi bi-gift me-2"></i>Promo Tersedia
            </div>
            <div class="card-body p-0">
                @foreach($promoTersedia as $promo)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $promo->nama }}</h6>
                            <span class="badge bg-success">
                                @if($promo->tipe_nilai == 'persen')
                                Diskon {{ $promo->nilai }}%
                                @else
                                Potongan Rp {{ number_format($promo->nilai, 0, ',', '.') }}
                                @endif
                            </span>
                            @if($promo->keterangan)
                            <p class="small text-muted mb-0 mt-1">{{ Str::limit($promo->keterangan, 50) }}</p>
                            @endif
                        </div>
                        <small class="text-muted">s.d {{ $promo->tanggal_selesai->format('d/m') }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Potongan Saya -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-tags me-2"></i>Potongan Saya
            </div>
            <div class="card-body p-0">
                @if(isset($potonganAktif) && $potonganAktif->count() > 0)
                @foreach($potonganAktif as $potongan)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{ $potongan->jenisPotongan->nama ?? 'Potongan' }}</h6>
                            <small class="text-muted">{{ $potongan->alasan ?? '-' }}</small>
                        </div>
                        <span class="badge bg-primary">
                            @if($potongan->tipe_nilai == 'persen')
                            {{ $potongan->nilai }}%
                            @else
                            Rp {{ number_format($potongan->nilai, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-4">
                    <i class="bi bi-tags text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Tidak ada potongan aktif</p>
                </div>
                @endif
            </div>
            <div class="card-footer bg-light">
                <a href="{{ route('mahasiswa.potongan') }}" class="text-decoration-none">
                    <small>Lihat semua potongan <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Cicilan Aktif -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-check me-2"></i>Cicilan Aktif</span>
                <a href="{{ route('cicilan.tracking') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if(isset($cicilanAktif) && $cicilanAktif->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis</th>
                                <th>Total Cicilan</th>
                                <th>Terbayar</th>
                                <th>Sisa</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cicilanAktif as $cicilan)
                            @php
                                $terbayar = $cicilan->detailCicilans()->where('status', 'lunas')->sum('jumlah');
                                $total = $cicilan->total_cicilan ?? 0;
                                $sisa = $total - $terbayar;
                                $progress = $total > 0 ? round(($terbayar / $total) * 100) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $cicilan->tagihan->jenis_tagihan ?? 'Cicilan' }}</strong>
                                    <br><small class="text-muted">{{ $cicilan->jumlah_cicilan }} x cicilan</small>
                                </td>
                                <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                                <td class="text-success">Rp {{ number_format($terbayar, 0, ',', '.') }}</td>
                                <td class="text-danger">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                <td style="width: 150px;">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: {{ $progress }}%">{{ $progress }}%</div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-0 mt-2">Tidak ada cicilan aktif</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Transaksi Terbaru -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Transaksi Terbaru
            </div>
            <div class="card-body p-0">
                @if(isset($transaksiTerbaru) && $transaksiTerbaru->count() > 0)
                @foreach($transaksiTerbaru as $trx)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $trx->tagihan->jenis_tagihan ?? 'Pembayaran' }}</h6>
                            <small class="text-muted">{{ $trx->tanggal_bayar->format('d M Y H:i') }}</small>
                            <br>
                            <small class="text-muted">{{ $trx->metode_pembayaran ?? '-' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="text-success fw-bold">+Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</span>
                            <br>
                            <span class="badge bg-{{ $trx->status == 'sukses' ? 'success' : ($trx->status == 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($trx->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Belum ada transaksi</p>
                </div>
                @endif
            </div>
            @if(isset($transaksiTerbaru) && $transaksiTerbaru->count() > 0)
            <div class="card-footer bg-light">
                <a href="{{ route('transaksi.mahasiswa') }}" class="text-decoration-none">
                    <small>Lihat semua transaksi <i class="bi bi-arrow-right"></i></small>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Menu Keuangan -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-wallet2 me-2"></i>Menu Keuangan
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('tagihan.mahasiswa') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <i class="bi bi-receipt fs-1 mb-2"></i>
                    <span>Tagihan</span>
                    @if(isset($tagihanBelumLunas) && $tagihanBelumLunas->count() > 0)
                    <span class="badge bg-danger mt-1">{{ $tagihanBelumLunas->count() }}</span>
                    @endif
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('transaksi.mahasiswa') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <i class="bi bi-cash-stack fs-1 mb-2"></i>
                    <span>Riwayat Pembayaran</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('cicilan.tracking') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <i class="bi bi-list-check fs-1 mb-2"></i>
                    <span>Cicilan</span>
                    @if(isset($cicilanAktif) && $cicilanAktif->count() > 0)
                    <span class="badge bg-warning text-dark mt-1">{{ $cicilanAktif->count() }}</span>
                    @endif
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('mahasiswa.potongan') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <i class="bi bi-tags fs-1 mb-2"></i>
                    <span>Potongan</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('beasiswa.available') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <i class="bi bi-award fs-1 mb-2"></i>
                    <span>Beasiswa</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('mahasiswa.tagihan') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <i class="bi bi-card-list fs-1 mb-2"></i>
                    <span>Kartu Tagihan</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('mahasiswa.rekap-pembayaran') }}" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <i class="bi bi-file-earmark-bar-graph fs-1 mb-2"></i>
                    <span>Rekap Pembayaran</span>
                </a>
            </div>
        </div>
    </div>
</div>

@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data mahasiswa tidak ditemukan. Silakan hubungi administrator.
</div>
@endif
@endsection
