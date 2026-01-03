@extends('layouts.app')

@section('title', 'Detail Mata Kuliah')

@section('content')
<div class="page-title">
    <h4>Detail Mata Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dekan.mata-kuliah.index') }}">Mata Kuliah</a></li>
            <li class="breadcrumb-item active">{{ $mataKuliah->kode }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Info Mata Kuliah -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Mata Kuliah</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Kode</strong></td>
                        <td>: <code>{{ $mataKuliah->kode }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>: {{ $mataKuliah->nama }}</td>
                    </tr>
                    <tr>
                        <td><strong>SKS</strong></td>
                        <td>: {{ $mataKuliah->sks }} SKS</td>
                    </tr>
                    <tr>
                        <td><strong>Semester</strong></td>
                        <td>: {{ $mataKuliah->semester ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis</strong></td>
                        <td>: {{ $mataKuliah->jenis ?? 'Wajib' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Program Studi</strong></td>
                        <td>: {{ $mataKuliah->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jml Pertemuan</strong></td>
                        <td>: {{ $mataKuliah->jumlah_pertemuan ?? 16 }} pertemuan</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Prasyarat -->
        @if($mataKuliah->prasyarat && $mataKuliah->prasyarat->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Mata Kuliah Prasyarat</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($mataKuliah->prasyarat as $prasyarat)
                    <li class="mb-2">
                        <i class="bi bi-arrow-right-circle text-primary"></i>
                        <code>{{ $prasyarat->kode }}</code> - {{ $prasyarat->nama }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-8">
        <!-- Deskripsi -->
        @if($mataKuliah->deskripsi)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Deskripsi</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{!! nl2br(e($mataKuliah->deskripsi)) !!}</p>
            </div>
        </div>
        @endif
        
        <!-- Jadwal Semester Ini -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Jadwal Semester {{ $tahunAktif->nama ?? 'Aktif' }}</h6>
            </div>
            <div class="card-body">
                @if($jadwalSemesterIni->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Ruangan</th>
                                <th>Dosen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalSemesterIni as $jadwal)
                            <tr>
                                <td>{{ $jadwal->kelas ?? '-' }}</td>
                                <td>{{ $jadwal->hari ?? '-' }}</td>
                                <td>{{ $jadwal->jam_mulai ?? '-' }} - {{ $jadwal->jam_selesai ?? '-' }}</td>
                                <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                                <td>{{ $jadwal->dosen->nama ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                    <p class="mt-2">Tidak ada jadwal di semester ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('dekan.mata-kuliah.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
@endsection
