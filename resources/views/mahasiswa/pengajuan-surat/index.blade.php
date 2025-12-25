@extends('layouts.app')

@section('title', 'Pengajuan Surat')

@section('content')
<div class="page-title">
    <h4>Pengajuan Surat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pengajuan Surat</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Riwayat Pengajuan Surat</h5>
                <a href="{{ route('pengajuan-surat.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Ajukan Surat Baru
                </a>
            </div>
            <div class="card-body">
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
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Jenis Surat</th>
                                <th width="20%">Keperluan</th>
                                <th width="15%">Tanggal Pengajuan</th>
                                <th width="12%">Status</th>
                                <th width="15%">Nomor Surat</th>
                                <th width="13%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuanSurat as $item)
                            <tr>
                                <td>{{ $pengajuanSurat->firstItem() + $loop->index }}</td>
                                <td>{{ $item->jenis_surat }}</td>
                                <td>{{ \Str::limit($item->keperluan, 50) }}</td>
                                <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->status_badge }}">
                                        {{ $item->status_text }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->nomor_surat)
                                        <code>{{ $item->nomor_surat }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('pengajuan-surat.show', $item) }}" class="btn btn-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($item->status == 'disetujui')
                                        <a href="{{ route('pengajuan-surat.download', $item) }}" class="btn btn-success" title="Download Surat">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @endif
                                        @if($item->status == 'pending')
                                        <form action="{{ route('pengajuan-surat.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pengajuan surat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Batalkan">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada pengajuan surat</p>
                                    <a href="{{ route('pengajuan-surat.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="bi bi-plus-circle me-1"></i>Ajukan Surat
                                    </a>
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
    </div>
</div>

<!-- Info Box -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Informasi Pengajuan Surat</h6>
            <ul class="mb-0 small">
                <li>Pengajuan surat akan diproses oleh bagian akademik dalam 1-3 hari kerja</li>
                <li>Pastikan data yang Anda masukkan sudah benar</li>
                <li>Anda dapat membatalkan pengajuan jika status masih <strong>Menunggu</strong></li>
                <li>Surat yang sudah disetujui dapat didownload dalam format PDF</li>
            </ul>
        </div>
    </div>
</div>
@endsection
