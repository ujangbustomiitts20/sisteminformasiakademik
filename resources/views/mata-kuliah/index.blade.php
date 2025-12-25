@extends('layouts.app')

@section('title', 'Kelola Mata Kuliah')

@section('content')
<div class="page-title">
    <h4>Kelola Mata Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Mata Kuliah</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-book me-2"></i>Daftar Mata Kuliah</span>
        <a href="{{ route('mata-kuliah.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Mata Kuliah
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('mata-kuliah.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="program_studi" class="form-select">
                        <option value="">-- Semua Prodi --</option>
                        @foreach($programStudi as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('program_studi') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="semester" class="form-select">
                        <option value="">-- Semester --</option>
                        @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('mata-kuliah.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th width="100">Kode</th>
                        <th>Nama Mata Kuliah</th>
                        <th class="text-center" width="60">SKS</th>
                        <th class="text-center" width="80">Semester</th>
                        <th class="text-center" width="100">Pertemuan</th>
                        <th>Program Studi</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mataKuliah as $index => $mk)
                    <tr>
                        <td>{{ $mataKuliah->firstItem() + $index }}</td>
                        <td><code>{{ $mk->kode }}</code></td>
                        <td>
                            <strong>{{ $mk->nama }}</strong>
                            @if($mk->deskripsi)
                            <br><small class="text-muted">{{ Str::limit($mk->deskripsi, 50) }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $mk->sks }} SKS</span>
                        </td>
                        <td class="text-center">{{ $mk->semester }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $mk->jumlah_pertemuan ?? 16 }}x</span>
                        </td>
                        <td>{{ $mk->programStudi->nama ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('mata-kuliah.edit', $mk) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('mata-kuliah.destroy', $mk) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus mata kuliah ini?')">
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
                            <i class="bi bi-book text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">Belum ada data mata kuliah</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($mataKuliah->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $mataKuliah->firstItem() }} - {{ $mataKuliah->lastItem() }} dari {{ $mataKuliah->total() }} data
            </div>
            {{ $mataKuliah->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
