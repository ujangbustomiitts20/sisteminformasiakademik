@extends('layouts.app')

@section('title', 'Prasyarat Mata Kuliah')

@section('content')
<div class="page-title">
    <h4>Prasyarat Mata Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Prasyarat Mata Kuliah</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Daftar Mata Kuliah & Prasyarat</h5>
                <a href="{{ route('prasyarat.kurikulum') }}" class="btn btn-info">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Lihat Struktur Kurikulum
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Filter -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Program Studi</label>
                        <select name="program_studi_id" class="form-select">
                            <option value="">-- Semua Prodi --</option>
                            @foreach($programStudi as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select">
                            <option value="">-- Semua --</option>
                            @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pencarian</label>
                        <input type="text" name="search" class="form-control" placeholder="Kode/Nama MK..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Filter</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="has_prasyarat" value="1" id="hasPrasyarat" {{ request('has_prasyarat') ? 'checked' : '' }}>
                            <label class="form-check-label" for="hasPrasyarat">Punya Prasyarat</label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('prasyarat.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode</th>
                                <th width="25%">Nama Mata Kuliah</th>
                                <th width="8%">SKS</th>
                                <th width="8%">Semester</th>
                                <th width="30%">Prasyarat</th>
                                <th width="14%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mataKuliah as $mk)
                            <tr>
                                <td>{{ $mataKuliah->firstItem() + $loop->index }}</td>
                                <td><code>{{ $mk->kode }}</code></td>
                                <td>
                                    {{ $mk->nama }}
                                    <br><small class="text-muted">{{ $mk->programStudi->nama ?? '-' }}</small>
                                </td>
                                <td class="text-center">{{ $mk->sks }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">Sem {{ $mk->semester }}</span>
                                </td>
                                <td>
                                    @if($mk->prasyarat->count() > 0)
                                        @foreach($mk->prasyarat as $prasyarat)
                                        <span class="badge bg-{{ $prasyarat->pivot->jenis_prasyarat == 'wajib' ? 'danger' : 'warning' }} me-1 mb-1">
                                            {{ $prasyarat->kode }}
                                            @if($prasyarat->pivot->jenis_prasyarat == 'wajib')
                                            (Min: {{ $prasyarat->pivot->nilai_minimal }})
                                            @else
                                            (Pilihan)
                                            @endif
                                        </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Tidak ada prasyarat</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('prasyarat.edit', $mk->hashid) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square me-1"></i>Kelola
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada data mata kuliah</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $mataKuliah->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Legenda -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Keterangan:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <span class="badge bg-danger me-2">Wajib (Min: D)</span>
                        <span class="text-muted">= Mahasiswa harus lulus MK prasyarat dengan nilai minimal tertentu</span>
                    </div>
                    <div class="col-md-6">
                        <span class="badge bg-warning me-2">Pilihan</span>
                        <span class="text-muted">= Mahasiswa cukup pernah mengambil MK prasyarat (tidak harus lulus)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
