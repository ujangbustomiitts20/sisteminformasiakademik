@extends('layouts.app')

@section('title', 'Kegiatan Lapangan Saya')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">PKL / Magang / KKN Saya</h1>
        @if($periodeTersedia->isNotEmpty())
        <a href="{{ route('mahasiswa.kegiatan-lapangan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Daftar Kegiatan
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($pendaftaranSaya->isEmpty())
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="bi bi-briefcase display-1 text-muted"></i>
            <h4 class="mt-4">Belum Ada Pendaftaran Kegiatan</h4>
            <p class="text-muted">Daftar untuk mengikuti PKL, Magang, atau KKN sesuai dengan periode yang tersedia.</p>
            @if($periodeTersedia->isNotEmpty())
            <a href="{{ route('mahasiswa.kegiatan-lapangan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Daftar Sekarang
            </a>
            @else
            <p class="text-muted">Tidak ada periode pendaftaran yang sedang dibuka saat ini.</p>
            @endif
        </div>
    </div>
    @else
    @foreach($pendaftaranSaya as $pendaftaran)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ $pendaftaran->periode->jenisKegiatan->nama ?? 'Kegiatan' }}
                - {{ $pendaftaran->periode->nama ?? '' }}
            </h6>
            @php
                $badgeClass = match($pendaftaran->status) {
                    'draft' => 'secondary',
                    'diajukan' => 'warning',
                    'disetujui' => 'info',
                    'ditolak' => 'danger',
                    'berlangsung' => 'primary',
                    'selesai' => 'success',
                    default => 'secondary'
                };
            @endphp
            <span class="badge bg-{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $pendaftaran->status)) }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Mitra Diterima:</strong> {{ $pendaftaran->mitraDiterima->nama ?? 'Belum ditentukan' }}</p>
                    <p><strong>Dosen Pembimbing:</strong> {{ $pendaftaran->dosenPembimbing->nama ?? 'Belum ditentukan' }}</p>
                    <p><strong>Tanggal Mulai:</strong> {{ $pendaftaran->tanggal_mulai ? \Carbon\Carbon::parse($pendaftaran->tanggal_mulai)->format('d/m/Y') : '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Pembimbing Lapangan:</strong> {{ $pendaftaran->nama_pembimbing_lapangan ?? '-' }}</p>
                    <p><strong>Log Kegiatan:</strong> {{ $pendaftaran->logKegiatan->count() ?? 0 }} entri</p>
                    <p><strong>Tanggal Selesai:</strong> {{ $pendaftaran->tanggal_selesai ? \Carbon\Carbon::parse($pendaftaran->tanggal_selesai)->format('d/m/Y') : '-' }}</p>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('mahasiswa.kegiatan-lapangan.show', $pendaftaran->hashid) }}" class="btn btn-info">
                    <i class="bi bi-eye"></i> Lihat Detail
                </a>
                @if($pendaftaran->status == 'berlangsung')
                <a href="{{ route('mahasiswa.kegiatan-lapangan.log.create', $pendaftaran->hashid) }}" class="btn btn-primary">
                    <i class="bi bi-journal-plus"></i> Tambah Log Kegiatan
                </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>
@endsection
