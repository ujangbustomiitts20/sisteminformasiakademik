@extends('layouts.app')

@section('title', 'Transkrip Nilai')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Transkrip Nilai</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Transkrip</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('mahasiswa.transkrip.cetak') }}" class="btn btn-primary" target="_blank">
        <i class="bi bi-printer me-1"></i>Cetak Transkrip
    </a>
</div>

<div class="row">
    <div class="col-md-4">
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
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $mahasiswa->angkatan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dosen Wali</td>
                        <td>{{ $mahasiswa->dosenWali->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Ringkasan -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-bar-chart me-2"></i>Ringkasan Akademik
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h1 class="display-4 text-primary mb-0">{{ number_format($stats['ipk'], 2) }}</h1>
                    <small class="text-muted">Indeks Prestasi Kumulatif</small>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="mb-0">{{ $stats['total_sks_lulus'] }}</h4>
                        <small class="text-muted">Total SKS</small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0">{{ $stats['total_mk_lulus'] }}</h4>
                        <small class="text-muted">Mata Kuliah</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <span class="badge bg-{{ $stats['ipk'] >= 3.5 ? 'success' : ($stats['ipk'] >= 3.0 ? 'primary' : ($stats['ipk'] >= 2.5 ? 'warning' : 'danger')) }} fs-6">
                        {{ $stats['predikat'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-text me-2"></i>Daftar Nilai
            </div>
            <div class="card-body p-0">
                @forelse($nilaiPerSemester as $tahunAkademikId => $nilaiList)
                @php
                    $ta = $nilaiList->first()->tahunAkademik;
                    $totalSks = 0;
                    $totalBobot = 0;
                    foreach($nilaiList as $n) {
                        $sks = $n->mataKuliah->sks ?? 0;
                        $bobot = match(strtoupper($n->nilai_huruf)) {
                            'A' => 4.0, 'A-' => 3.75, 'B+' => 3.5, 'B' => 3.0, 'B-' => 2.75,
                            'C+' => 2.5, 'C' => 2.0, 'C-' => 1.75, 'D+' => 1.5, 'D' => 1.0,
                            default => 0,
                        };
                        $totalSks += $sks;
                        $totalBobot += ($sks * $bobot);
                    }
                    $ips = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
                @endphp
                <div class="border-bottom">
                    <div class="p-3 bg-light d-flex justify-content-between align-items-center">
                        <strong>{{ $ta->nama ?? 'Semester ' . $tahunAkademikId }}</strong>
                        <span class="badge bg-primary">IPS: {{ number_format($ips, 2) }}</span>
                    </div>
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Mata Kuliah</th>
                                <th class="text-center">SKS</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center">Bobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nilaiList as $nilai)
                            @php
                                $bobot = match(strtoupper($nilai->nilai_huruf)) {
                                    'A' => 4.0, 'A-' => 3.75, 'B+' => 3.5, 'B' => 3.0, 'B-' => 2.75,
                                    'C+' => 2.5, 'C' => 2.0, 'C-' => 1.75, 'D+' => 1.5, 'D' => 1.0,
                                    default => 0,
                                };
                            @endphp
                            <tr>
                                <td><code>{{ $nilai->mataKuliah->kode ?? '-' }}</code></td>
                                <td>{{ $nilai->mataKuliah->nama ?? '-' }}</td>
                                <td class="text-center">{{ $nilai->mataKuliah->sks ?? 0 }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ in_array($nilai->nilai_huruf, ['A', 'A-', 'B+', 'B']) ? 'success' : (in_array($nilai->nilai_huruf, ['B-', 'C+', 'C']) ? 'warning' : 'danger') }}">
                                        {{ $nilai->nilai_huruf }}
                                    </span>
                                </td>
                                <td class="text-center">{{ number_format($bobot, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2">Total</th>
                                <th class="text-center">{{ $totalSks }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">Belum ada data nilai</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
