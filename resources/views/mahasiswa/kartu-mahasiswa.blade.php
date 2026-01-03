@extends('layouts.app')

@section('title', 'Kartu Mahasiswa')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h3 mb-0">Kartu Mahasiswa Digital</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kartu Mahasiswa</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('mahasiswa.kartu.download') }}" class="btn btn-primary">
        <i class="bi bi-download me-1"></i>Download PDF
    </a>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($mahasiswa)
<div class="row justify-content-center">
    <div class="col-lg-6">
        <!-- Preview Kartu -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Preview Kartu Mahasiswa</h6>
            </div>
            <div class="card-body text-center">
                <!-- Kartu Preview -->
                <div class="kartu-mahasiswa mx-auto" style="max-width: 400px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%); border-radius: 15px; padding: 20px; color: white; position: relative; overflow: hidden;">
                    <!-- Status Badge -->
                    <div style="position: absolute; top: 12px; right: 12px; background: {{ $mahasiswa->status == 'Aktif' ? '#22c55e' : '#ef4444' }}; padding: 4px 12px; border-radius: 15px; font-size: 11px; font-weight: bold;">
                        {{ $mahasiswa->status }}
                    </div>
                    
                    <!-- Header -->
                    <div class="text-start mb-3" style="font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase;">
                        SISTEM INFORMASI AKADEMIK
                    </div>
                    
                    <div class="row align-items-center">
                        <!-- Foto -->
                        <div class="col-4">
                            <div style="width: 100px; height: 130px; background: white; border-radius: 8px; overflow: hidden; margin: 0 auto;">
                                @if($mahasiswa->foto && Storage::exists($mahasiswa->foto))
                                <img src="{{ asset('storage/' . $mahasiswa->foto) }}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #e0e7ff; color: #4f46e5; font-size: 40px; font-weight: bold;">
                                    {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Info -->
                        <div class="col-8 text-start">
                            <h5 class="mb-1" style="font-size: 16px; font-weight: bold;">{{ strtoupper($mahasiswa->nama) }}</h5>
                            <h6 class="mb-2" style="font-size: 14px; letter-spacing: 2px;">{{ $mahasiswa->nim }}</h6>
                            <p class="mb-1" style="font-size: 12px; opacity: 0.9;"><strong>{{ $mahasiswa->programStudi->nama ?? '-' }}</strong></p>
                            <p class="mb-1" style="font-size: 11px; opacity: 0.8;">{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</p>
                            <p class="mb-1" style="font-size: 11px; opacity: 0.8;">Angkatan: {{ $mahasiswa->angkatan ?? '-' }}</p>
                            @if($mahasiswa->tempat_lahir || $mahasiswa->tanggal_lahir)
                            <p class="mb-0" style="font-size: 10px; opacity: 0.7;">
                                {{ $mahasiswa->tempat_lahir }}{{ $mahasiswa->tanggal_lahir ? ', ' . $mahasiswa->tanggal_lahir->format('d/m/Y') : '' }}
                            </p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="text-start mt-3 pt-2" style="border-top: 1px solid rgba(255,255,255,0.3); font-size: 10px; opacity: 0.8;">
                        <p class="mb-0"><strong>Kartu Mahasiswa Resmi</strong> - Berlaku selama terdaftar aktif.</p>
                        <p class="mb-0">Validasi: <strong>{{ strtoupper(substr(md5($mahasiswa->nim), 0, 8)) }}</strong></p>
                    </div>
                </div>
                
                <p class="text-muted mt-3 mb-0">
                    <small><i class="bi bi-info-circle me-1"></i>Kartu ini dapat digunakan sebagai identitas mahasiswa digital.</small>
                </p>
            </div>
        </div>
        
        <!-- Info Mahasiswa -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="35%" class="text-muted">NIM</td>
                        <td><strong>{{ $mahasiswa->nim }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Lengkap</td>
                        <td><strong>{{ $mahasiswa->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fakultas</td>
                        <td>{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($mahasiswa->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @elseif($mahasiswa->status == 'Lulus')
                            <span class="badge bg-primary">Lulus</span>
                            @elseif($mahasiswa->status == 'Cuti')
                            <span class="badge bg-warning">Cuti</span>
                            @else
                            <span class="badge bg-secondary">{{ $mahasiswa->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @if($mahasiswa->tempat_lahir)
                    <tr>
                        <td class="text-muted">Tempat Lahir</td>
                        <td>{{ $mahasiswa->tempat_lahir }}</td>
                    </tr>
                    @endif
                    @if($mahasiswa->tanggal_lahir)
                    <tr>
                        <td class="text-muted">Tanggal Lahir</td>
                        <td>{{ $mahasiswa->tanggal_lahir->format('d F Y') }}</td>
                    </tr>
                    @endif
                    @if($mahasiswa->email)
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $mahasiswa->email }}</td>
                    </tr>
                    @endif
                    @if($mahasiswa->no_hp)
                    <tr>
                        <td class="text-muted">No. HP</td>
                        <td>{{ $mahasiswa->no_hp }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="card-footer bg-white">
                <div class="d-grid gap-2">
                    <a href="{{ route('mahasiswa.kartu.download') }}" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>Download Kartu Mahasiswa (PDF)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data mahasiswa tidak ditemukan. Silakan hubungi admin untuk memperbarui data Anda.
</div>
@endif
@endsection
