@extends('layouts.app')

@section('title', 'Kartu Hasil Studi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Kartu Hasil Studi (KHS)</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">KHS</li>
            </ol>
        </nav>
    </div>
    @if($selectedTahunAkademik)
    <a href="{{ route('mahasiswa.khs.cetak', $selectedTahunAkademik) }}" class="btn btn-primary" target="_blank">
        <i class="bi bi-printer me-1"></i>Cetak KHS
    </a>
    @endif
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Pilih Semester -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-calendar3 me-2"></i>Pilih Semester
            </div>
            <div class="card-body">
                <form method="GET">
                    <select name="tahun_akademik" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Semester --</option>
                        @foreach($tahunAkademiks as $ta)
                        <option value="{{ $ta->id }}" {{ $selectedTahunAkademik && $selectedTahunAkademik->id == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Info Mahasiswa -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Data Mahasiswa
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="35%">NIM</td>
                        <td><strong>{{ $mahasiswa->nim }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $mahasiswa->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>{{ $mahasiswa->semester_aktif ?? $mahasiswa->semester ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($stats)
        <!-- Statistik IP -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-graph-up me-2"></i>Indeks Prestasi
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h3 class="text-primary mb-0">{{ number_format($stats['ips'], 2) }}</h3>
                            <small class="text-muted">IP Semester</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h3 class="text-success mb-0">{{ number_format($stats['ipk'], 2) }}</h3>
                            <small class="text-muted">IP Kumulatif</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">SKS Semester</span>
                    <strong>{{ $stats['sks_semester'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">SKS Kumulatif</span>
                    <strong>{{ $stats['sks_kumulatif'] }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Jumlah MK</span>
                    <strong>{{ $stats['jumlah_mk'] }}</strong>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-check me-2"></i>
                Nilai Semester {{ $selectedTahunAkademik->nama ?? '' }}
            </div>
            <div class="card-body p-0">
                @if($krsList && $krsList->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Kode</th>
                                <th>Mata Kuliah</th>
                                <th class="text-center" width="8%">SKS</th>
                                <th class="text-center" width="10%">Nilai Angka</th>
                                <th class="text-center" width="10%">Nilai Huruf</th>
                                <th class="text-center" width="10%">Bobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($krsList as $krs)
                            @php
                                $mataKuliah = $krs->jadwalKuliah->mataKuliah ?? null;
                                $nilai = $krs->nilai;
                                $huruf = $nilai->huruf ?? null;
                                $bobot = $nilai->bobot ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td><code>{{ $mataKuliah->kode ?? '-' }}</code></td>
                                <td>{{ $mataKuliah->nama ?? '-' }}</td>
                                <td class="text-center">{{ $mataKuliah->sks ?? 0 }}</td>
                                <td class="text-center">{{ $nilai->nilai_akhir ?? '-' }}</td>
                                <td class="text-center">
                                    @if($huruf)
                                    <span class="badge bg-{{ in_array($huruf, ['A', 'A-', 'B+', 'B']) ? 'success' : (in_array($huruf, ['B-', 'C+', 'C']) ? 'warning' : 'danger') }}">
                                        {{ $huruf }}
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($bobot, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3">Total</th>
                                <th class="text-center">{{ $stats['sks_semester'] ?? 0 }}</th>
                                <th colspan="2"></th>
                                <th class="text-center">{{ number_format($stats['ips'] ?? 0, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">
                        @if($selectedTahunAkademik)
                        Belum ada data nilai untuk semester ini
                        @else
                        Silakan pilih semester terlebih dahulu
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
