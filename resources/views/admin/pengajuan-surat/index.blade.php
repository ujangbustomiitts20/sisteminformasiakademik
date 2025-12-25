@extends('layouts.app')

@section('title', 'Kelola Pengajuan Surat')

@section('content')
<div class="page-title">
    <h4>Kelola Pengajuan Surat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pengajuan Surat</li>
        </ol>
    </nav>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Menunggu</h6>
                        <h4 class="mb-0">{{ $pengajuanSurat->where('status', 'pending')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="bi bi-clock-history text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Diproses</h6>
                        <h4 class="mb-0">{{ $pengajuanSurat->where('status', 'diproses')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Disetujui</h6>
                        <h4 class="mb-0">{{ $pengajuanSurat->where('status', 'disetujui')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Ditolak</h6>
                        <h4 class="mb-0">{{ $pengajuanSurat->where('status', 'ditolak')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Daftar Pengajuan Surat</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="12%">NIM</th>
                        <th width="15%">Nama Mahasiswa</th>
                        <th width="15%">Jenis Surat</th>
                        <th width="18%">Keperluan</th>
                        <th width="12%">Tanggal</th>
                        <th width="10%">Status</th>
                        <th width="14%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuanSurat as $item)
                    <tr>
                        <td>{{ $pengajuanSurat->firstItem() + $loop->index }}</td>
                        <td><code>{{ $item->mahasiswa->nim }}</code></td>
                        <td>{{ $item->mahasiswa->nama }}</td>
                        <td>{{ $item->jenis_surat }}</td>
                        <td>{{ \Str::limit($item->keperluan, 40) }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status_badge }}">
                                {{ $item->status_text }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.pengajuan-surat.show', $item) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye me-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada pengajuan surat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pengajuanSurat->hasPages())
        <div class="mt-3">
            {{ $pengajuanSurat->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
