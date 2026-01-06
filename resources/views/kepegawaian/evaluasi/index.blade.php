@extends('layouts.app')

@section('title', 'Evaluasi Kinerja')

@section('content')
<div class="page-title">
    <h4>Evaluasi Kinerja Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Evaluasi Kinerja</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Evaluasi</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-clipboard-data fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Sangat Baik</h6>
                        <h3 class="mb-0">{{ $stats['sangat_baik'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-star-fill fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Baik</h6>
                        <h3 class="mb-0">{{ $stats['baik'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-star-half fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Periode Aktif</h6>
                        <h3 class="mb-0">{{ $stats['periode_aktif'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions & Filters -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <a href="{{ route('kepegawaian.evaluasi.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Buat Evaluasi Baru
                </a>
                <a href="{{ route('kepegawaian.evaluasi.periode.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-calendar3 me-1"></i> Kelola Periode
                </a>
                <a href="{{ route('kepegawaian.evaluasi.kriteria.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-list-check me-1"></i> Kelola Kriteria
                </a>
            </div>
            <div class="col-md-6">
                <form action="{{ route('kepegawaian.evaluasi.index') }}" method="GET" class="d-flex gap-2 justify-content-end">
                    <select name="periode_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Periode</option>
                        @foreach($periodeList as $periode)
                            <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama }} ({{ $periode->tahun }})
                            </option>
                        @endforeach
                    </select>
                    <select name="predikat" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Predikat</option>
                        <option value="sangat_baik" {{ request('predikat') == 'sangat_baik' ? 'selected' : '' }}>Sangat Baik</option>
                        <option value="baik" {{ request('predikat') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="cukup" {{ request('predikat') == 'cukup' ? 'selected' : '' }}>Cukup</option>
                        <option value="kurang" {{ request('predikat') == 'kurang' ? 'selected' : '' }}>Kurang</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Periode</th>
                        <th>Nilai Akhir</th>
                        <th>Predikat</th>
                        <th>Penilai</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluasiList as $i => $evaluasi)
                        <tr>
                            <td>{{ $evaluasiList->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $evaluasi->nama_pegawai }}</strong><br>
                                <small class="text-muted">{{ $evaluasi->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                            </td>
                            <td>
                                {{ $evaluasi->periode->nama ?? '-' }}<br>
                                <small class="text-muted">{{ $evaluasi->periode->tahun ?? '' }}</small>
                            </td>
                            <td>
                                <span class="fw-bold fs-5">{{ number_format($evaluasi->nilai_akhir, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $evaluasi->predikat_color }}">{{ $evaluasi->predikat_label }}</span>
                            </td>
                            <td>{{ $evaluasi->penilai->name ?? '-' }}</td>
                            <td>{{ $evaluasi->tanggal_evaluasi->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('kepegawaian.evaluasi.show', $evaluasi) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('kepegawaian.evaluasi.edit', $evaluasi) }}" class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('kepegawaian.evaluasi.destroy', $evaluasi) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Hapus" onclick="return confirm('Yakin hapus data ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada data evaluasi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $evaluasiList->links() }}
    </div>
</div>
@endsection
