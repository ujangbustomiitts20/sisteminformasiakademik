@extends('layouts.app')

@section('title', 'Detail Kurikulum')

@section('content')
<div class="page-title">
    <h4>Detail Kurikulum</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.kurikulum.index') }}">Kurikulum</a></li>
            <li class="breadcrumb-item active">{{ $kurikulum->nama }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Kurikulum</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $kurikulum->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $kurikulum->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tahun</td>
                        <td>{{ $kurikulum->tahun_mulai ?? '-' }}{{ $kurikulum->tahun_selesai ? ' - ' . $kurikulum->tahun_selesai : '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ $kurikulum->is_aktif ? 'success' : 'secondary' }}">
                                {{ $kurikulum->is_aktif ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total SKS</td>
                        <td><strong>{{ $kurikulum->mataKuliah->sum('sks') ?? 0 }} SKS</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Daftar Mata Kuliah</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Mata Kuliah</th>
                                <th class="text-center">SKS</th>
                                <th class="text-center">Semester</th>
                                <th>Jenis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kurikulum->mataKuliah as $mk)
                            <tr>
                                <td>{{ $mk->kode }}</td>
                                <td>{{ $mk->nama }}</td>
                                <td class="text-center">{{ $mk->sks }}</td>
                                <td class="text-center">{{ $mk->pivot->semester ?? $mk->semester ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $mk->jenis == 'Wajib' ? 'primary' : 'info' }}">
                                        {{ $mk->jenis ?? 'Wajib' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada mata kuliah</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('dekan.kurikulum.index') }}" class="btn btn-secondary mt-3">
    <i class="bi bi-arrow-left me-1"></i> Kembali
</a>
@endsection
