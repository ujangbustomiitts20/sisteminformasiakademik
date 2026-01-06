@extends('layouts.app')

@section('title', 'Approval Kepegawaian')

@section('content')
<div class="page-title">
    <h4>Approval Kepegawaian</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Approval Kepegawaian</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-door-open fs-1 mb-2"></i>
                <h6>Izin Keluar Pending</h6>
                <h2>{{ $stats['pending_izin_keluar'] ?? 0 }}</h2>
                <a href="{{ route('kaprodi.approval.izin-keluar.index', ['status' => 'pending']) }}" class="btn btn-light btn-sm mt-2">
                    <i class="bi bi-eye me-1"></i> Lihat
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-clock-history fs-1 mb-2"></i>
                <h6>Lembur Pending</h6>
                <h2>{{ $stats['pending_lembur'] ?? 0 }}</h2>
                <a href="{{ route('kaprodi.approval.lembur.index', ['status' => 'pending']) }}" class="btn btn-light btn-sm mt-2">
                    <i class="bi bi-eye me-1"></i> Lihat
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-hourglass-split fs-1 mb-2"></i>
                <h6>Total Pending</h6>
                <h2>{{ $stats['total_pending'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Approval</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-0">
            <h6><i class="bi bi-diagram-3 me-2"></i>Alur Approval</h6>
            <ol class="mb-0 mt-2">
                <li><strong>Dosen mengajukan</strong> izin keluar / lembur</li>
                <li><strong>Kaprodi mereview</strong> dan menyetujui atau menolak</li>
                <li>Jika disetujui Kaprodi, pengajuan <strong>diteruskan ke Admin</strong> untuk approval final</li>
                <li>Admin melakukan <strong>approval final</strong> (termasuk penentuan tarif lembur)</li>
            </ol>
        </div>
        
        <div class="mt-3">
            <p class="text-muted mb-0">
                <i class="bi bi-person-badge me-1"></i>
                Anda login sebagai Kaprodi: <strong>{{ $kaprodi->nama ?? '-' }}</strong>
                ({{ $kaprodi->programStudi->nama ?? '-' }})
            </p>
        </div>
    </div>
</div>
@endsection
