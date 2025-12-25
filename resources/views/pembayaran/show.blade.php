@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="page-title">
    <h4>Detail Pembayaran</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembayaran.index') }}">Pembayaran</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>Bukti Pembayaran</span>
                <span class="badge bg-{{ $pembayaran->status == 'lunas' ? 'success' : ($pembayaran->status == 'pending' ? 'warning' : 'danger') }}">
                    {{ ucfirst(str_replace('_', ' ', $pembayaran->status)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Data Mahasiswa</h6>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td width="100">NIM</td>
                                <td>: <code>{{ $pembayaran->mahasiswa->nim ?? '-' }}</code></td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td>: {{ $pembayaran->mahasiswa->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Prodi</td>
                                <td>: {{ $pembayaran->mahasiswa->programStudi->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Periode</h6>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td width="120">Tahun Akademik</td>
                                <td>: {{ $pembayaran->tahunAkademik->nama_lengkap ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Jenis</td>
                                <td>: {{ ucfirst($pembayaran->jenis) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Detail Pembayaran</h6>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td width="100">Jumlah</td>
                                <td>: <strong class="text-primary">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>: 
                                    @if($pembayaran->status == 'lunas')
                                    <span class="badge bg-success">Lunas</span>
                                    @elseif($pembayaran->status == 'pending')
                                    <span class="badge bg-warning">Menunggu Verifikasi</span>
                                    @else
                                    <span class="badge bg-danger">Belum Bayar</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Tanggal Bayar</td>
                                <td>: {{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d F Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($pembayaran->keterangan)
                        <h6 class="text-muted">Keterangan</h6>
                        <p class="mb-0">{{ $pembayaran->keterangan }}</p>
                        @endif
                    </div>
                </div>
                
                @if($pembayaran->status == 'lunas')
                <hr>
                <div class="text-center">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2 mb-0">Pembayaran telah diverifikasi</p>
                </div>
                @elseif($pembayaran->status == 'belum_bayar')
                <hr>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Silakan lakukan pembayaran sebelum batas waktu yang ditentukan.
                </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('pembayaran.edit', $pembayaran) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
