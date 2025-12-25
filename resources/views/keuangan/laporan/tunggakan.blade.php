@extends('layouts.app')

@section('title', 'Laporan Tunggakan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Tunggakan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('laporan.dashboard') }}">Keuangan</a></li>
                    <li class="breadcrumb-item active">Tunggakan</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('laporan.tunggakan.excel', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('laporan.tunggakan.pdf', request()->all()) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Mahasiswa Menunggak</h6>
                            <h3 class="mb-0">{{ number_format($summary['total_mahasiswa']) }}</h3>
                        </div>
                        <i class="bi bi-people display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Tunggakan</h6>
                            <h3 class="mb-0">Rp {{ number_format($summary['total_tunggakan'], 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-exclamation-triangle display-6 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-2">
                    <select name="program_studi_id" class="form-select">
                        <option value="">Semua Prodi</option>
                        @foreach($programStudi as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
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
                    <select name="angkatan" class="form-select">
                        <option value="">Semua Angkatan</option>
                        @foreach($angkatanList as $angkatan)
                            <option value="{{ $angkatan }}" {{ request('angkatan') == $angkatan ? 'selected' : '' }}>{{ $angkatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('laporan.tunggakan') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Tagihan</th>
                            <th>Mahasiswa</th>
                            <th>Tahun Akademik</th>
                            <th>Jenis Tagihan</th>
                            <th class="text-end">Total Tagihan</th>
                            <th class="text-end">Sudah Bayar</th>
                            <th class="text-end">Tunggakan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihan as $item)
                        <tr>
                            <td><code>{{ $item->no_tagihan }}</code></td>
                            <td>
                                <strong>{{ $item->mahasiswa->nama ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $item->mahasiswa->nim ?? '-' }} - {{ $item->mahasiswa->programStudi->nama ?? '-' }}</small>
                            </td>
                            <td>{{ $item->tahunAkademik->nama ?? '-' }}</td>
                            <td>{{ $item->tarif->nama ?? '-' }}</td>
                            <td class="text-end">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}</td>
                            <td class="text-end text-danger fw-bold">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('invoice.invoice', $item) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Cetak Invoice">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Tidak ada data tunggakan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tagihan->hasPages())
        <div class="card-footer bg-white">
            {{ $tagihan->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
