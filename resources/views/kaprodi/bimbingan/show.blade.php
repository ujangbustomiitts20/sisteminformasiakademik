@extends('layouts.app')

@section('title', 'Detail Bimbingan')

@section('content')
<div class="page-title">
    <h4>Detail Bimbingan Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.bimbingan.index') }}">Bimbingan Akademik</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Informasi Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" width="35%">NIM</td>
                        <td><strong>{{ $bimbingan->mahasiswa->nim ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>{{ $bimbingan->mahasiswa->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $bimbingan->mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">IPK</td>
                        <td>{{ number_format($bimbingan->mahasiswa->ipk ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total SKS</td>
                        <td>{{ $bimbingan->mahasiswa->total_sks ?? 0 }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Dosen Pembimbing Akademik</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" width="35%">NIP/NIDN</td>
                        <td>{{ $bimbingan->dosen->nip ?? $bimbingan->dosen->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $bimbingan->dosen->nama ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $bimbingan->dosen->email ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Detail Bimbingan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted" width="35%">Tanggal</td>
                        <td>{{ $bimbingan->tanggal?->format('d F Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waktu</td>
                        <td>{{ $bimbingan->waktu ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @php
                                $status = $bimbingan->status ?? 'pending';
                                $statusClass = match($status) {
                                    'selesai' => 'success',
                                    'dijadwalkan' => 'info',
                                    'dibatalkan' => 'danger',
                                    default => 'warning'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Topik</td>
                        <td>{{ $bimbingan->topik ?? '-' }}</td>
                    </tr>
                </table>

                <hr>

                <h6 class="text-muted mb-2">Catatan Bimbingan</h6>
                <div class="p-3 bg-light rounded">
                    {{ $bimbingan->catatan ?? 'Tidak ada catatan' }}
                </div>

                @if($bimbingan->rekomendasi)
                <h6 class="text-muted mb-2 mt-3">Rekomendasi</h6>
                <div class="p-3 bg-light rounded">
                    {{ $bimbingan->rekomendasi }}
                </div>
                @endif

                @if($bimbingan->tindak_lanjut)
                <h6 class="text-muted mb-2 mt-3">Tindak Lanjut</h6>
                <div class="p-3 bg-light rounded">
                    {{ $bimbingan->tindak_lanjut }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('kaprodi.bimbingan.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>
@endsection
