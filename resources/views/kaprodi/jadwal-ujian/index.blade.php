@extends('layouts.app')

@section('title', 'Jadwal Ujian')

@section('content')
<div class="page-title">
    <h4>Jadwal Ujian</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Ujian</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Jadwal Ujian - {{ $prodi->nama }}</h6>
        <form method="GET" class="d-flex gap-2">
            <select name="tahun_akademik_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                @foreach($tahunAkademiks as $ta)
                <option value="{{ $ta->id }}" {{ request('tahun_akademik_id', $tahunAkademikAktif?->id) == $ta->id ? 'selected' : '' }}>
                    {{ $ta->nama }}
                </option>
                @endforeach
            </select>
            <select name="jenis" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Jenis</option>
                <option value="UTS" {{ request('jenis') == 'UTS' ? 'selected' : '' }}>UTS</option>
                <option value="UAS" {{ request('jenis') == 'UAS' ? 'selected' : '' }}>UAS</option>
            </select>
        </form>
    </div>
    <div class="card-body">
        @if($jadwalUjians->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Belum ada data jadwal ujian atau tabel jadwal ujian belum tersedia.
        </div>
        @else
        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['total'] ?? 0 }}</h4>
                        <small>Total Jadwal Ujian</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['uts'] ?? 0 }}</h4>
                        <small>UTS</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['uas'] ?? 0 }}</h4>
                        <small>UAS</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Ruangan</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Peserta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalUjians as $jadwal)
                    <tr>
                        <td>{{ $jadwal->tanggal?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $jadwal->jam_mulai ?? '-' }} - {{ $jadwal->jam_selesai ?? '-' }}</td>
                        <td>
                            <strong>{{ $jadwal->mataKuliah->kode ?? $jadwal->jadwalKuliah->mataKuliah->kode ?? '-' }}</strong><br>
                            <small>{{ $jadwal->mataKuliah->nama ?? $jadwal->jadwalKuliah->mataKuliah->nama ?? '-' }}</small>
                        </td>
                        <td>{{ $jadwal->dosen->nama ?? $jadwal->jadwalKuliah->dosen->nama ?? '-' }}</td>
                        <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ ($jadwal->jenis ?? 'UTS') == 'UTS' ? 'info' : 'success' }}">
                                {{ $jadwal->jenis ?? 'UTS' }}
                            </span>
                        </td>
                        <td class="text-center">{{ $jadwal->jumlah_peserta ?? $jadwal->peserta_count ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $jadwalUjians->links() }}
        @endif
    </div>
</div>
@endsection
