@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="page-title">
    <h4>Rekap Absensi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Rekap Absensi</li>
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
        <form method="GET" action="{{ route('dosen.rekap-absensi') }}">
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
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-clipboard-check me-2"></i>
            Rekap Absensi: {{ $selectedJadwal->mataKuliah->nama ?? 'N/A' }} - Kelas {{ $selectedJadwal->kelas ?? '-' }}
        </span>
        <span class="badge bg-primary">{{ $tahunAkademik->nama_lengkap ?? '-' }}</span>
    </div>
    <div class="card-body">
        @if(count($rekapAbsensi) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Alpha</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapAbsensi as $index => $rekap)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $rekap['mahasiswa']->nim }}</code></td>
                        <td>{{ $rekap['mahasiswa']->nama }}</td>
                        <td class="text-center"><span class="badge bg-success">{{ $rekap['hadir'] }}</span></td>
                        <td class="text-center"><span class="badge bg-warning">{{ $rekap['sakit'] }}</span></td>
                        <td class="text-center"><span class="badge bg-info">{{ $rekap['izin'] }}</span></td>
                        <td class="text-center"><span class="badge bg-danger">{{ $rekap['alpha'] }}</span></td>
                        <td class="text-center">{{ $rekap['total'] }}</td>
                        <td class="text-center">
                            @if($rekap['persentase'] >= 75)
                            <span class="badge bg-success">{{ $rekap['persentase'] }}%</span>
                            @elseif($rekap['persentase'] >= 50)
                            <span class="badge bg-warning">{{ $rekap['persentase'] }}%</span>
                            @else
                            <span class="badge bg-danger">{{ $rekap['persentase'] }}%</span>
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
            <p class="mt-2">Belum ada data absensi untuk mata kuliah ini</p>
        </div>
        @endif
    </div>
</div>
@endif

@endif
@endsection
