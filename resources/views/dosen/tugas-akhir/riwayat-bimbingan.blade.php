@extends('layouts.app')

@section('title', 'Riwayat Bimbingan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Riwayat Bimbingan TA</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dosen.tugas-akhir.index') }}">Bimbingan TA</a></li>
                    <li class="breadcrumb-item active">Riwayat</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('dosen.tugas-akhir.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Bimbingan</h6>
                            <h3 class="mb-0">{{ $riwayat->total() }}</h3>
                        </div>
                        <i class="bi bi-journal-text display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Selesai</h6>
                            <h3 class="mb-0">{{ $riwayat->where('status', 'selesai')->count() }}</h3>
                        </div>
                        <i class="bi bi-check-circle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Dijadwalkan</h6>
                            <h3 class="mb-0">{{ $riwayat->where('status', 'dijadwalkan')->count() }}</h3>
                        </div>
                        <i class="bi bi-calendar-event display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Dibatalkan</h6>
                            <h3 class="mb-0">{{ $riwayat->where('status', 'dibatalkan')->count() }}</h3>
                        </div>
                        <i class="bi bi-x-circle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Semua Riwayat Bimbingan</h5>
        </div>
        <div class="card-body">
            @if($riwayat->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">Belum ada riwayat bimbingan</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Mahasiswa</th>
                            <th>Judul TA</th>
                            <th>Materi</th>
                            <th>Hasil</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $key => $bimbingan)
                        <tr>
                            <td>{{ $riwayat->firstItem() + $key }}</td>
                            <td>
                                <strong>{{ $bimbingan->tanggal->format('d M Y') }}</strong>
                                @if($bimbingan->waktu_mulai)
                                <br><small class="text-muted">{{ $bimbingan->waktu_mulai }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $bimbingan->tugasAkhir->mahasiswa->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $bimbingan->tugasAkhir->mahasiswa->nim ?? '-' }}</small>
                            </td>
                            <td>
                                <span title="{{ $bimbingan->tugasAkhir->judul ?? '-' }}">
                                    {{ Str::limit($bimbingan->tugasAkhir->judul ?? '-', 30) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($bimbingan->materi_bimbingan ?? '-', 30) }}</td>
                            <td>{{ Str::limit($bimbingan->hasil_bimbingan ?? '-', 30) }}</td>
                            <td>
                                @if($bimbingan->persentase_progress)
                                <div class="progress" style="height: 20px; min-width: 80px;">
                                    <div class="progress-bar bg-{{ $bimbingan->persentase_progress >= 80 ? 'success' : ($bimbingan->persentase_progress >= 50 ? 'warning' : 'info') }}" 
                                         style="width: {{ $bimbingan->persentase_progress }}%">
                                        {{ $bimbingan->persentase_progress }}%
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $bimbingan->status == 'selesai' ? 'success' : ($bimbingan->status == 'dibatalkan' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($bimbingan->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('dosen.tugas-akhir.show', $bimbingan->tugasAkhir->hashid) }}" 
                                   class="btn btn-sm btn-outline-primary" title="Lihat Detail TA">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $riwayat->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
