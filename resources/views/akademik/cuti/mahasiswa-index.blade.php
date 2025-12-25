@extends('layouts.app')

@section('title', 'Cuti Akademik Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Cuti Akademik</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Cuti Akademik</li>
            </ol>
        </nav>
    </div>
    @if($canApply)
    <a href="{{ route('cuti.mahasiswa.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Ajukan Cuti
    </a>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list me-2"></i>Riwayat Pengajuan Cuti
            </div>
            <div class="card-body p-0">
                @if($cutis->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($cutis as $cuti)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <span class="badge bg-{{ $cuti->alasan_badge }} me-1">{{ $cuti->alasan }}</span>
                                    Cuti {{ $cuti->jumlah_semester }} Semester
                                </h6>
                                <p class="mb-1 text-muted small">
                                    {{ $cuti->tanggal_mulai->format('d M Y') }} - {{ $cuti->tanggal_selesai->format('d M Y') }}
                                </p>
                                <p class="mb-0 small">{{ Str::limit($cuti->keterangan, 100) }}</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $cuti->status_badge }}">{{ $cuti->status }}</span>
                                <br>
                                <small class="text-muted">{{ $cuti->created_at->format('d/m/Y') }}</small>
                                <br>
                                @if($cuti->status == 'Pending')
                                <form action="{{ route('cuti.cancel', $cuti) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger mt-1" onclick="return confirm('Batalkan pengajuan cuti ini?')">
                                        <i class="bi bi-x-lg"></i> Batalkan
                                    </button>
                                </form>
                                @elseif($cuti->status == 'Disetujui Dekan' || $cuti->status == 'Selesai')
                                <a href="{{ route('cuti.print-surat', $cuti) }}" class="btn btn-sm btn-outline-secondary mt-1" target="_blank">
                                    <i class="bi bi-printer"></i> Cetak Surat
                                </a>
                                @endif
                            </div>
                        </div>
                        
                        @if($cuti->catatan_kaprodi || $cuti->catatan_dekan)
                        <hr class="my-2">
                        <div class="small">
                            @if($cuti->catatan_kaprodi)
                            <p class="mb-1"><strong>Catatan Kaprodi:</strong> {{ $cuti->catatan_kaprodi }}</p>
                            @endif
                            @if($cuti->catatan_dekan)
                            <p class="mb-0"><strong>Catatan Dekan:</strong> {{ $cuti->catatan_dekan }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">Belum ada riwayat pengajuan cuti</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Informasi Mahasiswa
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">NIM</td>
                        <td>{{ $mahasiswa->nim }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>{{ $mahasiswa->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>{{ $mahasiswa->semester_aktif }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td><span class="badge bg-{{ $mahasiswa->status == 'Aktif' ? 'success' : ($mahasiswa->status == 'Cuti' ? 'warning' : 'secondary') }}">{{ $mahasiswa->status }}</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Cuti
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Cuti dapat diajukan maksimal 2 semester.
                </p>
                <p class="small text-muted mb-2">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Persetujuan melalui Kaprodi dan Dekan.
                </p>
                <p class="small text-muted mb-2">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Lampirkan dokumen pendukung sesuai alasan cuti.
                </p>
                <p class="small text-muted mb-0">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Status mahasiswa akan berubah menjadi "Cuti" setelah disetujui.
                </p>
            </div>
        </div>

        @if(!$canApply)
        <div class="alert alert-warning mt-4">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Anda masih memiliki pengajuan cuti yang sedang diproses atau aktif.
        </div>
        @endif
    </div>
</div>
@endsection
