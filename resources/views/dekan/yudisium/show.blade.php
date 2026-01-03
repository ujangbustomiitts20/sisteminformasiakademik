@extends('layouts.app')

@section('title', 'Detail Yudisium')

@section('content')
<div class="page-title">
    <h4>Detail Yudisium</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.yudisium.index') }}">Yudisium</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Info Mahasiswa -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Data Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar avatar-xl bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($yudisium->mahasiswa->nama ?? 'M', 0, 1) }}
                    </div>
                    <h5 class="mt-3 mb-0">{{ $yudisium->mahasiswa->nama ?? '-' }}</h5>
                    <p class="text-muted">{{ $yudisium->mahasiswa->nim ?? '-' }}</p>
                </div>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Program Studi</strong></td>
                        <td>: {{ $yudisium->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Angkatan</strong></td>
                        <td>: {{ $yudisium->mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>IPK</strong></td>
                        <td>: <strong class="text-primary">{{ $ipk }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Total SKS</strong></td>
                        <td>: {{ $totalSks }} SKS</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Status Yudisium -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Status Yudisium</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    @if($yudisium->status == 'lulus')
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-success">LULUS</h5>
                        @if($yudisium->predikat)
                        <span class="badge bg-success fs-6">{{ $yudisium->predikat }}</span>
                        @endif
                    @elseif($yudisium->status == 'tidak_lulus')
                        <div class="mb-3">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-danger">TIDAK LULUS</h5>
                    @else
                        <div class="mb-3">
                            <i class="bi bi-clock-fill text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-warning">PENDING</h5>
                        <p class="text-muted mb-0">Menunggu approval</p>
                    @endif
                </div>
                
                @if($yudisium->tanggal_yudisium)
                <hr>
                <p class="mb-1"><strong>Tanggal Yudisium:</strong></p>
                <p class="mb-0">{{ $yudisium->tanggal_yudisium->format('d F Y') }}</p>
                @endif
                
                @if($yudisium->catatan)
                <hr>
                <p class="mb-1"><strong>Catatan:</strong></p>
                <p class="mb-0 text-muted">{{ $yudisium->catatan }}</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Detail & Approval -->
    <div class="col-md-8">
        <!-- Ringkasan Akademik -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Ringkasan Akademik</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h3 class="mb-0 text-primary">{{ $totalSks }}</h3>
                        <small class="text-muted">Total SKS</small>
                    </div>
                    <div class="col-md-3">
                        <h3 class="mb-0 text-success">{{ $ipk }}</h3>
                        <small class="text-muted">IPK</small>
                    </div>
                    <div class="col-md-3">
                        <h3 class="mb-0 text-info">{{ $yudisium->mahasiswa->angkatan ?? '-' }}</h3>
                        <small class="text-muted">Angkatan</small>
                    </div>
                    <div class="col-md-3">
                        @php
                            $predikat = '-';
                            if ($ipk >= 3.50) $predikat = 'Cum Laude';
                            elseif ($ipk >= 3.00) $predikat = 'Sangat Memuaskan';
                            elseif ($ipk >= 2.50) $predikat = 'Memuaskan';
                            elseif ($ipk >= 2.00) $predikat = 'Cukup';
                        @endphp
                        <h5 class="mb-0 text-warning">{{ $predikat }}</h5>
                        <small class="text-muted">Predikat</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Approval (jika masih pending) -->
        @if($yudisium->status == 'pending')
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Approval Yudisium</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('dekan.yudisium.approval', $yudisium) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="">-- Pilih Keputusan --</option>
                                    <option value="lulus">LULUS</option>
                                    <option value="tidak_lulus">TIDAK LULUS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Predikat Kelulusan</label>
                                <select name="predikat" class="form-select">
                                    <option value="">-- Pilih Predikat --</option>
                                    <option value="Cum Laude" {{ $ipk >= 3.50 ? 'selected' : '' }}>Cum Laude (IPK ≥ 3.50)</option>
                                    <option value="Sangat Memuaskan" {{ $ipk >= 3.00 && $ipk < 3.50 ? 'selected' : '' }}>Sangat Memuaskan (IPK 3.00 - 3.49)</option>
                                    <option value="Memuaskan" {{ $ipk >= 2.50 && $ipk < 3.00 ? 'selected' : '' }}>Memuaskan (IPK 2.50 - 2.99)</option>
                                    <option value="Cukup" {{ $ipk >= 2.00 && $ipk < 2.50 ? 'selected' : '' }}>Cukup (IPK 2.00 - 2.49)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Proses Yudisium
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        
        <!-- Info Wisuda -->
        @if($yudisium->pendaftaranWisuda)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Informasi Pendaftaran Wisuda</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Periode Wisuda</strong></td>
                        <td>: {{ $yudisium->pendaftaranWisuda->periodeWisuda->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Wisuda</strong></td>
                        <td>: {{ $yudisium->pendaftaranWisuda->periodeWisuda->tanggal_wisuda ? $yudisium->pendaftaranWisuda->periodeWisuda->tanggal_wisuda->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Daftar</strong></td>
                        <td>: {{ $yudisium->pendaftaranWisuda->created_at->format('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('dekan.yudisium.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
@endsection
