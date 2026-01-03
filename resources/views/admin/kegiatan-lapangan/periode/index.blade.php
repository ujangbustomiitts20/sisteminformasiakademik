@extends('layouts.app')

@section('title', 'Periode Kegiatan Lapangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Periode Kegiatan Lapangan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Periode Kegiatan</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.kegiatan-lapangan.periode.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Periode
        </a>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kegiatan-lapangan.jenis.index') }}">Jenis Kegiatan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kegiatan-lapangan.mitra.index') }}">Mitra Kegiatan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.kegiatan-lapangan.periode.index') }}">Periode</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.kegiatan-lapangan.pendaftaran.index') }}">Pendaftaran</a>
        </li>
    </ul>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Periode</th>
                            <th>Jenis Kegiatan</th>
                            <th>Tahun Akademik</th>
                            <th>Tanggal Pendaftaran</th>
                            <th>Tanggal Kegiatan</th>
                            <th>Kuota</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodes as $key => $periode)
                        <tr>
                            <td>{{ $periodes->firstItem() + $key }}</td>
                            <td>{{ $periode->nama }}</td>
                            <td>{{ $periode->jenisKegiatan->nama ?? '-' }}</td>
                            <td>{{ $periode->tahunAkademik->nama ?? '-' }}</td>
                            <td>
                                {{ $periode->tanggal_mulai_daftar?->format('d/m/Y') ?? '-' }} - 
                                {{ $periode->tanggal_selesai_daftar?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>
                                {{ $periode->tanggal_mulai_kegiatan?->format('d/m/Y') ?? '-' }} - 
                                {{ $periode->tanggal_selesai_kegiatan?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>{{ $periode->kuota ?? '-' }}</td>
                            <td>
                                @php
                                    $statusBadge = match($periode->status) {
                                        'draft' => 'secondary',
                                        'dibuka' => 'success',
                                        'ditutup' => 'warning',
                                        'selesai' => 'primary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusBadge }}">
                                    {{ ucfirst($periode->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.kegiatan-lapangan.periode.edit', $periode->hashid) }}" 
                                        class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.kegiatan-lapangan.periode.destroy', $periode->hashid) }}" 
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus periode ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                Belum ada data periode kegiatan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $periodes->links() }}
        </div>
    </div>
</div>
@endsection
