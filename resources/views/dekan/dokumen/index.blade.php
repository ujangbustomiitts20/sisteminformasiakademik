@extends('layouts.app')

@section('title', 'Tanda Tangan Dokumen')

@section('content')
<div class="page-title">
    <h4>Dokumen Menunggu Tanda Tangan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Dokumen</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">{{ $fakultas->nama }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Surat</th>
                        <th>Jenis Surat</th>
                        <th>Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratPending as $surat)
                    <tr>
                        <td>{{ $surat->nomor_surat ?? '-' }}</td>
                        <td>{{ $surat->jenis_surat }}</td>
                        <td>
                            {{ $surat->mahasiswa->nama ?? '-' }}<br>
                            <small class="text-muted">{{ $surat->mahasiswa->nim ?? '-' }}</small>
                        </td>
                        <td>{{ $surat->mahasiswa->programStudi->nama ?? '-' }}</td>
                        <td>{{ $surat->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-warning">Menunggu TTD</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                            <p class="mt-2">Tidak ada dokumen yang menunggu tanda tangan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $suratPending->links() }}
    </div>
</div>
@endsection
