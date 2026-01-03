@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
<div class="page-title">
    <h4>Detail Mahasiswa</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.mahasiswa.index') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active">{{ $mahasiswa->nama }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    @if($mahasiswa->foto)
                        <img src="{{ Storage::url($mahasiswa->foto) }}" class="rounded-circle" width="120" height="120" alt="Foto">
                    @else
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                            <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                </div>
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">NIM</td>
                        <td><strong>{{ $mahasiswa->nim }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $mahasiswa->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $mahasiswa->angkatan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ $mahasiswa->status == 'Aktif' ? 'success' : 'secondary' }}">
                                {{ $mahasiswa->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dosen PA</td>
                        <td>{{ $mahasiswa->dosenWali->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $mahasiswa->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. HP</td>
                        <td>{{ $mahasiswa->no_hp ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Riwayat KRS & Nilai</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Kode MK</th>
                                <th>Mata Kuliah</th>
                                <th class="text-center">SKS</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mahasiswa->krs as $krs)
                            <tr>
                                <td>{{ $krs->jadwalKuliah->mataKuliah->kode ?? '-' }}</td>
                                <td>{{ $krs->jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                                <td class="text-center">{{ $krs->jadwalKuliah->mataKuliah->sks ?? 0 }}</td>
                                <td class="text-center">
                                    @if($krs->nilai)
                                        <span class="badge bg-{{ $krs->nilai->huruf == 'E' ? 'danger' : 'primary' }}">
                                            {{ $krs->nilai->huruf ?? '-' }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $krs->status == 'Approved' ? 'success' : ($krs->status == 'Pending' ? 'warning' : 'secondary') }}">
                                        {{ $krs->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data KRS</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('dekan.mahasiswa.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left me-1"></i> Kembali
</a>
@endsection
