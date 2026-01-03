@extends('layouts.app')

@section('title', 'Jadwal Kuliah')

@section('content')
<div class="page-title">
    <h4>Jadwal Kuliah Program Studi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Kuliah</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">{{ $prodi->nama }} - {{ $tahunAktif ? $tahunAktif->nama . ' (' . $tahunAktif->semester . ')' : '-' }}</h6>
    </div>
    <div class="card-body">
        @php
            $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $jadwalGrouped = $jadwals->groupBy('hari');
        @endphp

        @foreach($hariList as $hari)
        @if(isset($jadwalGrouped[$hari]) && $jadwalGrouped[$hari]->count() > 0)
        <h6 class="text-primary mt-4 mb-3">{{ $hari }}</h6>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Kelas</th>
                        <th>Dosen</th>
                        <th>Ruangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalGrouped[$hari]->sortBy('jam_mulai') as $jadwal)
                    <tr>
                        <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                        <td>
                            <strong>{{ $jadwal->mataKuliah->nama ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $jadwal->mataKuliah->kode ?? '-' }}</small>
                        </td>
                        <td>{{ $jadwal->mataKuliah->sks ?? '-' }}</td>
                        <td>{{ $jadwal->kelas ?? '-' }}</td>
                        <td>{{ $jadwal->dosen->nama ?? '-' }}</td>
                        <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @endforeach

        @if($jadwals->count() == 0)
        <div class="text-center text-muted py-4">
            <i class="bi bi-calendar-x fs-1"></i>
            <p class="mt-2">Belum ada jadwal kuliah untuk semester ini</p>
        </div>
        @endif
    </div>
</div>
@endsection
