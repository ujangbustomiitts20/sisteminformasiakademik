@extends('layouts.app')

@section('title', 'Materi Kuliah')

@section('content')
<div class="page-title">
    <h4>Materi Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Materi Kuliah</li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Mahasiswa</h6>
                        <h4 class="mb-0">{{ $mahasiswa->nama }}</h4>
                        <small>{{ $mahasiswa->nim }}</small>
                    </div>
                    <i class="bi bi-person-circle display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Semester</h6>
                        <h4 class="mb-0">{{ $tahunAkademikAktif?->nama_lengkap ?? '-' }}</h4>
                        <small>Mata Kuliah: {{ $krsData->count() }}</small>
                    </div>
                    <i class="bi bi-calendar3 display-4 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-journal-text me-2"></i>Daftar Mata Kuliah & Materi
    </div>
    <div class="card-body">
        @if($krsData->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-journal-x display-1 text-muted"></i>
            <h5 class="text-muted mt-3">Belum Ada Mata Kuliah</h5>
            <p class="text-muted">Anda belum mengambil mata kuliah di semester ini.</p>
        </div>
        @else
        <div class="row">
            @foreach($krsData as $krs)
            @php
                $jadwal = $krs->jadwalKuliah;
                $pertemuanCount = $jadwal->pertemuan->count();
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">{{ $jadwal->mataKuliah->nama }}</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <i class="bi bi-tag me-2 text-muted"></i>{{ $jadwal->mataKuliah->kode }} | Kelas {{ $jadwal->kelas }}
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-person me-2 text-muted"></i>{{ $jadwal->dosen->nama }}
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-calendar3 me-2 text-muted"></i>{{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-journal-text me-2 text-muted"></i>
                            <span class="badge bg-{{ $pertemuanCount > 0 ? 'success' : 'secondary' }}">
                                {{ $pertemuanCount }} Materi Tersedia
                            </span>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('pertemuan.mahasiswa.detail', $jadwal) }}" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-eye me-1"></i>Lihat Materi
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
