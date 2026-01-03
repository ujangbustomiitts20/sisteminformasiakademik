@extends('layouts.app')

@section('title', 'Mahasiswa Perwalian')

@section('content')
<div class="page-title">
    <h4>Mahasiswa Perwalian</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Mahasiswa Perwalian</li>
        </ol>
    </nav>
</div>

<!-- Statistik -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5>Total Mahasiswa</h5>
                <h2>{{ $statistik['total'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>Aktif</h5>
                <h2>{{ $statistik['aktif'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5>Cuti</h5>
                <h2>{{ $statistik['cuti'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <h5>Non-Aktif</h5>
                <h2>{{ $statistik['non_aktif'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-people me-2"></i>
            Daftar Mahasiswa Perwalian - {{ $dosen->nama }}
        </span>
        @if($mahasiswaWali->count() > 0)
        <a href="{{ route('dosen.export-mahasiswa-wali-csv') }}" class="btn btn-success btn-sm">
            <i class="bi bi-download me-1"></i>Export CSV
        </a>
        @endif
    </div>
    <div class="card-body">
        @if($mahasiswaWali->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Email</th>
                        <th>No HP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswaWali as $index => $mhs)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $mhs->nim }}</code></td>
                        <td>{{ $mhs->nama }}</td>
                        <td>{{ $mhs->programStudi->nama ?? '-' }}</td>
                        <td>{{ $mhs->angkatan }}</td>
                        <td>{{ $mhs->semester_aktif }}</td>
                        <td>
                            @if($mhs->status == 'Aktif')
                            <span class="badge bg-success">{{ $mhs->status }}</span>
                            @elseif($mhs->status == 'Cuti')
                            <span class="badge bg-warning">{{ $mhs->status }}</span>
                            @else
                            <span class="badge bg-secondary">{{ $mhs->status }}</span>
                            @endif
                        </td>
                        <td>{{ $mhs->email }}</td>
                        <td>{{ $mhs->no_hp ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-muted py-4">
            <i class="bi bi-inbox display-4"></i>
            <p class="mt-2">Belum ada mahasiswa perwalian yang ditugaskan kepada Anda</p>
        </div>
        @endif
    </div>
</div>
@endsection
