@extends('layouts.app')

@section('title', 'Detail Cuti Akademik')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Detail Pengajuan Cuti</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cuti.index') }}">Cuti Akademik</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('cuti.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-text me-2"></i>Informasi Pengajuan</span>
                <span class="badge bg-{{ $cuti->status_badge }}">{{ $cuti->status }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Alasan Cuti</h6>
                        <p class="mb-0"><span class="badge bg-{{ $cuti->alasan_badge }}">{{ $cuti->alasan }}</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Jumlah Semester</h6>
                        <p class="mb-0"><strong>{{ $cuti->jumlah_semester }} Semester</strong></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Tanggal Mulai</h6>
                        <p class="mb-0">{{ $cuti->tanggal_mulai->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Tanggal Selesai</h6>
                        <p class="mb-0">{{ $cuti->tanggal_selesai->format('d F Y') }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-1">Keterangan</h6>
                    <div class="p-3 bg-light rounded">{{ $cuti->keterangan }}</div>
                </div>

                @if($cuti->dokumen_pendukung)
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Dokumen Pendukung</h6>
                    <a href="{{ route('cuti.download-dokumen', $cuti) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download me-1"></i>Download Dokumen
                    </a>
                </div>
                @endif

                @if($cuti->nomor_surat)
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Nomor Surat</h6>
                    <p class="mb-0"><code>{{ $cuti->nomor_surat }}</code></p>
                </div>
                @endif

                @if($cuti->catatan_kaprodi)
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Catatan Kaprodi</h6>
                    <div class="p-3 bg-info bg-opacity-10 rounded">{{ $cuti->catatan_kaprodi }}</div>
                    @if($cuti->disetujuiKaprodiOleh)
                    <small class="text-muted">Oleh: {{ $cuti->disetujuiKaprodiOleh->name }} - {{ $cuti->tanggal_persetujuan_kaprodi?->format('d/m/Y H:i') }}</small>
                    @endif
                </div>
                @endif

                @if($cuti->catatan_dekan)
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Catatan Dekan</h6>
                    <div class="p-3 bg-success bg-opacity-10 rounded">{{ $cuti->catatan_dekan }}</div>
                    @if($cuti->disetujuiDekanOleh)
                    <small class="text-muted">Oleh: {{ $cuti->disetujuiDekanOleh->name }} - {{ $cuti->tanggal_persetujuan_dekan?->format('d/m/Y H:i') }}</small>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Form Approval -->
        @if(in_array($cuti->status, ['Pending', 'Disetujui Kaprodi']))
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-check-square me-2"></i>Proses Persetujuan
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('cuti.approve', $cuti) }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan persetujuan/penolakan..."></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        @if($cuti->status == 'Pending')
                        <button type="submit" name="action" value="approve_kaprodi" class="btn btn-info">
                            <i class="bi bi-check-lg me-1"></i>Setujui (Kaprodi)
                        </button>
                        @elseif($cuti->status == 'Disetujui Kaprodi')
                        <button type="submit" name="action" value="approve_dekan" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>Setujui (Dekan)
                        </button>
                        @endif
                        <button type="submit" name="action" value="reject" class="btn btn-danger">
                            <i class="bi bi-x-lg me-1"></i>Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($cuti->status == 'Disetujui Dekan')
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>Aksi
            </div>
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="{{ route('cuti.print-surat', $cuti) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="bi bi-printer me-1"></i>Cetak Surat Cuti
                    </a>
                    <form action="{{ route('cuti.end', $cuti) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Akhiri masa cuti mahasiswa ini? Status akan kembali menjadi Aktif.')">
                            <i class="bi bi-stop-circle me-1"></i>Akhiri Cuti
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Informasi Mahasiswa
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%">NIM</td>
                        <td>{{ $cuti->mahasiswa->nim }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>{{ $cuti->mahasiswa->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $cuti->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fakultas</td>
                        <td>{{ $cuti->mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>{{ $cuti->mahasiswa->semester_aktif }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td><span class="badge bg-{{ $cuti->mahasiswa->status == 'Aktif' ? 'success' : ($cuti->mahasiswa->status == 'Cuti' ? 'warning' : 'secondary') }}">{{ $cuti->mahasiswa->status }}</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-calendar me-2"></i>Tahun Akademik
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $cuti->tahunAkademik->nama_lengkap ?? '-' }}</p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Timeline
            </div>
            <div class="card-body">
                <div class="timeline-sm">
                    <div class="d-flex align-items-start mb-3">
                        <span class="badge bg-warning me-2">1</span>
                        <div>
                            <small class="text-muted d-block">{{ $cuti->created_at->format('d/m/Y H:i') }}</small>
                            <span>Pengajuan dibuat</span>
                        </div>
                    </div>
                    @if($cuti->tanggal_persetujuan_kaprodi)
                    <div class="d-flex align-items-start mb-3">
                        <span class="badge bg-info me-2">2</span>
                        <div>
                            <small class="text-muted d-block">{{ $cuti->tanggal_persetujuan_kaprodi->format('d/m/Y H:i') }}</small>
                            <span>{{ $cuti->status == 'Ditolak' && !$cuti->tanggal_persetujuan_dekan ? 'Ditolak Kaprodi' : 'Disetujui Kaprodi' }}</span>
                        </div>
                    </div>
                    @endif
                    @if($cuti->tanggal_persetujuan_dekan)
                    <div class="d-flex align-items-start">
                        <span class="badge bg-{{ $cuti->status == 'Ditolak' ? 'danger' : 'success' }} me-2">3</span>
                        <div>
                            <small class="text-muted d-block">{{ $cuti->tanggal_persetujuan_dekan->format('d/m/Y H:i') }}</small>
                            <span>{{ $cuti->status == 'Ditolak' ? 'Ditolak Dekan' : 'Disetujui Dekan' }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
