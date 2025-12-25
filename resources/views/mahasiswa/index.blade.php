@extends('layouts.app')

@section('title', 'Mahasiswa')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Data Mahasiswa</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Mahasiswa</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Mahasiswa
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari NIM atau Nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="program_studi_id" class="form-select">
                    <option value="">-- Semua Program Studi --</option>
                    @foreach($programStudi as $ps)
                    <option value="{{ $ps->id }}" {{ request('program_studi_id') == $ps->id ? 'selected' : '' }}>{{ $ps->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="angkatan" class="form-select">
                    <option value="">-- Angkatan --</option>
                    @foreach($angkatanList as $a)
                    <option value="{{ $a }}" {{ request('angkatan') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- Status --</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="DO" {{ request('status') == 'DO' ? 'selected' : '' }}>DO</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswa as $mhs)
                    <tr>
                        <td><code>{{ $mhs->nim }}</code></td>
                        <td>
                            <strong>{{ $mhs->nama }}</strong>
                            <br><small class="text-muted">{{ $mhs->email }}</small>
                        </td>
                        <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td>{{ $mhs->semester_aktif }}</td>
                        <td>
                            <span class="badge bg-{{ $mhs->status == 'Aktif' ? 'success' : ($mhs->status == 'Cuti' ? 'warning' : ($mhs->status == 'Lulus' ? 'info' : 'danger')) }}">
                                {{ $mhs->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('mahasiswa.show', $mhs) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('mahasiswa.edit', $mhs) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('mahasiswa.destroy', $mhs) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Tidak ada data mahasiswa</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $mahasiswa->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
