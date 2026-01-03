@extends('layouts.app')

@section('title', 'Detail Bimbingan Akademik')

@section('content')
<div class="page-title">
    <h4>Detail Bimbingan Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.bimbingan.index') }}">Bimbingan</a></li>
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
                        <td>: {{ $bimbingan->mahasiswa->nim ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>: {{ $bimbingan->mahasiswa->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Program Studi</strong></td>
                        <td>: {{ $bimbingan->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Angkatan</strong></td>
                        <td>: {{ $bimbingan->mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Info Dosen -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Dosen Pembimbing</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>NIDN</strong></td>
                        <td>: {{ $bimbingan->dosen->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>: {{ $bimbingan->dosen->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Detail Bimbingan -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Detail Bimbingan</h6>
                <span class="badge bg-{{ $bimbingan->status == 'selesai' ? 'success' : ($bimbingan->status == 'berlangsung' ? 'primary' : 'warning') }}">
                    {{ ucfirst($bimbingan->status ?? 'pending') }}
                </span>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="25%"><strong>Tanggal</strong></td>
                        <td>: {{ $bimbingan->tanggal ? \Carbon\Carbon::parse($bimbingan->tanggal)->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Topik</strong></td>
                        <td>: {{ $bimbingan->topik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tempat</strong></td>
                        <td>: {{ $bimbingan->tempat ?? '-' }}</td>
                    </tr>
                </table>
                
                <hr>
                
                <h6>Catatan Bimbingan</h6>
                <div class="bg-light p-3 rounded">
                    {!! nl2br(e($bimbingan->catatan ?? 'Tidak ada catatan')) !!}
                </div>
                
                @if($bimbingan->hasil)
                <hr>
                <h6>Hasil/Kesimpulan</h6>
                <div class="bg-light p-3 rounded">
                    {!! nl2br(e($bimbingan->hasil)) !!}
                </div>
                @endif
                
                @if($bimbingan->rekomendasi)
                <hr>
                <h6>Rekomendasi</h6>
                <div class="bg-light p-3 rounded">
                    {!! nl2br(e($bimbingan->rekomendasi)) !!}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('dekan.bimbingan.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
@endsection
