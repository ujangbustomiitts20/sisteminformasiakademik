@extends('layouts.app')

@section('title', 'Detail Jadwal Pengganti')

@section('content')
<div class="page-title">
    <h4>Detail Jadwal Pengganti</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jadwal-pengganti.index') }}">Jadwal Pengganti</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Informasi Jadwal</h5>
                {!! $jadwalPengganti->status_badge !!}
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Mata Kuliah</h6>
                        <p class="fs-5 mb-1">{{ $jadwalPengganti->jadwalKuliah->mataKuliah->nama ?? '-' }}</p>
                        <p class="text-muted">{{ $jadwalPengganti->jadwalKuliah->mataKuliah->kode ?? '' }} 
                            ({{ $jadwalPengganti->jadwalKuliah->mataKuliah->sks ?? 0 }} SKS)</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Dosen Pengampu</h6>
                        <p class="fs-5 mb-1">{{ $jadwalPengganti->jadwalKuliah->dosen->nama ?? '-' }}</p>
                        <p class="text-muted">{{ $jadwalPengganti->jadwalKuliah->dosen->nidn ?? '' }}</p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-danger">
                                    <i class="bi bi-calendar-x me-1"></i>Jadwal Asli (Dibatalkan)
                                </h6>
                                <p class="mb-1"><strong>Tanggal:</strong> {{ $jadwalPengganti->tanggal_asli->format('d F Y') }}</p>
                                <p class="mb-1"><strong>Hari:</strong> {{ $jadwalPengganti->jadwalKuliah->hari }}</p>
                                <p class="mb-0"><strong>Jam:</strong> {{ substr($jadwalPengganti->jadwalKuliah->jam_mulai, 0, 5) }} - {{ substr($jadwalPengganti->jadwalKuliah->jam_selesai, 0, 5) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success bg-opacity-10 mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="bi bi-calendar-check me-1"></i>Jadwal Pengganti
                                </h6>
                                <p class="mb-1"><strong>Tanggal:</strong> {{ $jadwalPengganti->tanggal_pengganti->format('d F Y') }}</p>
                                <p class="mb-1"><strong>Hari:</strong> {{ $jadwalPengganti->tanggal_pengganti->translatedFormat('l') }}</p>
                                <p class="mb-0"><strong>Jam:</strong> {{ substr($jadwalPengganti->jam_mulai, 0, 5) }} - {{ substr($jadwalPengganti->jam_selesai, 0, 5) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Ruangan</h6>
                        <p>{{ $jadwalPengganti->ruangan->nama ?? '-' }} 
                            @if($jadwalPengganti->ruangan)
                            ({{ $jadwalPengganti->ruangan->gedung }}, Lantai {{ $jadwalPengganti->ruangan->lantai }})
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Alasan Penggantian</h6>
                        <p><span class="badge bg-secondary">{{ $jadwalPengganti->alasan }}</span></p>
                    </div>
                </div>

                @if($jadwalPengganti->keterangan)
                <div class="mb-3">
                    <h6 class="text-muted">Keterangan</h6>
                    <p class="text-muted">{{ $jadwalPengganti->keterangan }}</p>
                </div>
                @endif

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Diajukan oleh</h6>
                        <p>{{ $jadwalPengganti->pengaju->name ?? '-' }}</p>
                    </div>
                    @if($jadwalPengganti->penyetuju)
                    <div class="col-md-6">
                        <h6 class="text-muted">Diproses oleh</h6>
                        <p>{{ $jadwalPengganti->penyetuju->name ?? '-' }}
                            <br><small class="text-muted">{{ $jadwalPengganti->tanggal_persetujuan?->format('d/m/Y H:i') }}</small>
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    @if($jadwalPengganti->status === 'Pending')
                    <form action="{{ route('jadwal-pengganti.approve', $jadwalPengganti) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Setujui jadwal pengganti ini?')">
                            <i class="bi bi-check-lg me-1"></i>Setujui
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-1"></i>Tolak
                    </button>
                    <a href="{{ route('jadwal-pengganti.edit', $jadwalPengganti) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    @elseif($jadwalPengganti->status === 'Disetujui')
                    <form action="{{ route('jadwal-pengganti.complete', $jadwalPengganti) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary" onclick="return confirm('Tandai selesai?')">
                            <i class="bi bi-check-all me-1"></i>Tandai Selesai
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('jadwal-pengganti.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Mahasiswa Terkait -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people me-2"></i>Mahasiswa Terkait
            </div>
            <div class="card-body p-0">
                @php
                    $mahasiswaList = $jadwalPengganti->jadwalKuliah->krs->where('status', 'Disetujui');
                @endphp
                @if($mahasiswaList->count() > 0)
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @foreach($mahasiswaList as $krs)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $krs->mahasiswa->nama ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $krs->mahasiswa->nim ?? '' }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2">Belum ada mahasiswa terdaftar</p>
                </div>
                @endif
            </div>
            <div class="card-footer text-center">
                <small class="text-muted">Total: {{ $mahasiswaList->count() }} mahasiswa</small>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('jadwal-pengganti.reject', $jadwalPengganti) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Jadwal Pengganti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_tolak" class="form-control" rows="3" required 
                                  placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
