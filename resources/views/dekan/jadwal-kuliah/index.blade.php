@extends('layouts.app')

@section('title', 'Jadwal Kuliah')

@section('content')
<div class="page-title">
    <h4>Jadwal Kuliah - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Kuliah</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="prodi" class="form-select">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="hari" class="form-select">
                    <option value="">Semua Hari</option>
                    <option value="Senin" {{ request('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                    <option value="Selasa" {{ request('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                    <option value="Rabu" {{ request('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                    <option value="Kamis" {{ request('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                    <option value="Jumat" {{ request('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                    <option value="Sabtu" {{ request('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Jadwal Kuliah - {{ $tahunAktif->nama ?? 'Tahun Aktif' }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Mata Kuliah</th>
                        <th>Program Studi</th>
                        <th>Dosen</th>
                        <th>Ruangan</th>
                        <th>Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $jadwal)
                    <tr>
                        <td>{{ $jadwal->hari }}</td>
                        <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                        <td>
                            <strong>{{ $jadwal->mataKuliah->nama ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $jadwal->mataKuliah->kode ?? '-' }} ({{ $jadwal->mataKuliah->sks ?? 0 }} SKS)</small>
                        </td>
                        <td>{{ $jadwal->mataKuliah->programStudi->nama ?? '-' }}</td>
                        <td>{{ $jadwal->dosen->nama ?? '-' }}</td>
                        <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                        <td>{{ $jadwal->kelas ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Tidak ada jadwal kuliah</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $jadwals->withQueryString()->links() }}
    </div>
</div>
@endsection
