@extends('layouts.app')

@section('title', 'Detail Presensi')

@section('content')
<div class="page-title">
    <h4>Detail Presensi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.presensi.index') }}">Presensi</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-check me-2"></i>Informasi Presensi
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Tanggal</div>
                    <div class="col-md-8">
                        <strong>{{ \Carbon\Carbon::parse($presensi->tanggal)->format('d F Y') }}</strong>
                        ({{ \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('l') }})
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Nama Pegawai</div>
                    <div class="col-md-8">
                        @if($presensi->dosen)
                        <strong>{{ $presensi->dosen->nama_lengkap }}</strong>
                        <br><small class="text-muted">NIDN: {{ $presensi->dosen->nidn ?? '-' }} | NIP: {{ $presensi->dosen->nip ?? '-' }}</small>
                        <br><span class="badge bg-info">Dosen</span>
                        @elseif($presensi->pegawai)
                        <strong>{{ $presensi->pegawai->nama }}</strong>
                        <br><small class="text-muted">NIP: {{ $presensi->pegawai->nip ?? '-' }}</small>
                        <br><span class="badge bg-secondary">Tenaga Kependidikan</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Status</div>
                    <div class="col-md-8">{!! $presensi->status_badge !!}</div>
                </div>
                
                <hr>
                
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Jam Masuk</div>
                    <div class="col-md-8">
                        @if($presensi->jam_masuk)
                        <span class="{{ $presensi->isTerlambat() ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                            {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') }}
                        </span>
                        @if($presensi->isTerlambat())
                        <span class="badge bg-danger ms-2">Terlambat</span>
                        @endif
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Jam Keluar</div>
                    <div class="col-md-8">
                        @if($presensi->jam_keluar)
                        <strong>{{ \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i:s') }}</strong>
                        @else
                        <span class="text-muted">Belum checkout</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Durasi Kerja</div>
                    <div class="col-md-8">
                        @if($presensi->jam_masuk && $presensi->jam_keluar)
                        <span class="badge bg-primary">{{ $presensi->hitungJamKerja() }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
                
                @if($jamKerja)
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Jam Kerja Standar</div>
                    <div class="col-md-8">
                        {{ \Carbon\Carbon::parse($jamKerja->jam_masuk)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($jamKerja->jam_keluar)->format('H:i') }}
                        <br><small class="text-muted">Toleransi: {{ $jamKerja->toleransi_terlambat }} menit</small>
                    </div>
                </div>
                @endif
                
                <hr>
                
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Keterangan</div>
                    <div class="col-md-8">{{ $presensi->keterangan ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Input Manual</div>
                    <div class="col-md-8">
                        @if($presensi->is_manual)
                        <span class="badge bg-warning text-dark">Ya</span>
                        @else
                        <span class="badge bg-success">Mesin</span>
                        @endif
                    </div>
                </div>
                @if($presensi->diinputOleh)
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Diinput Oleh</div>
                    <div class="col-md-8">{{ $presensi->diinputOleh->name }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        @if($presensi->lokasi_masuk || $presensi->lokasi_keluar)
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-geo-alt me-2"></i>Lokasi
            </div>
            <div class="card-body">
                @if($presensi->lokasi_masuk)
                <p class="mb-2"><strong>Lokasi Masuk:</strong><br>{{ $presensi->lokasi_masuk }}</p>
                @endif
                @if($presensi->lokasi_keluar)
                <p class="mb-0"><strong>Lokasi Keluar:</strong><br>{{ $presensi->lokasi_keluar }}</p>
                @endif
            </div>
        </div>
        @endif
        
        <div class="card">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>Aksi
            </div>
            <div class="card-body">
                <a href="{{ route('kepegawaian.presensi.edit', $presensi) }}" class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-pencil me-1"></i>Edit Presensi
                </a>
                <form action="{{ route('kepegawaian.presensi.destroy', $presensi) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data presensi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i>Hapus Presensi
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="{{ route('kepegawaian.presensi.index', ['tanggal' => $presensi->tanggal->format('Y-m-d')]) }}" class="btn btn-secondary w-100">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>
@endsection
