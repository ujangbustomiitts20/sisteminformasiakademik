@extends('layouts.app')

@section('title', 'Manajemen Pensiun')

@section('content')
<div class="page-title">
    <h4>Manajemen Pensiun</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Pensiun</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Akan Pensiun</h6>
                        <h3 class="mb-0">{{ $stats['akan_pensiun'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-clock-history fs-1 opacity-50"></i>
                </div>
                <small class="opacity-75">1 tahun ke depan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Dalam Proses</h6>
                        <h3 class="mb-0">{{ $stats['proses'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Selesai</h6>
                        <h3 class="mb-0">{{ $stats['selesai'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-white-50">Total Data</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-person-badge fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge me-2"></i>Daftar Data Pensiun</span>
        <a href="{{ route('kepegawaian.pensiun.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.pensiun.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIP atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="jenis" class="form-select">
                        <option value="">-- Jenis --</option>
                        <option value="bup" {{ request('jenis') == 'bup' ? 'selected' : '' }}>BUP (Batas Usia)</option>
                        <option value="atas_permintaan" {{ request('jenis') == 'atas_permintaan' ? 'selected' : '' }}>Atas Permintaan</option>
                        <option value="uzur" {{ request('jenis') == 'uzur' ? 'selected' : '' }}>Uzur</option>
                        <option value="duda_janda" {{ request('jenis') == 'duda_janda' ? 'selected' : '' }}>Duda/Janda</option>
                        <option value="meninggal" {{ request('jenis') == 'meninggal' ? 'selected' : '' }}>Meninggal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Status --</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Dalam Proses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tahun" class="form-select">
                        <option value="">-- Tahun BUP --</option>
                        @for($y = date('Y'); $y <= date('Y') + 5; $y++)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('kepegawaian.pensiun.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Pegawai</th>
                        <th width="80">Usia BUP</th>
                        <th width="100">Tgl BUP</th>
                        <th>Jenis</th>
                        <th width="100">Sisa Waktu</th>
                        <th width="90">Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pensiun as $index => $p)
                    <tr>
                        <td>{{ $pensiun->firstItem() + $index }}</td>
                        <td>
                            @if($p->dosen)
                            <strong>{{ $p->dosen->nama_lengkap }}</strong>
                            <br><small class="text-muted">{{ $p->dosen->nidn ?? $p->dosen->nip }} (Dosen)</small>
                            @elseif($p->pegawai)
                            <strong>{{ $p->pegawai->nama }}</strong>
                            <br><small class="text-muted">{{ $p->pegawai->nip }} (Tendik)</small>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ $p->usia_bup }} thn</span></td>
                        <td>{{ $p->tanggal_bup ? \Carbon\Carbon::parse($p->tanggal_bup)->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge bg-info">{{ $p->jenis_pensiun_label }}</span></td>
                        <td>
                            @if($p->tanggal_bup)
                            @php
                                $tglBup = \Carbon\Carbon::parse($p->tanggal_bup);
                                $selisihHari = now()->diffInDays($tglBup, false);
                                $selisihBulan = now()->diffInMonths($tglBup, false);
                            @endphp
                            @if($selisihHari > 0)
                            <span class="{{ $selisihBulan <= 12 ? 'text-warning fw-bold' : '' }}">
                                @if($selisihBulan >= 12)
                                {{ floor($selisihBulan / 12) }} thn {{ $selisihBulan % 12 }} bln
                                @else
                                {{ $selisihBulan }} bln {{ $selisihHari % 30 }} hr
                                @endif
                            </span>
                            @else
                            <span class="text-danger fw-bold">Sudah BUP</span>
                            @endif
                            @else
                            -
                            @endif
                        </td>
                        <td>{!! $p->status_badge !!}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.pensiun.show', $p) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($p->status, ['aktif', 'proses']))
                                <a href="{{ route('kepegawaian.pensiun.edit', $p) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if($p->status == 'proses')
                                <form action="{{ route('kepegawaian.pensiun.proses', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Selesaikan proses pensiun ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Selesaikan">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-person-badge text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data pensiun</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pensiun->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $pensiun->firstItem() }} - {{ $pensiun->lastItem() }} dari {{ $pensiun->total() }} data
            </div>
            {{ $pensiun->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
