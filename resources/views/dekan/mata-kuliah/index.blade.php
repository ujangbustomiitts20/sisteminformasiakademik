@extends('layouts.app')

@section('title', 'Mata Kuliah Fakultas')

@section('content')
<div class="page-title">
    <h4>Mata Kuliah Fakultas</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Mata Kuliah</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Daftar Mata Kuliah - {{ $fakultas->nama }}</h6>
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
            <div class="col-md-2">
                <select name="semester" class="form-select">
                    <option value="">Semua Semester</option>
                    @for($i = 1; $i <= 8; $i++)
                    <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari kode/nama mata kuliah..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <a href="{{ route('dekan.mata-kuliah.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabel -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Mata Kuliah</th>
                        <th>Program Studi</th>
                        <th class="text-center">SKS</th>
                        <th class="text-center">Semester</th>
                        <th>Jenis</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mataKuliahs as $index => $mk)
                    <tr>
                        <td>{{ $mataKuliahs->firstItem() + $index }}</td>
                        <td><code>{{ $mk->kode }}</code></td>
                        <td>{{ $mk->nama }}</td>
                        <td>{{ $mk->programStudi->nama ?? '-' }}</td>
                        <td class="text-center">{{ $mk->sks }}</td>
                        <td class="text-center">{{ $mk->semester ?? '-' }}</td>
                        <td>{{ $mk->jenis ?? '-' }}</td>
                        <td>
                            <a href="{{ route('dekan.mata-kuliah.show', $mk) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Tidak ada data mata kuliah
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $mataKuliahs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
