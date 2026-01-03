@extends('layouts.app')

@section('title', 'Detail Yudisium')

@section('content')
<div class="page-title">
    <h4>Detail Yudisium</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('yudisium.index') }}">Yudisium</a></li>
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

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-4">
        <!-- Info Mahasiswa -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person me-2"></i>Data Mahasiswa
            </div>
            <div class="card-body text-center">
                @if($yudisium->mahasiswa->foto)
                <img src="{{ asset('storage/' . $yudisium->mahasiswa->foto) }}" class="rounded-circle mb-3" width="100" height="100" alt="Foto">
                @else
                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                    <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                </div>
                @endif
                <h5 class="mb-1">{{ $yudisium->mahasiswa->nama }}</h5>
                <p class="text-muted mb-2">{{ $yudisium->mahasiswa->nim }}</p>
                {!! $yudisium->predikat_badge !!}
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Program Studi</span>
                    <strong>{{ $yudisium->mahasiswa->programStudi->nama ?? '-' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Fakultas</span>
                    <strong>{{ $yudisium->mahasiswa->programStudi->fakultas->nama ?? '-' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Status Mahasiswa</span>
                    <span class="badge bg-{{ $yudisium->mahasiswa->status == 'Lulus' ? 'success' : 'secondary' }}">
                        {{ $yudisium->mahasiswa->status }}
                    </span>
                </li>
            </ul>
        </div>

        <!-- Info Periode -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-mortarboard me-2"></i>Periode Wisuda
            </div>
            <div class="card-body">
                <h6>{{ $yudisium->pendaftaranWisuda->periodeWisuda->nama ?? '-' }}</h6>
                <p class="text-muted mb-2">
                    {{ $yudisium->pendaftaranWisuda->periodeWisuda->tanggal_wisuda?->format('d F Y') }}
                </p>
                <small class="text-muted">
                    No. Pendaftaran: <code>{{ $yudisium->pendaftaranWisuda->no_pendaftaran ?? '-' }}</code>
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-journal-check me-2"></i>Data Yudisium</h5>
                {!! $yudisium->status_badge !!}
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4 text-center">
                        <div class="border rounded p-3">
                            <h2 class="text-primary mb-0">{{ number_format($yudisium->ipk_akhir, 2) }}</h2>
                            <small class="text-muted">IPK Akhir</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="border rounded p-3">
                            <h2 class="text-success mb-0">{{ $yudisium->total_sks_lulus }}</h2>
                            <small class="text-muted">Total SKS</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="border rounded p-3">
                            <h2 class="text-info mb-0">{{ $yudisium->masa_studi_format }}</h2>
                            <small class="text-muted">Masa Studi</small>
                        </div>
                    </div>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="200">No. Yudisium</td>
                        <td><code>{{ $yudisium->no_yudisium }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Yudisium</td>
                        <td>{{ $yudisium->tanggal_yudisium->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Masuk</td>
                        <td>{{ $yudisium->tanggal_masuk->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Lulus</td>
                        <td><strong>{{ $yudisium->tanggal_lulus->format('d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Predikat</td>
                        <td>{!! $yudisium->predikat_badge !!}</td>
                    </tr>
                    @if($yudisium->no_ijazah)
                    <tr>
                        <td class="text-muted">No. Ijazah</td>
                        <td><code>{{ $yudisium->no_ijazah }}</code></td>
                    </tr>
                    @endif
                    @if($yudisium->no_transkrip)
                    <tr>
                        <td class="text-muted">No. Transkrip</td>
                        <td><code>{{ $yudisium->no_transkrip }}</code></td>
                    </tr>
                    @endif
                    @if($yudisium->catatan)
                    <tr>
                        <td class="text-muted">Catatan</td>
                        <td>{{ $yudisium->catatan }}</td>
                    </tr>
                    @endif
                </table>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Diproses oleh</h6>
                        <p>
                            {{ $yudisium->prosesOleh->name ?? '-' }}
                            @if($yudisium->tanggal_proses)
                            <br><small class="text-muted">{{ $yudisium->tanggal_proses->format('d/m/Y H:i') }}</small>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2 flex-wrap">
                    @if($yudisium->status === 'Pending')
                    <form action="{{ route('yudisium.approve', $yudisium) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Setujui yudisium ini? Status mahasiswa akan diubah menjadi LULUS.')">
                            <i class="bi bi-check-lg me-1"></i>Setujui
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-1"></i>Tolak
                    </button>
                    @elseif($yudisium->status === 'Disetujui')
                    <a href="{{ route('yudisium.print', $yudisium) }}" class="btn btn-primary">
                        <i class="bi bi-printer me-1"></i>Cetak Surat Keterangan
                    </a>
                    @endif
                    <a href="{{ route('yudisium.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('yudisium.reject', $yudisium) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Yudisium</h5>
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
