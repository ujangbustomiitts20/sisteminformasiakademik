@extends('layouts.app')

@section('title', 'Laporan Nilai')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Nilai</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active">Nilai</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('laporan.nilai', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
        <i class="bi bi-file-pdf me-1"></i>Export PDF
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.nilai') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" class="form-select">
                    @foreach($tahunAkademik as $ta)
                    <option value="{{ $ta->id }}" {{ ($tahunAkademikAktif?->id == $ta->id) ? 'selected' : '' }}>
                        {{ $ta->tahun }} - {{ $ta->semester }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hasil per Mata Kuliah -->
@forelse($nilai as $mataKuliah => $data)
<div class="card mb-4">
    <div class="card-header bg-light">
        <strong><i class="bi bi-book me-2"></i>{{ $mataKuliah }}</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th class="text-center">Tugas</th>
                        <th class="text-center">UTS</th>
                        <th class="text-center">UAS</th>
                        <th class="text-center">Akhir</th>
                        <th class="text-center">Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $index => $krs)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $krs->mahasiswa->nim }}</code></td>
                        <td>{{ $krs->mahasiswa->nama }}</td>
                        <td class="text-center">{{ $krs->nilai->nilai_tugas ?? '-' }}</td>
                        <td class="text-center">{{ $krs->nilai->nilai_uts ?? '-' }}</td>
                        <td class="text-center">{{ $krs->nilai->nilai_uas ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $krs->nilai->nilai_akhir ?? '-' }}</strong></td>
                        <td class="text-center">
                            @if($krs->nilai)
                            <span class="badge bg-{{ in_array($krs->nilai->huruf, ['A', 'B']) ? 'success' : (in_array($krs->nilai->huruf, ['C']) ? 'warning' : 'danger') }}">
                                {{ $krs->nilai->huruf }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @php
            $nilaiCount = $data->filter(fn($k) => $k->nilai)->count();
            $avgNilai = $nilaiCount > 0 ? $data->filter(fn($k) => $k->nilai)->avg(fn($k) => $k->nilai->nilai_akhir) : 0;
        @endphp
        <div class="mt-3 text-muted small">
            <strong>Ringkasan:</strong> {{ $data->count() }} mahasiswa | 
            {{ $nilaiCount }} sudah dinilai | 
            Rata-rata: {{ number_format($avgNilai, 1) }}
        </div>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2">Tidak ada data nilai untuk periode ini</p>
    </div>
</div>
@endforelse
@endsection
