@extends('layouts.app')

@section('title', 'Jadwal Ujian')

@section('content')
<div class="page-title">
    <h4>Jadwal Ujian - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Ujian</li>
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
                <select name="jenis" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="UTS" {{ request('jenis') == 'UTS' ? 'selected' : '' }}>UTS</option>
                    <option value="UAS" {{ request('jenis') == 'UAS' ? 'selected' : '' }}>UAS</option>
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
        <h6 class="mb-0">Jadwal Ujian - {{ $tahunAktif->nama ?? 'Tahun Aktif' }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Mata Kuliah</th>
                        <th>Program Studi</th>
                        <th>Jenis</th>
                        <th>Ruangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalUjians as $jadwal)
                    <tr>
                        <td>{{ $jadwal->tanggal ? \Carbon\Carbon::parse($jadwal->tanggal)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                        <td>
                            <strong>{{ $jadwal->mataKuliah->nama ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $jadwal->mataKuliah->kode ?? '-' }}</small>
                        </td>
                        <td>{{ $jadwal->mataKuliah->programStudi->nama ?? '-' }}</td>
                        <td><span class="badge bg-{{ $jadwal->jenis_ujian == 'UTS' ? 'info' : 'primary' }}">{{ $jadwal->jenis_ujian }}</span></td>
                        <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Tidak ada jadwal ujian</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $jadwalUjians->withQueryString()->links() }}
    </div>
</div>
@endsection
