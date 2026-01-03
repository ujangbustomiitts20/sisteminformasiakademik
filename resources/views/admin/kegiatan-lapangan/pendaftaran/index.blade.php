@extends('layouts.app')

@section('title', 'Pendaftaran Kegiatan Lapangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pendaftaran PKL/Magang/KKN</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pendaftaran</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.kegiatan-lapangan.pendaftaran.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\PendaftaranKegiatanLapangan::getStatusOptions() as $key => $val)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Kegiatan</label>
                    <select name="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisKegiatan as $jenis)
                        <option value="{{ $jenis->id }}" {{ request('jenis') == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="NIM / Nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.kegiatan-lapangan.pendaftaran.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Pendaftaran</th>
                            <th>Mahasiswa</th>
                            <th>Jenis</th>
                            <th>Periode</th>
                            <th>Mitra Diterima</th>
                            <th>Pembimbing</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftarans as $key => $pendaftaran)
                        <tr>
                            <td>{{ $pendaftarans->firstItem() + $key }}</td>
                            <td><code>{{ $pendaftaran->nomor_pendaftaran }}</code></td>
                            <td>
                                <strong>{{ $pendaftaran->mahasiswa->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $pendaftaran->mahasiswa->nim ?? '-' }}</small>
                            </td>
                            <td>{{ $pendaftaran->periode->jenisKegiatan->nama ?? '-' }}</td>
                            <td>{{ $pendaftaran->periode->nama ?? '-' }}</td>
                            <td>{{ $pendaftaran->mitraDiterima->nama ?? '-' }}</td>
                            <td>{{ $pendaftaran->dosenPembimbing->nama ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $pendaftaran->status_badge }}">
                                    {{ $pendaftaran->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.kegiatan-lapangan.pendaftaran.show', $pendaftaran->hashid) }}" 
                                        class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    Belum ada data pendaftaran
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $pendaftarans->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
