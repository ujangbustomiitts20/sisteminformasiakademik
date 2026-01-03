@extends('layouts.app')

@section('title', 'Mata Kuliah Prodi')

@section('content')
<div class="page-title">
    <h4>Mata Kuliah Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Mata Kuliah</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Daftar Mata Kuliah - {{ $prodi->nama }}</h6>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="semester" class="form-select">
                    <option value="">Semua Semester</option>
                    @for($i = 1; $i <= 8; $i++)
                    <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari kode/nama MK..." value="{{ request('search') }}">
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
                        <th>Kode</th>
                        <th>Nama Mata Kuliah</th>
                        <th class="text-center">SKS</th>
                        <th class="text-center">Semester</th>
                        <th>Jenis</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mataKuliahs as $mk)
                    <tr>
                        <td><strong>{{ $mk->kode }}</strong></td>
                        <td>{{ $mk->nama }}</td>
                        <td class="text-center">{{ $mk->sks }}</td>
                        <td class="text-center">{{ $mk->semester ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $mk->jenis == 'Wajib' ? 'primary' : 'secondary' }}">
                                {{ $mk->jenis ?? 'Wajib' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('kaprodi.mata-kuliah.show', $mk) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data mata kuliah</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $mataKuliahs->links() }}
    </div>
</div>
@endsection
