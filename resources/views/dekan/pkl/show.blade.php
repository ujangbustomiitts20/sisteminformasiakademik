@extends('layouts.app')

@section('title', 'Detail PKL/Magang')

@section('content')
<div class="page-title">
    <h4>Detail PKL/Magang</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.pkl.index') }}">PKL/Magang</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Info Mahasiswa -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Data Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>NIM</strong></td>
                        <td>: {{ $pendaftaran->mahasiswa->nim ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>: {{ $pendaftaran->mahasiswa->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Program Studi</strong></td>
                        <td>: {{ $pendaftaran->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Angkatan</strong></td>
                        <td>: {{ $pendaftaran->mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Status -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Status Kegiatan</h6>
            </div>
            <div class="card-body text-center">
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'diterima' => 'info',
                        'berlangsung' => 'primary',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        'batal' => 'secondary',
                    ];
                @endphp
                <span class="badge bg-{{ $statusColors[$pendaftaran->status] ?? 'secondary' }} fs-6 px-3 py-2">
                    {{ ucfirst($pendaftaran->status) }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Info Kegiatan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Kegiatan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Jenis Kegiatan</strong></td>
                        <td>: {{ $pendaftaran->periode->jenisKegiatan->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Periode</strong></td>
                        <td>: {{ $pendaftaran->periode->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Mulai</strong></td>
                        <td>: {{ $pendaftaran->tanggal_mulai ? \Carbon\Carbon::parse($pendaftaran->tanggal_mulai)->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Selesai</strong></td>
                        <td>: {{ $pendaftaran->tanggal_selesai ? \Carbon\Carbon::parse($pendaftaran->tanggal_selesai)->format('d F Y') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Info Mitra/Perusahaan -->
        @if($pendaftaran->mitraDiterima)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Tempat PKL/Magang</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Perusahaan</strong></td>
                        <td>: {{ $pendaftaran->mitraDiterima->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td>: {{ $pendaftaran->mitraDiterima->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Kontak</strong></td>
                        <td>: {{ $pendaftaran->mitraDiterima->kontak ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
        
        <!-- Dosen Pembimbing -->
        @if($pendaftaran->dosenPembimbing)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Dosen Pembimbing</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>NIDN</strong></td>
                        <td>: {{ $pendaftaran->dosenPembimbing->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>: {{ $pendaftaran->dosenPembimbing->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
        
        <!-- Laporan/Log Kegiatan -->
        @if($pendaftaran->logKegiatan && $pendaftaran->logKegiatan->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Log Kegiatan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kegiatan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendaftaran->logKegiatan as $index => $log)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $log->tanggal ? \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') : '-' }}</td>
                                <td>{{ Str::limit($log->kegiatan ?? $log->deskripsi ?? '-', 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->status == 'disetujui' ? 'success' : ($log->status == 'ditolak' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($log->status ?? 'pending') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Penilaian -->
        @if($pendaftaran->penilaian && $pendaftaran->penilaian->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Penilaian</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Jenis Penilai</th>
                                <th>Nilai</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendaftaran->penilaian as $penilaian)
                            <tr>
                                <td>{{ ucfirst($penilaian->jenis_penilai ?? '-') }}</td>
                                <td><strong>{{ $penilaian->nilai ?? '-' }}</strong></td>
                                <td>{{ $penilaian->keterangan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('dekan.pkl.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
@endsection
