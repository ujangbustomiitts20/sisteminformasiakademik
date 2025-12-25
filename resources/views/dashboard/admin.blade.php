@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-title">
    <h4>Dashboard</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Overview</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-primary position-relative">
            <i class="bi bi-people-fill stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ number_format($totalMahasiswa) }}</div>
            <div class="stat-label opacity-75">Total Mahasiswa Aktif</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-success position-relative">
            <i class="bi bi-person-workspace stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ number_format($totalDosen) }}</div>
            <div class="stat-label opacity-75">Total Dosen Aktif</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-warning position-relative">
            <i class="bi bi-book-fill stat-icon"></i>
            <div class="stat-value h3 mb-1">{{ number_format($totalMataKuliah) }}</div>
            <div class="stat-label opacity-75">Total Mata Kuliah</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-info position-relative">
            <i class="bi bi-cash-stack stat-icon"></i>
            <div class="stat-value h3 mb-1">Rp {{ number_format($totalPembayaranBulanIni, 0, ',', '.') }}</div>
            <div class="stat-label opacity-75">Pembayaran Bulan Ini</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Tahun Akademik Aktif -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-calendar3 me-2"></i>Tahun Akademik Aktif
            </div>
            <div class="card-body text-center py-4">
                @if($tahunAkademikAktif)
                <h3 class="text-primary mb-2">{{ $tahunAkademikAktif->tahun }}</h3>
                <span class="badge bg-primary fs-6">Semester {{ $tahunAkademikAktif->semester }}</span>
                <div class="mt-3 text-muted small">
                    <i class="bi bi-calendar-event me-1"></i>
                    {{ $tahunAkademikAktif->tanggal_mulai->format('d M Y') }} - {{ $tahunAkademikAktif->tanggal_selesai->format('d M Y') }}
                </div>
                @if($tahunAkademikAktif->isPeriodeKrs())
                <div class="alert alert-success mt-3 mb-0 py-2">
                    <i class="bi bi-check-circle me-1"></i>Periode KRS dibuka
                </div>
                @endif
                @else
                <p class="text-muted mb-0">Belum ada tahun akademik aktif</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Mahasiswa per Program Studi -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart me-2"></i>Mahasiswa per Program Studi</span>
            </div>
            <div class="card-body">
                @if($mahasiswaPerProdi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Program Studi</th>
                                <th class="text-end">Jumlah</th>
                                <th style="width: 40%">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $maxTotal = $mahasiswaPerProdi->max('total'); @endphp
                            @foreach($mahasiswaPerProdi as $data)
                            <tr>
                                <td>{{ $data->programStudi->nama ?? 'N/A' }}</td>
                                <td class="text-end fw-semibold">{{ number_format($data->total) }}</td>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: {{ ($data->total / $maxTotal) * 100 }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0 py-4">Belum ada data mahasiswa</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Pengumuman Terbaru -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-megaphone me-2"></i>Pengumuman Terbaru</span>
                <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($pengumuman->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($pengumuman as $p)
                    <a href="{{ route('pengumuman.show', $p) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $p->judul }}</h6>
                                <p class="text-muted small mb-0">{{ Str::limit(strip_tags($p->isi), 100) }}</p>
                            </div>
                            <span class="badge bg-{{ $p->kategori == 'Akademik' ? 'primary' : ($p->kategori == 'Keuangan' ? 'success' : 'secondary') }}">
                                {{ $p->kategori }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada pengumuman</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
