@extends('layouts.app')

@section('title', 'Laporan Data Dosen')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Data Dosen & Beban Mengajar</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">Dosen</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('export.dosen', request()->all()) }}" class="btn btn-success me-2">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
        </a>
        <a href="{{ route('laporan.dosen', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.dosen') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select">
                    <option value="">Pilih Tahun Akademik</option>
                    @foreach($tahunAkademik as $ta)
                    <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                        {{ $ta->tahun ?? '' }} - {{ $ta->semester ?? '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('laporan.dosen') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Statistik -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['total_dosen'] ?? 0 }}</h3>
                <small>Total Dosen</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['total_sks'] ?? 0 }}</h3>
                <small>Total SKS</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['rata_sks'] ?? 0 }}</h3>
                <small>Rata-rata SKS/Dosen</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>{{ $summary['dosen_overload'] ?? 0 }}</h3>
                <small>Dosen Overload (>16 SKS)</small>
            </div>
        </div>
    </div>
</div>

<!-- Hasil -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-person-badge me-2"></i>Data Dosen & Beban Mengajar
        @if($tahunAkademikAktif)
            - {{ $tahunAkademikAktif->tahun ?? '' }} {{ $tahunAkademikAktif->semester ?? '' }}
        @endif
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIDN</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>SKS</th>
                        <th>Jumlah MK</th>
                        <th>Mhs. Bimbingan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosenData as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $data['dosen']->nidn }}</code></td>
                        <td>{{ $data['dosen']->nama }}</td>
                        <td>{{ $data['dosen']->programStudi->nama ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $data['total_sks'] > 16 ? 'danger' : 'info' }}">{{ $data['total_sks'] }} SKS</span>
                        </td>
                        <td>{{ $data['jumlah_mk'] }} MK</td>
                        <td>{{ $data['mahasiswa_bimbingan'] }} mhs</td>
                        <td>
                            <span class="badge bg-{{ $data['dosen']->status == 'Aktif' ? 'success' : ($data['dosen']->status == 'Cuti' ? 'warning' : 'secondary') }}">
                                {{ $data['dosen']->status ?? 'Aktif' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">Tidak ada data dosen</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Rincian Beban Mengajar -->
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-calendar-week me-2"></i>Rincian Beban Mengajar per Dosen
    </div>
    <div class="card-body">
        @foreach($dosenData->take(10) as $data)
        @if($data['jadwal'] && $data['jadwal']->count() > 0)
        <div class="mb-4">
            <h6 class="border-bottom pb-2">
                <strong>{{ $data['dosen']->nama }}</strong> 
                <small class="text-muted">({{ $data['dosen']->nidn }}) - Total {{ $data['total_sks'] }} SKS</small>
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>SKS</th>
                            <th>Hari</th>
                            <th>Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['jadwal'] as $jadwal)
                        <tr>
                            <td>{{ $jadwal->mataKuliah->nama ?? '-' }}</td>
                            <td>{{ $jadwal->kelas ?? '-' }}</td>
                            <td>{{ $jadwal->mataKuliah->sks ?? 0 }} SKS</td>
                            <td>{{ $jadwal->hari ?? '-' }}</td>
                            <td>{{ $jadwal->jam_mulai ?? '-' }} - {{ $jadwal->jam_selesai ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @endforeach
        
        @if($dosenData->count() > 10)
        <p class="text-muted text-center">
            <i class="bi bi-info-circle me-1"></i>
            Menampilkan 10 dosen pertama. Export PDF untuk melihat semua data.
        </p>
        @endif
    </div>
</div>
@endsection
