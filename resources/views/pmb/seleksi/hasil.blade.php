@extends('layouts.app')

@section('title', 'Hasil Seleksi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Hasil Seleksi PMB</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pmb.seleksi.index') }}">Seleksi</a></li>
                    <li class="breadcrumb-item active">Hasil</li>
                </ol>
            </nav>
        </div>
        <div>
            @if(request('gelombang'))
            <a href="{{ route('pmb.seleksi.export-hasil', ['gelombang' => request('gelombang')]) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pmb.seleksi.hasil') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Gelombang PMB</label>
                        <select name="gelombang" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}" {{ request('gelombang') == $gelombang->id ? 'selected' : '' }}>
                                    {{ $gelombang->periodePmb->nama ?? '' }} - {{ $gelombang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Program Studi</label>
                        <select name="prodi" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="tidak_lulus" {{ request('status') == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                            <option value="cadangan" {{ request('status') == 'cadangan' ? 'selected' : '' }}>Cadangan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Nama / No. Pendaftaran" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request('gelombang'))
    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Peserta</h6>
                            <h3 class="mb-0">{{ $summary['total'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-people-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Lulus</h6>
                            <h3 class="mb-0">{{ $summary['lulus'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Tidak Lulus</h6>
                            <h3 class="mb-0">{{ $summary['tidak_lulus'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-x-circle-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Cadangan</h6>
                            <h3 class="mb-0">{{ $summary['cadangan'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-trophy me-2"></i>Daftar Hasil Seleksi
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Ranking</th>
                            <th>No. Pendaftaran</th>
                            <th>Nama</th>
                            <th>Prodi Pilihan</th>
                            <th>Prodi Diterima</th>
                            <th class="text-center">Nilai Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hasilSeleksi as $hasil)
                        <tr>
                            <td class="text-center">
                                @if($hasil->ranking <= 3 && $hasil->status == 'lulus')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-trophy me-1"></i>{{ $hasil->ranking }}
                                    </span>
                                @else
                                    {{ $hasil->ranking }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pmb.calon-mahasiswa.show', $hasil->calonMahasiswa->hashid) }}">
                                    {{ $hasil->calonMahasiswa->no_pendaftaran }}
                                </a>
                            </td>
                            <td>{{ $hasil->calonMahasiswa->nama_lengkap }}</td>
                            <td>
                                {{ $hasil->calonMahasiswa->programStudi->nama ?? '-' }}
                                @if($hasil->calonMahasiswa->program_studi_2_id)
                                    <br><small class="text-muted">Pilihan 2: {{ $hasil->calonMahasiswa->programStudi2->nama ?? '-' }}</small>
                                @endif
                            </td>
                            <td>
                                @if($hasil->program_studi_diterima_id)
                                    <span class="badge bg-primary">{{ $hasil->programStudiDiterima->nama ?? '-' }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <strong>{{ number_format($hasil->nilai_total, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $hasil->status_badge }} fs-6">{{ $hasil->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('pmb.calon-mahasiswa.show', $hasil->calonMahasiswa->hashid) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada hasil seleksi untuk gelombang ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($hasilSeleksi instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">Menampilkan {{ $hasilSeleksi->firstItem() ?? 0 }} - {{ $hasilSeleksi->lastItem() ?? 0 }} dari {{ $hasilSeleksi->total() }} data</small>
                </div>
                {{ $hasilSeleksi->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-funnel fs-1 text-muted mb-3"></i>
            <p class="text-muted">Silakan pilih gelombang PMB terlebih dahulu</p>
        </div>
    </div>
    @endif
</div>
@endsection
