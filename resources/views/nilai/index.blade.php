@extends('layouts.app')

@section('title', 'Input Nilai')

@section('content')
<div class="page-title">
    <h4>Input Nilai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Input Nilai</li>
        </ol>
    </nav>
</div>

<!-- Filter Tahun Akademik -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select" onchange="this.form.submit()">
                    @foreach($tahunAkademik as $ta)
                    <option value="{{ $ta->id }}" {{ ($tahunAkademikAktif && $tahunAkademikAktif->id == $ta->id) ? 'selected' : '' }}>
                        {{ $ta->nama_lengkap }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Kelas -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-clipboard-data me-2"></i>Kelas yang Diampu - {{ $tahunAkademikAktif->nama_lengkap ?? '-' }}
    </div>
    <div class="card-body">
        @if(count($jadwalMengajar) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Jadwal</th>
                        <th>Ruangan</th>
                        <th class="text-center">Jumlah Peserta</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalMengajar as $jadwal)
                    <tr>
                        <td><code>{{ $jadwal->mataKuliah->kode }}</code></td>
                        <td>
                            <strong>{{ $jadwal->mataKuliah->nama }}</strong>
                            <br><small class="text-muted">{{ $jadwal->mataKuliah->sks }} SKS</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $jadwal->kelas }}</span></td>
                        <td>
                            {{ $jadwal->hari }}<br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</small>
                        </td>
                        <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $jadwal->jumlahPeserta() }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('nilai.input', $jadwal) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil-square me-1"></i>Input Nilai
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mb-0 mt-2">Tidak ada jadwal mengajar pada semester ini</p>
        </div>
        @endif
    </div>
</div>
@endsection
