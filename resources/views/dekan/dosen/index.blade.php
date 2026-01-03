@extends('layouts.app')

@section('title', 'Dosen Fakultas')

@section('content')
<div class="page-title">
    <h4>Dosen Fakultas</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Dosen</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">{{ $fakultas->nama }}</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="prodi" class="form-select">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NIDN atau Nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIDN</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Jabatan Fungsional</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosens as $dosen)
                    <tr>
                        <td>{{ $dosen->nidn ?? '-' }}</td>
                        <td>{{ $dosen->nama }}</td>
                        <td>{{ $dosen->programStudi->nama ?? '-' }}</td>
                        <td>{{ $dosen->jabatan_fungsional ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $dosen->status == 'Aktif' ? 'success' : 'secondary' }}">
                                {{ $dosen->status ?? 'Aktif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('dekan.dosen.show', $dosen) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Tidak ada data dosen
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $dosens->links() }}
    </div>
</div>
@endsection
