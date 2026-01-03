@extends('layouts.app')

@section('title', 'Tugas Akhir')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manajemen Tugas Akhir / Skripsi</h1>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3>{{ $tugasAkhirs->where('status', 'draft')->count() }}</h3>
                    <small>Draft</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h3>{{ $tugasAkhirs->where('status', 'diajukan')->count() }}</h3>
                    <small>Menunggu Approval</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ $tugasAkhirs->whereIn('status', ['judul_disetujui', 'penelitian', 'penulisan'])->count() }}</h3>
                    <small>Dalam Proses</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ $tugasAkhirs->whereIn('status', ['sidang_diajukan', 'sidang_dijadwalkan'])->count() }}</h3>
                    <small>Sidang</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $tugasAkhirs->whereIn('status', ['lulus', 'selesai'])->count() }}</h3>
                    <small>Lulus</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\TugasAkhir::getStatusOptions() as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="NIM, Nama, Judul..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('admin.tugas-akhir.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="mb-3">
        <a href="{{ route('admin.tugas-akhir.seminar.index') }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-calendar-event"></i> Seminar Proposal
        </a>
        <a href="{{ route('admin.tugas-akhir.sidang.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-mortarboard"></i> Sidang TA
        </a>
    </div>

    <!-- Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Mahasiswa</th>
                            <th>Judul</th>
                            <th>Pembimbing 1</th>
                            <th>Status</th>
                            <th>Tgl Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tugasAkhirs as $index => $ta)
                        <tr>
                            <td>{{ $tugasAkhirs->firstItem() + $index }}</td>
                            <td>{{ $ta->mahasiswa->nim }}</td>
                            <td>
                                {{ $ta->mahasiswa->nama }}<br>
                                <small class="text-muted">{{ $ta->mahasiswa->programStudi->nama ?? '-' }}</small>
                            </td>
                            <td>
                                {{ Str::limit($ta->judul, 50) }}<br>
                                <small class="text-muted">{{ $ta->bidang_ilmu }}</small>
                            </td>
                            <td>{{ $ta->pembimbing1->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($ta->status) {
                                        'draft' => 'secondary',
                                        'diajukan' => 'warning',
                                        'judul_disetujui' => 'info',
                                        'judul_ditolak' => 'danger',
                                        'proposal_diajukan', 'proposal_revisi' => 'primary',
                                        'penelitian', 'penulisan' => 'info',
                                        'sidang_diajukan', 'sidang_dijadwalkan' => 'primary',
                                        'lulus', 'lulus_revisi', 'selesai' => 'success',
                                        'tidak_lulus' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $ta->getStatusLabel() }}</span>
                            </td>
                            <td>{{ $ta->tanggal_pengajuan ? \Carbon\Carbon::parse($ta->tanggal_pengajuan)->format('d/m/Y') : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.tugas-akhir.show', $ta->hashid) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Tidak ada data tugas akhir.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $tugasAkhirs->links() }}
        </div>
    </div>
</div>
@endsection
