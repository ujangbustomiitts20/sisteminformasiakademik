@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
<div class="page-title">
    <h4>Detail Mahasiswa</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.mahasiswa.index') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active">{{ $mahasiswa->nama }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="avatar-circle bg-primary text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; line-height: 80px; border-radius: 50%;">
                    {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $mahasiswa->nama }}</h5>
                <p class="text-muted mb-2">{{ $mahasiswa->nim }}</p>
                <span class="badge bg-{{ $mahasiswa->status == 'aktif' ? 'success' : ($mahasiswa->status == 'cuti' ? 'warning' : 'secondary') }} mb-3">
                    {{ ucfirst($mahasiswa->status) }}
                </span>
            </div>
            <div class="card-footer">
                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="mb-0">{{ number_format($mahasiswa->ipk ?? 0, 2) }}</h5>
                        <small class="text-muted">IPK</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0">{{ $mahasiswa->total_sks ?? 0 }}</h5>
                        <small class="text-muted">SKS</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0">{{ $mahasiswa->semester_aktif ?? '-' }}</h5>
                        <small class="text-muted">Semester</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Akademik</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $mahasiswa->angkatan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dosen Wali</td>
                        <td>{{ $mahasiswa->dosenWali->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Masuk</td>
                        <td>{{ $mahasiswa->tanggal_masuk ? $mahasiswa->tanggal_masuk->format('d M Y') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- KRS Aktif -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-journal-text me-2"></i>KRS Semester Ini</h6>
            </div>
            <div class="card-body">
                @if($mahasiswa->krs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode MK</th>
                                <th>Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mahasiswa->krs->take(10) as $krs)
                            <tr>
                                <td>{{ $krs->jadwalKuliah->mataKuliah->kode ?? '-' }}</td>
                                <td>{{ $krs->jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                                <td>{{ ($krs->jadwalKuliah->mataKuliah->sks_teori ?? 0) + ($krs->jadwalKuliah->mataKuliah->sks_praktik ?? 0) }}</td>
                                <td>
                                    <span class="badge bg-{{ $krs->status == 'approved' ? 'success' : ($krs->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($krs->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Belum ada KRS</p>
                @endif
            </div>
        </div>

        <!-- Riwayat Nilai -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Riwayat Nilai</h6>
            </div>
            <div class="card-body">
                @php
                    $krsWithNilai = $mahasiswa->krs->filter(fn($krs) => $krs->nilai);
                @endphp
                @if($krsWithNilai->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode MK</th>
                                <th>Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Nilai</th>
                                <th>Bobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($krsWithNilai->take(10) as $krs)
                            <tr>
                                <td>{{ $krs->jadwalKuliah->mataKuliah->kode ?? '-' }}</td>
                                <td>{{ $krs->jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                                <td>{{ ($krs->jadwalKuliah->mataKuliah->sks_teori ?? 0) + ($krs->jadwalKuliah->mataKuliah->sks_praktik ?? 0) }}</td>
                                <td>
                                    <span class="badge bg-{{ in_array($krs->nilai->huruf ?? '', ['A', 'A-', 'B+', 'B']) ? 'success' : (in_array($krs->nilai->huruf ?? '', ['B-', 'C+', 'C']) ? 'warning' : 'danger') }}">
                                        {{ $krs->nilai->huruf ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ number_format($krs->nilai->bobot ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Belum ada nilai</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
