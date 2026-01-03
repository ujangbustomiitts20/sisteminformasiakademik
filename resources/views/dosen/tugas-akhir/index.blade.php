@extends('layouts.app')

@section('title', 'Bimbingan Tugas Akhir')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Bimbingan Tugas Akhir</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Bimbingan TA</li>
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

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Bimbingan</h6>
                            <h2 class="mb-0">{{ $tugasAkhirs->count() }}</h2>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Sedang Berjalan</h6>
                            <h2 class="mb-0">{{ $tugasAkhirs->whereIn('status', ['judul_disetujui', 'proposal_diajukan', 'penelitian'])->count() }}</h2>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Sidang Dijadwalkan</h6>
                            <h2 class="mb-0">{{ $tugasAkhirs->where('status', 'sidang_dijadwalkan')->count() }}</h2>
                        </div>
                        <i class="bi bi-calendar-event fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Selesai</h6>
                            <h2 class="mb-0">{{ $tugasAkhirs->whereIn('status', ['lulus', 'selesai'])->count() }}</h2>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Mahasiswa Bimbingan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>Judul</th>
                            <th>Sebagai</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Bimbingan Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tugasAkhirs as $key => $ta)
                        @php
                            $sebagai = $ta->pembimbing_1_id == $dosen->id ? 'Pembimbing 1' : 'Pembimbing 2';
                            $lastBimbingan = $ta->bimbingan->sortByDesc('tanggal')->first();
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <strong>{{ $ta->mahasiswa->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $ta->mahasiswa->nim ?? '-' }}</small>
                            </td>
                            <td>
                                <span title="{{ $ta->judul }}">{{ Str::limit($ta->judul, 50) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $sebagai == 'Pembimbing 1' ? 'primary' : 'secondary' }}">
                                    {{ $sebagai }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    @php
                                        $progress = $lastBimbingan?->persentase_progress ?? 0;
                                    @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $progress }}%">{{ $progress }}%</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $ta->status_badge }}">{{ $ta->getStatusLabel() }}</span>
                            </td>
                            <td>
                                @if($lastBimbingan)
                                {{ $lastBimbingan->tanggal->format('d M Y') }}
                                @else
                                <span class="text-muted">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('dosen.tugas-akhir.show', $ta->hashid) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    Tidak ada mahasiswa bimbingan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
