@extends('layouts.app')

@section('title', 'Detail Izin Keluar')

@section('content')
<div class="page-title">
    <h4>Detail Izin Keluar</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.approval.izin-keluar.index') }}">Izin Keluar</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Izin Keluar</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Nama Dosen</th>
                        <td>: {{ $izinKeluar->dosen?->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>NIDN</th>
                        <td>: {{ $izinKeluar->dosen?->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td>: {{ $izinKeluar->dosen?->programStudi?->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>: {{ $izinKeluar->tanggal?->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Jam Keluar</th>
                        <td>: {{ \Carbon\Carbon::parse($izinKeluar->jam_keluar)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Jam Kembali</th>
                        <td>: {{ \Carbon\Carbon::parse($izinKeluar->jam_kembali)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Keperluan</th>
                        <td>: <span class="badge bg-secondary">{{ $izinKeluar->keperluan_label }}</span></td>
                    </tr>
                    <tr>
                        <th>Tujuan</th>
                        <td>: {{ $izinKeluar->tujuan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>: {{ $izinKeluar->keterangan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status Approval</h5>
            </div>
            <div class="card-body">
                <!-- Status Kaprodi -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Status Kaprodi</label>
                    @if($izinKeluar->status_kaprodi === 'pending')
                        <span class="badge bg-warning d-block py-2">Menunggu Approval</span>
                    @elseif($izinKeluar->status_kaprodi === 'disetujui')
                        <span class="badge bg-success d-block py-2">Disetujui Kaprodi</span>
                        @if($izinKeluar->tanggal_approval_kaprodi)
                            <small class="text-muted d-block mt-1">{{ $izinKeluar->tanggal_approval_kaprodi->format('d/m/Y H:i') }}</small>
                        @endif
                    @elseif($izinKeluar->status_kaprodi === 'ditolak')
                        <span class="badge bg-danger d-block py-2">Ditolak Kaprodi</span>
                    @else
                        <span class="badge bg-secondary d-block py-2">-</span>
                    @endif
                </div>
                
                @if($izinKeluar->catatan_kaprodi)
                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan Kaprodi</label>
                    <p class="mb-0">{{ $izinKeluar->catatan_kaprodi }}</p>
                </div>
                @endif
                
                <!-- Status Admin -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Status Admin</label>
                    <span class="badge bg-{{ $izinKeluar->status_color }} d-block py-2">{{ $izinKeluar->status_label }}</span>
                </div>
                
                @if($izinKeluar->catatan_approval)
                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan Admin</label>
                    <p class="mb-0">{{ $izinKeluar->catatan_approval }}</p>
                </div>
                @endif
            </div>
        </div>
        
        @if($izinKeluar->status_kaprodi === 'pending')
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kaprodi.approval.izin-keluar.approve', $izinKeluar) }}" method="POST" class="mb-2">
                    @csrf
                    <div class="mb-2">
                        <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Catatan (opsional)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui izin keluar ini?')">
                        <i class="bi bi-check-lg me-1"></i> Setujui
                    </button>
                </form>
                
                <form action="{{ route('kaprodi.approval.izin-keluar.reject', $izinKeluar) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Alasan penolakan (wajib)" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Tolak izin keluar ini?')">
                        <i class="bi bi-x-lg me-1"></i> Tolak
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('kaprodi.approval.izin-keluar.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>
@endsection
