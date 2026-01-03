@extends('layouts.app')

@section('title', 'Detail Konversi Nilai')

@section('content')
<div class="page-title">
    <h4>Detail Pengajuan Konversi Nilai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.konversi-nilai.index') }}">Konversi Nilai</a></li>
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
                        <td>: {{ $pengajuanKonversi->mahasiswa->nim ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>: {{ $pengajuanKonversi->mahasiswa->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Program Studi</strong></td>
                        <td>: {{ $pengajuanKonversi->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Angkatan</strong></td>
                        <td>: {{ $pengajuanKonversi->mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Status Pengajuan -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Status Pengajuan</h6>
            </div>
            <div class="card-body text-center">
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'diproses' => 'info',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    ];
                @endphp
                <span class="badge bg-{{ $statusColors[$pengajuanKonversi->status] ?? 'secondary' }} fs-6 px-3 py-2">
                    {{ ucfirst($pengajuanKonversi->status) }}
                </span>
                
                <hr>
                
                <table class="table table-borderless table-sm text-start">
                    <tr>
                        <td><strong>Tanggal Ajuan</strong></td>
                        <td>: {{ $pengajuanKonversi->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @if($pengajuanKonversi->tanggal_proses)
                    <tr>
                        <td><strong>Tanggal Proses</strong></td>
                        <td>: {{ $pengajuanKonversi->tanggal_proses->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Info Pengajuan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Pengajuan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="25%"><strong>Jenis Konversi</strong></td>
                        <td>: {{ $pengajuanKonversi->jenis ?? 'Transfer Kredit' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Asal Perguruan Tinggi</strong></td>
                        <td>: {{ $pengajuanKonversi->asal_pt ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Alasan</strong></td>
                        <td>: {{ $pengajuanKonversi->alasan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Detail Konversi -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Detail Mata Kuliah yang Dikonversi</h6>
            </div>
            <div class="card-body">
                @if($pengajuanKonversi->detailKonversi && $pengajuanKonversi->detailKonversi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th colspan="3" class="text-center bg-info text-white">Mata Kuliah Asal</th>
                                <th colspan="3" class="text-center bg-success text-white">Mata Kuliah Tujuan</th>
                                <th rowspan="2" class="align-middle text-center">Status</th>
                            </tr>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>SKS</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>SKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengajuanKonversi->detailKonversi as $detail)
                            <tr>
                                <td>{{ $detail->mataKuliahAsal->kode ?? $detail->kode_mk_asal ?? '-' }}</td>
                                <td>{{ $detail->mataKuliahAsal->nama ?? $detail->nama_mk_asal ?? '-' }}</td>
                                <td class="text-center">{{ $detail->mataKuliahAsal->sks ?? $detail->sks_asal ?? '-' }}</td>
                                <td>{{ $detail->mataKuliahTujuan->kode ?? '-' }}</td>
                                <td>{{ $detail->mataKuliahTujuan->nama ?? '-' }}</td>
                                <td class="text-center">{{ $detail->mataKuliahTujuan->sks ?? '-' }}</td>
                                <td class="text-center">
                                    @if($detail->status == 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($detail->status == 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end"><strong>Total SKS:</strong></td>
                                <td class="text-center"><strong>{{ $pengajuanKonversi->detailKonversi->sum(fn($d) => $d->mataKuliahAsal->sks ?? $d->sks_asal ?? 0) }}</strong></td>
                                <td colspan="2" class="text-end"><strong>Total SKS:</strong></td>
                                <td class="text-center"><strong>{{ $pengajuanKonversi->detailKonversi->sum(fn($d) => $d->mataKuliahTujuan->sks ?? 0) }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-file-earmark-x" style="font-size: 3rem;"></i>
                    <p class="mt-2">Tidak ada detail konversi</p>
                </div>
                @endif
            </div>
        </div>
        
        @if($pengajuanKonversi->catatan)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Catatan</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{!! nl2br(e($pengajuanKonversi->catatan)) !!}</p>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('dekan.konversi-nilai.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
@endsection
