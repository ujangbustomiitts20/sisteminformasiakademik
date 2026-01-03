@extends('layouts.app')

@section('title', 'Rekap Nilai')

@section('content')
<div class="page-title">
    <h4>Rekap Nilai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Rekap Nilai</li>
        </ol>
    </nav>
</div>

@if(!$tahunAkademik)
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>Tidak ada tahun akademik aktif. Silakan hubungi administrator.
</div>
@else

<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-funnel me-2"></i>Pilih Mata Kuliah
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('dosen.rekap-nilai') }}">
            <div class="row align-items-end">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Mata Kuliah</label>
                    <select name="jadwal_id" class="form-select" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($jadwalMengajar as $jadwal)
                        <option value="{{ $jadwal->id }}" {{ request('jadwal_id') == $jadwal->id ? 'selected' : '' }}>
                            {{ $jadwal->mataKuliah->nama ?? 'N/A' }} - Kelas {{ $jadwal->kelas ?? '-' }} ({{ $jadwal->hari ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($selectedJadwal)
<!-- Statistik Nilai -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5>Rata-rata</h5>
                <h2>{{ number_format($statistik['rata_rata'] ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>Nilai Tertinggi</h5>
                <h2>{{ number_format($statistik['tertinggi'] ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5>Nilai Terendah</h5>
                <h2>{{ number_format($statistik['terendah'] ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5>Sudah Input</h5>
                <h2>{{ $statistik['sudah_input'] ?? 0 }}/{{ $statistik['total'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Distribusi Nilai -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Distribusi Nilai
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <div class="border rounded p-3">
                            <h5 class="text-success mb-0">A</h5>
                            <h3>{{ $statistik['distribusi']['A'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="border rounded p-3">
                            <h5 class="text-primary mb-0">B</h5>
                            <h3>{{ $statistik['distribusi']['B'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="border rounded p-3">
                            <h5 class="text-info mb-0">C</h5>
                            <h3>{{ $statistik['distribusi']['C'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="border rounded p-3">
                            <h5 class="text-warning mb-0">D</h5>
                            <h3>{{ $statistik['distribusi']['D'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="border rounded p-3">
                            <h5 class="text-danger mb-0">E</h5>
                            <h3>{{ $statistik['distribusi']['E'] ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="border rounded p-3 bg-light">
                            <h5 class="text-muted mb-0">Belum</h5>
                            <h3>{{ $statistik['distribusi']['Belum'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-list-ol me-2"></i>
            Daftar Nilai: {{ $selectedJadwal->mataKuliah->nama }} - Kelas {{ $selectedJadwal->kelas }}
        </span>
        <span class="badge bg-primary">{{ $tahunAkademik->nama_lengkap ?? '-' }}</span>
    </div>
    <div class="card-body">
        @if(count($rekapNilai) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th class="text-center">Tugas</th>
                        <th class="text-center">UTS</th>
                        <th class="text-center">UAS</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapNilai as $index => $rekap)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $rekap['mahasiswa']->nim }}</code></td>
                        <td>{{ $rekap['mahasiswa']->nama }}</td>
                        <td class="text-center">{{ $rekap['tugas'] ?? '-' }}</td>
                        <td class="text-center">{{ $rekap['uts'] ?? '-' }}</td>
                        <td class="text-center">{{ $rekap['uas'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $rekap['akhir'] ?? '-' }}</strong></td>
                        <td class="text-center">
                            @if($rekap['huruf'])
                                @if(in_array($rekap['huruf'], ['A', 'B', 'B+', 'B-']))
                                <span class="badge bg-success">{{ $rekap['huruf'] }}</span>
                                @elseif(in_array($rekap['huruf'], ['C', 'C+', 'C-']))
                                <span class="badge bg-info">{{ $rekap['huruf'] }}</span>
                                @elseif($rekap['huruf'] == 'D')
                                <span class="badge bg-warning">{{ $rekap['huruf'] }}</span>
                                @else
                                <span class="badge bg-danger">{{ $rekap['huruf'] }}</span>
                                @endif
                            @else
                            <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-muted py-4">
            <i class="bi bi-inbox display-4"></i>
            <p class="mt-2">Belum ada data nilai untuk mata kuliah ini</p>
        </div>
        @endif
    </div>
</div>
@endif

@endif
@endsection
