@extends('layouts.app')

@section('title', 'Potongan & Diskon Saya')

@section('content')
<div class="page-title">
    <h4>Potongan & Diskon Saya</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Potongan & Diskon</li>
        </ol>
    </nav>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success p-3">
                            <i class="bi bi-check-circle text-white fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Potongan Aktif</h6>
                        <h3 class="mb-0 text-success">{{ $potonganAktif->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-primary p-3">
                            <i class="bi bi-cash text-white fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Potongan Digunakan</h6>
                        <h3 class="mb-0 text-primary">Rp {{ number_format($totalPotonganDigunakan, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-warning p-3">
                            <i class="bi bi-hourglass-split text-white fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Menunggu Persetujuan</h6>
                        <h3 class="mb-0 text-warning">{{ $potonganPending->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Promo Tersedia -->
@if($promoTersedia->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <i class="bi bi-gift me-2"></i>Promo & Diskon Tersedia
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($promoTersedia as $promo)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">{{ $promo->nama }}</h6>
                            <span class="badge bg-success">
                                @if($promo->tipe_nilai == 'persen')
                                {{ $promo->nilai }}% OFF
                                @else
                                Rp {{ number_format($promo->nilai, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        <p class="card-text text-muted small mb-2">
                            {{ $promo->jenisPotongan->nama ?? '-' }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                s.d {{ $promo->tanggal_selesai->format('d M Y') }}
                            </small>
                            @if($promo->kuota)
                            <small class="text-muted">
                                <i class="bi bi-people me-1"></i>
                                Sisa {{ $promo->kuota - $promo->kuota_terpakai }}
                            </small>
                            @endif
                        </div>
                        @if($promo->syarat_ketentuan)
                        <hr class="my-2">
                        <small class="text-muted">{{ Str::limit($promo->syarat_ketentuan, 80) }}</small>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Potongan Aktif -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-check-circle text-success me-2"></i>Potongan Aktif Saya</span>
    </div>
    <div class="card-body">
        @if($potonganAktif->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Jenis Potongan</th>
                        <th>Nilai</th>
                        <th>Berlaku</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($potonganAktif as $potongan)
                    <tr>
                        <td><code>{{ $potongan->kode }}</code></td>
                        <td>
                            <strong>{{ $potongan->jenisPotongan->nama ?? '-' }}</strong>
                            <br>
                            <span class="badge bg-{{ $potongan->jenisPotongan->kategori == 'diskon' ? 'primary' : ($potongan->jenisPotongan->kategori == 'keringanan' ? 'info' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $potongan->jenisPotongan->kategori ?? '-')) }}
                            </span>
                        </td>
                        <td>
                            @if($potongan->tipe_nilai == 'persen')
                            <span class="badge bg-success fs-6">{{ $potongan->nilai }}%</span>
                            @if($potongan->nilai_max)
                            <br><small class="text-muted">Max: Rp {{ number_format($potongan->nilai_max, 0, ',', '.') }}</small>
                            @endif
                            @else
                            <span class="badge bg-primary fs-6">Rp {{ number_format($potongan->nilai, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($potongan->tanggal_mulai || $potongan->tanggal_selesai)
                            <small>
                                {{ $potongan->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                                <br>s.d {{ $potongan->tanggal_selesai?->format('d/m/Y') ?? 'Selamanya' }}
                            </small>
                            @else
                            <span class="badge bg-success">Selamanya</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>Aktif
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $potongan->alasan ?? '-' }}</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-tags text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">Belum Ada Potongan Aktif</h5>
            <p class="text-muted">Anda belum memiliki potongan atau diskon yang aktif saat ini.</p>
        </div>
        @endif
    </div>
</div>

<!-- Potongan Pending -->
@if($potonganPending->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-hourglass-split text-warning me-2"></i>Menunggu Persetujuan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Jenis Potongan</th>
                        <th>Nilai</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($potonganPending as $potongan)
                    <tr>
                        <td><code>{{ $potongan->kode }}</code></td>
                        <td>{{ $potongan->jenisPotongan->nama ?? '-' }}</td>
                        <td>
                            @if($potongan->tipe_nilai == 'persen')
                            {{ $potongan->nilai }}%
                            @else
                            Rp {{ number_format($potongan->nilai, 0, ',', '.') }}
                            @endif
                        </td>
                        <td>{{ $potongan->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock me-1"></i>Pending
                            </span>
                        </td>
                        <td>{{ $potongan->alasan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Riwayat Penggunaan Potongan -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i>Riwayat Penggunaan Potongan</span>
    </div>
    <div class="card-body">
        @if($riwayatPotongan->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Tagihan</th>
                        <th>Nama Potongan</th>
                        <th class="text-end">Nominal Tagihan</th>
                        <th class="text-end">Potongan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatPotongan as $riwayat)
                    <tr>
                        <td>{{ $riwayat->created_at->format('d M Y') }}</td>
                        <td>
                            <strong>{{ $riwayat->tagihan->jenis_tagihan ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $riwayat->tagihan->no_tagihan ?? '-' }}</small>
                        </td>
                        <td>
                            <strong>{{ $riwayat->nama_potongan }}</strong>
                            <br><code>{{ $riwayat->kode_potongan }}</code>
                        </td>
                        <td class="text-end">Rp {{ number_format($riwayat->nominal_tagihan, 0, ',', '.') }}</td>
                        <td class="text-end">
                            <span class="text-success fw-bold">
                                -Rp {{ number_format($riwayat->nominal_potongan, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Total Potongan:</th>
                        <th class="text-end text-success">
                            Rp {{ number_format($riwayatPotongan->sum('nominal_potongan'), 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        {{ $riwayatPotongan->withQueryString()->links() }}
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">Belum Ada Riwayat</h5>
            <p class="text-muted">Anda belum pernah menggunakan potongan untuk tagihan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
