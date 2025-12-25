@extends('layouts.app')

@section('title', 'Detail Pengajuan Surat')

@section('content')
<div class="page-title">
    <h4>Detail Pengajuan Surat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pengajuan-surat.index') }}">Pengajuan Surat</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Informasi Pengajuan</h5>
                <span class="badge bg-{{ $pengajuanSurat->status_badge }} fs-6">
                    {{ $pengajuanSurat->status_text }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Jenis Surat:</div>
                    <div class="col-md-8"><strong>{{ $pengajuanSurat->jenis_surat }}</strong></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Tanggal Pengajuan:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->created_at->format('d F Y, H:i') }} WIB</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Keperluan:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->keperluan }}</div>
                </div>
                
                @if($pengajuanSurat->ditujukan_kepada)
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Ditujukan Kepada:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->ditujukan_kepada }}</div>
                </div>
                @endif
                
                @if($pengajuanSurat->keterangan_tambahan)
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Keterangan Tambahan:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->keterangan_tambahan }}</div>
                </div>
                @endif
                
                <hr class="my-4">
                
                <h6 class="text-primary mb-3"><i class="bi bi-hourglass-split me-2"></i>Status Pemrosesan</h6>
                
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Status Saat Ini:</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $pengajuanSurat->status_badge }} fs-6">
                            {{ $pengajuanSurat->status_text }}
                        </span>
                    </div>
                </div>
                
                @if($pengajuanSurat->tanggal_diproses)
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Tanggal Diproses:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->tanggal_diproses->format('d F Y, H:i') }} WIB</div>
                </div>
                @endif
                
                @if($pengajuanSurat->diproses_oleh_user)
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Diproses Oleh:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->diproses_oleh_user->name }}</div>
                </div>
                @endif
                
                @if($pengajuanSurat->nomor_surat)
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Nomor Surat:</div>
                    <div class="col-md-8"><code>{{ $pengajuanSurat->nomor_surat }}</code></div>
                </div>
                @endif
                
                @if($pengajuanSurat->catatan_admin)
                <div class="alert alert-{{ $pengajuanSurat->status == 'ditolak' ? 'danger' : 'info' }} mt-3">
                    <h6 class="alert-heading"><i class="bi bi-chat-text me-2"></i>Catatan dari Admin</h6>
                    <p class="mb-0">{{ $pengajuanSurat->catatan_admin }}</p>
                </div>
                @endif
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('pengajuan-surat.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                    
                    <div>
                        @if($pengajuanSurat->status == 'disetujui')
                        <a href="{{ route('pengajuan-surat.download', $pengajuanSurat) }}" class="btn btn-success">
                            <i class="bi bi-download me-2"></i>Download Surat PDF
                        </a>
                        @endif
                        
                        @if($pengajuanSurat->status == 'pending')
                        <form action="{{ route('pengajuan-surat.destroy', $pengajuanSurat) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pengajuan surat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle me-2"></i>Batalkan Pengajuan
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Timeline Status -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Pengajuan Dibuat</h6>
                            <p class="text-muted small mb-0">{{ $pengajuanSurat->created_at->format('d F Y, H:i') }} WIB</p>
                        </div>
                    </div>
                    
                    @if($pengajuanSurat->status != 'pending')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-{{ $pengajuanSurat->status_badge }}"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">{{ $pengajuanSurat->status_text }}</h6>
                            <p class="text-muted small mb-0">
                                {{ $pengajuanSurat->tanggal_diproses ? $pengajuanSurat->tanggal_diproses->format('d F Y, H:i') . ' WIB' : '-' }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: 9px;
    top: 30px;
    width: 2px;
    height: calc(100% - 10px);
    background: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid #fff;
}

.timeline-content {
    margin-left: 15px;
}
</style>
@endsection
