@extends('layouts.app')

@section('title', 'Detail Pendaftaran Wisuda')

@section('content')
<div class="page-title">
    <h4>Detail Pendaftaran Wisuda</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('wisuda.index') }}">Wisuda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('wisuda.show', $pendaftaran->periodeWisuda) }}">{{ $pendaftaran->periodeWisuda->nama }}</a></li>
            <li class="breadcrumb-item active">Detail Pendaftaran</li>
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
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Data Mahasiswa
            </div>
            <div class="card-body text-center">
                @if($pendaftaran->mahasiswa->foto)
                <img src="{{ Storage::url($pendaftaran->mahasiswa->foto) }}" class="rounded-circle mb-3" width="100" height="100" alt="Foto">
                @else
                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                    <i class="bi bi-person fs-1 text-white"></i>
                </div>
                @endif
                <h5>{{ $pendaftaran->mahasiswa->nama }}</h5>
                <p class="text-muted mb-2">{{ $pendaftaran->mahasiswa->nim }}</p>
                <span class="badge bg-primary">{{ $pendaftaran->mahasiswa->programStudi->nama ?? '-' }}</span>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Angkatan</span>
                    <strong>{{ $pendaftaran->mahasiswa->angkatan }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Email</span>
                    <span>{{ $pendaftaran->mahasiswa->email ?? '-' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">No. HP</span>
                    <span>{{ $pendaftaran->mahasiswa->no_hp ?? '-' }}</span>
                </li>
            </ul>
        </div>

        <!-- Status Pendaftaran -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard-check me-2"></i>Status</span>
                {!! $pendaftaran->status_badge !!}
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">No. Pendaftaran</td>
                        <td><code>{{ $pendaftaran->no_pendaftaran }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Daftar</td>
                        <td>{{ $pendaftaran->tanggal_daftar?->format('d M Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Verifikator</td>
                        <td>{{ $pendaftaran->verifikator->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tgl. Verifikasi</td>
                        <td>{{ $pendaftaran->tanggal_verifikasi?->format('d M Y H:i') ?? '-' }}</td>
                    </tr>
                </table>
                
                @if($pendaftaran->catatan_verifikasi)
                <hr>
                <small class="text-muted">Catatan Verifikasi:</small>
                <p class="mb-0">{{ $pendaftaran->catatan_verifikasi }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Data Akademik -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-mortarboard me-2"></i>Data Akademik
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center border-end">
                        <h3 class="text-primary mb-0">{{ number_format($pendaftaran->ipk, 2) }}</h3>
                        <small class="text-muted">IPK</small>
                    </div>
                    <div class="col-md-3 text-center border-end">
                        <h3 class="mb-0">{{ $pendaftaran->total_sks }}</h3>
                        <small class="text-muted">Total SKS</small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Judul Skripsi</small>
                        <p class="mb-0">{{ $pendaftaran->judul_skripsi ?? '-' }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Tanggal Lulus Sidang</small>
                        <p class="mb-0">{{ $pendaftaran->tanggal_lulus_sidang?->format('d F Y') ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Periode Wisuda</small>
                        <p class="mb-0">{{ $pendaftaran->periodeWisuda->nama }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelengkapan Berkas -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-folder me-2"></i>Kelengkapan Berkas
                @php $berkas = $pendaftaran->kelengkapan_berkas; @endphp
                <span class="badge bg-{{ $berkas['percent'] == 100 ? 'success' : 'warning' }} ms-2">
                    {{ $berkas['completed'] }}/{{ $berkas['total'] }} ({{ $berkas['percent'] }}%)
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 {{ $pendaftaran->foto_formal ? 'border-success' : 'border-warning' }}">
                            <div class="d-flex align-items-center">
                                <i class="bi {{ $pendaftaran->foto_formal ? 'bi-check-circle text-success' : 'bi-exclamation-circle text-warning' }} fs-4 me-3"></i>
                                <div>
                                    <strong>Foto Formal</strong>
                                    <br><small class="text-muted">{{ $pendaftaran->foto_formal ? 'Sudah diupload' : 'Belum diupload' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 {{ $pendaftaran->bukti_bebas_pustaka ? 'border-success' : 'border-warning' }}">
                            <div class="d-flex align-items-center">
                                <i class="bi {{ $pendaftaran->bukti_bebas_pustaka ? 'bi-check-circle text-success' : 'bi-exclamation-circle text-warning' }} fs-4 me-3"></i>
                                <div>
                                    <strong>Bebas Perpustakaan</strong>
                                    <br><small class="text-muted">{{ $pendaftaran->bukti_bebas_pustaka ? 'Sudah diupload' : 'Belum diupload' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 {{ $pendaftaran->bukti_bebas_keuangan ? 'border-success' : 'border-warning' }}">
                            <div class="d-flex align-items-center">
                                <i class="bi {{ $pendaftaran->bukti_bebas_keuangan ? 'bi-check-circle text-success' : 'bi-exclamation-circle text-warning' }} fs-4 me-3"></i>
                                <div>
                                    <strong>Bebas Keuangan</strong>
                                    <br><small class="text-muted">{{ $pendaftaran->bukti_bebas_keuangan ? 'Sudah diupload' : 'Belum diupload' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 {{ $pendaftaran->bukti_pembayaran_wisuda ? 'border-success' : 'border-warning' }}">
                            <div class="d-flex align-items-center">
                                <i class="bi {{ $pendaftaran->bukti_pembayaran_wisuda ? 'bi-check-circle text-success' : 'bi-exclamation-circle text-warning' }} fs-4 me-3"></i>
                                <div>
                                    <strong>Bukti Pembayaran Wisuda</strong>
                                    <br><small class="text-muted">{{ $pendaftaran->bukti_pembayaran_wisuda ? 'Sudah diupload' : 'Belum diupload' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yudisium -->
        @if($pendaftaran->yudisium)
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-award me-2"></i>Data Yudisium
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted">No. Yudisium</small>
                        <p><code>{{ $pendaftaran->yudisium->no_yudisium }}</code></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">IPK Akhir</small>
                        <p class="h4 text-primary">{{ number_format($pendaftaran->yudisium->ipk_akhir, 2) }}</p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Predikat</small>
                        <p>{!! $pendaftaran->yudisium->predikat_badge !!}</p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Status</small>
                        <p>{!! $pendaftaran->yudisium->status_badge !!}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Verifikasi Form -->
        @if(in_array($pendaftaran->status, ['Pending', 'Verifikasi Berkas']))
        <div class="card">
            <div class="card-header">
                <i class="bi bi-check2-square me-2"></i>Verifikasi Pendaftaran
            </div>
            <div class="card-body">
                <form action="{{ route('wisuda.pendaftaran.verify', $pendaftaran) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Status Verifikasi</label>
                        <select name="status" class="form-select" required>
                            <option value="">Pilih Status</option>
                            <option value="Verifikasi Berkas">Verifikasi Berkas (Sedang diproses)</option>
                            <option value="Lolos Yudisium">Lolos Yudisium</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Verifikasi</label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Catatan untuk mahasiswa (opsional)">{{ $pendaftaran->catatan_verifikasi }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Simpan Verifikasi
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
