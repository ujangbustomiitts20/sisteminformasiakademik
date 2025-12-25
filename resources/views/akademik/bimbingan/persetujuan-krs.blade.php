@extends('layouts.app')

@section('title', 'Persetujuan KRS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Persetujuan KRS Mahasiswa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bimbingan.dosen') }}">Bimbingan</a></li>
                <li class="breadcrumb-item active">Persetujuan KRS</li>
            </ol>
        </nav>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Menunggu Persetujuan</small>
                        <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                    </div>
                    <i class="bi bi-hourglass-split" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Disetujui</small>
                        <h4 class="mb-0">{{ $stats['disetujui'] }}</h4>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small>Ditolak</small>
                        <h4 class="mb-0">{{ $stats['ditolak'] }}</h4>
                    </div>
                    <i class="bi bi-x-circle" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-card-checklist me-2"></i>Daftar Pengajuan KRS</span>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="Revisi" {{ request('status') == 'Revisi' ? 'selected' : '' }}>Revisi</option>
            </select>
            <label class="d-flex align-items-center gap-1">
                <input type="checkbox" name="all" value="1" {{ request('all') ? 'checked' : '' }} onchange="this.form.submit()">
                <small>Semua Periode</small>
            </label>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Tahun Akademik</th>
                        <th class="text-center">Total SKS</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($persetujuans as $persetujuan)
                    <tr>
                        <td>
                            <strong>{{ $persetujuan->mahasiswa->nama }}</strong>
                            <br><small class="text-muted">{{ $persetujuan->mahasiswa->nim }} - Semester {{ $persetujuan->mahasiswa->semester_aktif }}</small>
                        </td>
                        <td>{{ $persetujuan->tahunAkademik->nama_lengkap ?? '-' }}</td>
                        <td class="text-center"><span class="badge bg-secondary">{{ $persetujuan->total_sks }} SKS</span></td>
                        <td>{{ $persetujuan->tanggal_pengajuan ? $persetujuan->tanggal_pengajuan->format('d/m/Y H:i') : '-' }}</td>
                        <td><span class="badge bg-{{ $persetujuan->status_badge }}">{{ $persetujuan->status }}</span></td>
                        <td>
                            <a href="{{ route('bimbingan.detail-krs', $persetujuan) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Tidak ada pengajuan KRS</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($persetujuans->hasPages())
    <div class="card-footer">
        {{ $persetujuans->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
