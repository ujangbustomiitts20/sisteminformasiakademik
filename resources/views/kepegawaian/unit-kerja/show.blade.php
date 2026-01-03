@extends('layouts.app')

@section('title', 'Detail Unit Kerja')

@section('content')
<div class="page-title">
    <h4>Detail Unit Kerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.unit-kerja.index') }}">Unit Kerja</a></li>
            <li class="breadcrumb-item active">{{ $unitKerja->nama }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building me-2"></i>Informasi Unit Kerja</span>
                <a href="{{ route('kepegawaian.unit-kerja.edit', $unitKerja) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i>
                </a>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="35%">Kode</th>
                        <td>{{ $unitKerja->kode }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $unitKerja->nama }}</td>
                    </tr>
                    <tr>
                        <th>Unit Induk</th>
                        <td>{{ $unitKerja->parent?->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Kepala Unit</th>
                        <td>{{ $unitKerja->kepala?->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($unitKerja->is_active)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-secondary">Non-Aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $unitKerja->deskripsi ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        @if($unitKerja->children->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-diagram-3 me-2"></i>Sub Unit ({{ $unitKerja->children->count() }})
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($unitKerja->children as $child)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('kepegawaian.unit-kerja.show', $child) }}">{{ $child->nama }}</a>
                        @if($child->is_active)
                        <span class="badge bg-success">Aktif</span>
                        @else
                        <span class="badge bg-secondary">Non-Aktif</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-2"></i>Daftar Pegawai ({{ $unitKerja->pegawais->count() }})</span>
                <a href="{{ route('kepegawaian.pegawai.create') }}?unit_kerja_id={{ $unitKerja->id }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Pegawai
                </a>
            </div>
            <div class="card-body p-0">
                @if($unitKerja->pegawais->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unitKerja->pegawais as $index => $pegawai)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $pegawai->nip ?? '-' }}</td>
                                <td>
                                    @if($pegawai->foto)
                                    <img src="{{ asset('storage/'.$pegawai->foto) }}" alt="Foto" class="rounded-circle me-2" width="30" height="30">
                                    @endif
                                    {{ $pegawai->nama }}
                                    @if($unitKerja->kepala_id == $pegawai->id)
                                    <span class="badge bg-primary ms-1">Kepala</span>
                                    @endif
                                </td>
                                <td>{{ $pegawai->jabatan ?? '-' }}</td>
                                <td>
                                    @if($pegawai->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                    @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kepegawaian.pegawai.show', $pegawai) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people display-4 d-block mb-2"></i>
                    <p class="mb-0">Belum ada pegawai di unit kerja ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('kepegawaian.unit-kerja.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
    </a>
</div>
@endsection
