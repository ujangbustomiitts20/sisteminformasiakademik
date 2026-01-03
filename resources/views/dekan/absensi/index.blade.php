@extends('layouts.app')

@section('title', 'Monitoring Absensi')

@section('content')
<div class="page-title">
    <h4>Monitoring Absensi - Fakultas {{ $fakultas->nama }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dekan.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Absensi</li>
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
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Jadwal Kuliah & Pertemuan - {{ $tahunAktif->nama ?? 'Tahun Aktif' }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Program Studi</th>
                        <th>Dosen</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Ruangan</th>
                        <th class="text-center">Pertemuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $jadwal)
                    <tr>
                        <td>
                            <strong>{{ $jadwal->mataKuliah->nama ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $jadwal->mataKuliah->kode ?? '-' }}</small>
                        </td>
                        <td>{{ $jadwal->mataKuliah->programStudi->nama ?? '-' }}</td>
                        <td>{{ $jadwal->dosen->nama ?? '-' }}</td>
                        <td>{{ $jadwal->hari }}</td>
                        <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                        <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $jadwal->pertemuan_count >= 14 ? 'success' : ($jadwal->pertemuan_count >= 7 ? 'warning' : 'secondary') }}">
                                {{ $jadwal->pertemuan_count }}/16
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('dekan.absensi.show', $jadwal) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">Tidak ada jadwal kuliah</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $jadwals->withQueryString()->links() }}
    </div>
</div>
@endsection
