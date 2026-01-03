@extends('layouts.app')

@section('title', 'Kartu Tagihan')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h3 mb-0">Kartu Tagihan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kartu Tagihan</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('mahasiswa.tagihan.download') }}" class="btn btn-primary">
        <i class="bi bi-download me-1"></i>Download PDF
    </a>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($mahasiswa)
<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('mahasiswa.tagihan') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select">
                    <option value="">Semua Periode</option>
                    @foreach($tahunAkademiks as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                        {{ $ta->tahun }}/{{ $ta->tahun + 1 }} - {{ $ta->semester }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Belum Lunas" {{ request('status') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="Belum Bayar" {{ request('status') == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis Tagihan</label>
                <select name="jenis" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisTagihans as $jenis)
                    <option value="{{ $jenis }}" {{ request('jenis') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('mahasiswa.tagihan') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Info Mahasiswa -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="40%">NIM</td>
                        <td><strong>{{ $mahasiswa->nim }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $mahasiswa->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fakultas</td>
                        <td>{{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Ringkasan -->
    <div class="col-lg-8 mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Tagihan</h6>
                                <h4 class="mb-0">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h4>
                            </div>
                            <i class="bi bi-receipt fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Sudah Dibayar</h6>
                                <h4 class="mb-0">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h4>
                            </div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-{{ $sisaTagihan > 0 ? 'danger' : 'secondary' }} text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Sisa Tagihan</h6>
                                <h4 class="mb-0">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</h4>
                            </div>
                            <i class="bi bi-exclamation-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Tagihan -->
<div class="card shadow">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Tagihan</h6>
    </div>
    <div class="card-body p-0">
        @if($tagihan->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox display-4 text-muted"></i>
            <p class="text-muted mt-2">Belum ada tagihan</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No. Tagihan</th>
                        <th>Periode</th>
                        <th>Jenis</th>
                        <th class="text-end">Nominal</th>
                        <th class="text-end">Dibayar</th>
                        <th class="text-end">Sisa</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihan as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $item->no_tagihan ?? '-' }}</code></td>
                        <td>
                            @if($item->tahunAkademik)
                            {{ $item->tahunAkademik->tahun }}/{{ $item->tahunAkademik->tahun + 1 }} - {{ $item->tahunAkademik->semester }}
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $item->jenis_tagihan ?? 'SPP' }}</td>
                        <td class="text-end">Rp {{ number_format($item->total_bayar ?? $item->nominal, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($item->jumlah_dibayar ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end text-{{ ($item->total_bayar - ($item->jumlah_dibayar ?? 0)) > 0 ? 'danger' : 'success' }}">
                            Rp {{ number_format(max(0, ($item->total_bayar ?? $item->nominal) - ($item->jumlah_dibayar ?? 0)), 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($item->status == 'Lunas')
                            <span class="badge bg-success">Lunas</span>
                            @elseif($item->status == 'Belum Lunas' || $item->status == 'Belum Bayar')
                            <span class="badge bg-warning">Belum Lunas</span>
                            @else
                            <span class="badge bg-secondary">{{ $item->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Total:</th>
                        <th class="text-end">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</th>
                        <th class="text-end text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</th>
                        <th class="text-end text-{{ $sisaTagihan > 0 ? 'danger' : 'success' }}">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>Data per {{ now()->format('d F Y H:i') }}
            </small>
            <a href="{{ route('mahasiswa.tagihan.download') }}" class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Download PDF
            </a>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data mahasiswa tidak ditemukan. Silakan hubungi admin.
</div>
@endif
@endsection
