@extends('layouts.app')

@section('title', 'Detail Tugas Akhir')

@section('content')
<div class="page-title">
    <h4>Detail Tugas Akhir</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.tugas-akhir.index') }}">Tugas Akhir</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">NIM</td>
                        <td><strong>{{ $tugasAkhir->mahasiswa->nim ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $tugasAkhir->mahasiswa->nama ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $tugasAkhir->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Pembimbing</h6>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Pembimbing 1:</strong></p>
                <p>{{ $tugasAkhir->pembimbing1->nama ?? '-' }}</p>
                <p class="mb-1"><strong>Pembimbing 2:</strong></p>
                <p class="mb-0">{{ $tugasAkhir->pembimbing2->nama ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Informasi Tugas Akhir</h6>
                <span class="badge bg-{{ $tugasAkhir->status == 'selesai' ? 'success' : ($tugasAkhir->status == 'bimbingan' ? 'info' : 'warning') }}">
                    {{ ucfirst(str_replace('_', ' ', $tugasAkhir->status)) }}
                </span>
            </div>
            <div class="card-body">
                <h5>{{ $tugasAkhir->judul ?? 'Belum ada judul' }}</h5>
                <hr>
                <p><strong>Abstrak:</strong></p>
                <p>{{ $tugasAkhir->abstrak ?? 'Belum ada abstrak' }}</p>
            </div>
        </div>

        <!-- Riwayat Bimbingan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Riwayat Bimbingan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Pembimbing</th>
                                <th>Catatan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tugasAkhir->bimbingan as $bimbingan)
                            <tr>
                                <td>{{ $bimbingan->tanggal ? \Carbon\Carbon::parse($bimbingan->tanggal)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $bimbingan->dosen->nama ?? '-' }}</td>
                                <td>{{ Str::limit($bimbingan->catatan ?? '-', 100) }}</td>
                                <td>
                                    <span class="badge bg-{{ $bimbingan->status == 'selesai' ? 'success' : 'warning' }}">
                                        {{ ucfirst($bimbingan->status ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada riwayat bimbingan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Seminar & Sidang -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Seminar Proposal</h6>
                    </div>
                    <div class="card-body">
                        @if($tugasAkhir->seminarProposal)
                            <p><strong>Tanggal:</strong> {{ $tugasAkhir->seminarProposal->tanggal ? \Carbon\Carbon::parse($tugasAkhir->seminarProposal->tanggal)->format('d/m/Y') : '-' }}</p>
                            <p><strong>Nilai:</strong> {{ $tugasAkhir->seminarProposal->nilai ?? '-' }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $tugasAkhir->seminarProposal->status == 'lulus' ? 'success' : 'warning' }}">
                                    {{ ucfirst($tugasAkhir->seminarProposal->status ?? '-') }}
                                </span>
                            </p>
                        @else
                            <p class="text-muted">Belum ada jadwal seminar</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Sidang</h6>
                    </div>
                    <div class="card-body">
                        @if($tugasAkhir->sidang)
                            <p><strong>Tanggal:</strong> {{ $tugasAkhir->sidang->tanggal ? \Carbon\Carbon::parse($tugasAkhir->sidang->tanggal)->format('d/m/Y') : '-' }}</p>
                            <p><strong>Nilai:</strong> {{ $tugasAkhir->sidang->nilai ?? '-' }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $tugasAkhir->sidang->status == 'lulus' ? 'success' : 'warning' }}">
                                    {{ ucfirst($tugasAkhir->sidang->status ?? '-') }}
                                </span>
                            </p>
                        @else
                            <p class="text-muted">Belum ada jadwal sidang</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('dekan.tugas-akhir.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left me-1"></i> Kembali
</a>
@endsection
