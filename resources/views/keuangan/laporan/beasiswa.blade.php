@extends('layouts.app')

@section('title', 'Laporan Beasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Beasiswa</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('laporan.dashboard') }}">Keuangan</a></li>
                    <li class="breadcrumb-item active">Beasiswa</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('laporan.beasiswa.excel', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('laporan.beasiswa.pdf', request()->all()) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50 mb-1">Total Penerima</h6>
                    <h3 class="mb-0">{{ number_format($summary['total_penerima']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50 mb-1">Disetujui</h6>
                    <h3 class="mb-0">{{ number_format($summary['total_disetujui']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Menunggu</h6>
                    <h3 class="mb-0">{{ number_format($summary['total_diajukan']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white-50 mb-1">Est. Total Nilai</h6>
                    <h3 class="mb-0">Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <select name="beasiswa_id" class="form-select">
                        <option value="">Semua Beasiswa</option>
                        @foreach($beasiswaList as $b)
                            <option value="{{ $b->id }}" {{ request('beasiswa_id') == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun_akademik_id" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunAkademik as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\PenerimaBeasiswa::STATUS as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('laporan.beasiswa') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Beasiswa</th>
                            <th>Tipe/Nilai</th>
                            <th>Tahun Akademik</th>
                            <th>Periode</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerima as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->mahasiswa->nama ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $item->mahasiswa->nim ?? '-' }} - {{ $item->mahasiswa->programStudi->nama ?? '-' }}</small>
                            </td>
                            <td>
                                <strong>{{ $item->beasiswa->nama ?? '-' }}</strong>
                                <br><span class="badge bg-{{ $item->beasiswa->jenis == 'Beasiswa' ? 'success' : ($item->beasiswa->jenis == 'Potongan' ? 'info' : 'warning') }}">{{ $item->beasiswa->jenis ?? '-' }}</span>
                            </td>
                            <td>
                                @if($item->beasiswa)
                                    @if($item->beasiswa->tipe_potongan === 'Persen')
                                        {{ $item->beasiswa->nilai_potongan }}%
                                    @else
                                        Rp {{ number_format($item->beasiswa->nilai_potongan, 0, ',', '.') }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->tahunAkademik->nama ?? '-' }}</td>
                            <td>
                                {{ $item->tanggal_mulai ? $item->tanggal_mulai->format('d/m/Y') : '-' }}
                                @if($item->tanggal_selesai)
                                    <br><small class="text-muted">s/d {{ $item->tanggal_selesai->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td class="text-center">{!! $item->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada data penerima beasiswa</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($penerima->hasPages())
        <div class="card-footer bg-white">
            {{ $penerima->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
