@extends('layouts.app')

@section('title', 'Kelola Tenaga Kependidikan')

@section('content')
<div class="page-title">
    <h4>Kelola Tenaga Kependidikan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Tenaga Kependidikan</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Daftar Tenaga Kependidikan</span>
        <a href="{{ route('kepegawaian.pegawai.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Pegawai
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('kepegawaian.pegawai.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIP atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="unit_kerja" class="form-select">
                        <option value="">-- Semua Unit --</option>
                        @foreach($unitKerja as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_kerja') == $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="jenis_pegawai" class="form-select">
                        <option value="">-- Semua Jenis --</option>
                        <option value="PNS" {{ request('jenis_pegawai') == 'PNS' ? 'selected' : '' }}>PNS</option>
                        <option value="PPPK" {{ request('jenis_pegawai') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        <option value="Honorer" {{ request('jenis_pegawai') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                        <option value="Kontrak" {{ request('jenis_pegawai') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                        <option value="Tetap Yayasan" {{ request('jenis_pegawai') == 'Tetap Yayasan' ? 'selected' : '' }}>Tetap Yayasan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="Pensiun" {{ request('status') == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('kepegawaian.pegawai.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th width="120">NIP</th>
                        <th>Nama Pegawai</th>
                        <th>Unit Kerja</th>
                        <th>Jabatan</th>
                        <th>Jenis</th>
                        <th width="80">Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $index => $p)
                    <tr>
                        <td>{{ $pegawai->firstItem() + $index }}</td>
                        <td><code>{{ $p->nip ?? '-' }}</code></td>
                        <td>
                            <strong>{{ $p->nama }}</strong>
                            @if($p->email)
                            <br><small class="text-muted">{{ $p->email }}</small>
                            @endif
                        </td>
                        <td>{{ $p->unitKerja->nama ?? '-' }}</td>
                        <td>{{ $p->jabatan ?? '-' }}</td>
                        <td><span class="badge bg-secondary">{{ $p->jenis_pegawai }}</span></td>
                        <td>
                            @if($p->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @elseif($p->status == 'Cuti')
                            <span class="badge bg-warning">Cuti</span>
                            @elseif($p->status == 'Pensiun')
                            <span class="badge bg-info">Pensiun</span>
                            @else
                            <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.pegawai.show', $p) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('kepegawaian.pegawai.edit', $p) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kepegawaian.pegawai.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pegawai ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data tenaga kependidikan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pegawai->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $pegawai->firstItem() }} - {{ $pegawai->lastItem() }} dari {{ $pegawai->total() }} data
            </div>
            {{ $pegawai->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
