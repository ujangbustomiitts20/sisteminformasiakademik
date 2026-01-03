@extends('layouts.app')

@section('title', 'Evaluasi Dosen')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Evaluasi Dosen (EDOM)</h1>
            <p class="text-muted mb-0">Berikan penilaian terhadap dosen pengampu mata kuliah Anda</p>
        </div>
        <a href="{{ route('mahasiswa.edom.riwayat') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Riwayat
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!$periodeAktif)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Tidak Ada Periode EDOM Aktif</h5>
                <p class="text-muted mb-0">Saat ini tidak ada periode evaluasi dosen yang sedang berlangsung.</p>
            </div>
        </div>
    @else
        <!-- Info Periode -->
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="bi bi-info-circle me-3 fs-4"></i>
            <div>
                <strong>{{ $periodeAktif->nama }}</strong><br>
                <small>Periode pengisian: {{ $periodeAktif->tanggal_mulai->format('d M Y') }} - {{ $periodeAktif->tanggal_selesai->format('d M Y') }}</small>
            </div>
        </div>

        @if($periodeAktif->deskripsi)
            <div class="alert alert-light">
                <i class="bi bi-megaphone me-2"></i>{{ $periodeAktif->deskripsi }}
            </div>
        @endif

        <!-- Daftar Mata Kuliah -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-journal-check me-2"></i>Daftar Mata Kuliah</h5>
            </div>
            <div class="card-body">
                @if($mataKuliahList->isEmpty())
                    <p class="text-center text-muted py-4">Tidak ada mata kuliah yang dapat dievaluasi.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>SKS</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mataKuliahList as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <strong>{{ $item['jadwal_kuliah']->mataKuliah->nama ?? '-' }}</strong><br>
                                    <small class="text-muted">{{ $item['jadwal_kuliah']->mataKuliah->kode ?? '' }}</small>
                                </td>
                                <td>{{ $item['jadwal_kuliah']->dosen->nama ?? '-' }}</td>
                                <td>{{ $item['jadwal_kuliah']->mataKuliah->sks ?? '-' }}</td>
                                <td>
                                    @if($item['sudah_diisi'])
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sudah Diisi</span>
                                    @else
                                        <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Belum Diisi</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item['sudah_diisi'])
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="bi bi-check-lg"></i> Selesai
                                        </button>
                                    @else
                                        <a href="{{ route('mahasiswa.edom.create', $item['jadwal_kuliah']->hashid) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil-square me-1"></i>Isi Evaluasi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <!-- Progress -->
        @php
            $total = $mataKuliahList->count();
            $selesai = $mataKuliahList->where('sudah_diisi', true)->count();
            $persen = $total > 0 ? ($selesai / $total) * 100 : 0;
        @endphp
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Progress Pengisian</span>
                    <span>{{ $selesai }}/{{ $total }} mata kuliah ({{ number_format($persen, 0) }}%)</span>
                </div>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $persen }}%" aria-valuenow="{{ $persen }}" 
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
