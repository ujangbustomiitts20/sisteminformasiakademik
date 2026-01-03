@extends('layouts.app')

@section('title', 'Detail Mata Kuliah')

@section('content')
<div class="page-title">
    <h4>Detail Mata Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.mata-kuliah.index') }}">Mata Kuliah</a></li>
            <li class="breadcrumb-item active">{{ $mataKuliah->kode }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Informasi Mata Kuliah</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted">Kode</td>
                        <td><strong>{{ $mataKuliah->kode }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>{{ $mataKuliah->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">SKS</td>
                        <td>{{ $mataKuliah->sks }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Semester</td>
                        <td>{{ $mataKuliah->semester ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis</td>
                        <td>
                            <span class="badge bg-{{ ($mataKuliah->jenis ?? 'Wajib') == 'Wajib' ? 'primary' : 'secondary' }}">
                                {{ $mataKuliah->jenis ?? 'Wajib' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Statistik -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Statistik Nilai (Semester Aktif)</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="text-primary mb-0">{{ $statistik['total_mahasiswa'] ?? 0 }}</h4>
                        <small class="text-muted">Total Mahasiswa</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-success mb-0">{{ number_format($statistik['rata_nilai'] ?? 0, 2) }}</h4>
                        <small class="text-muted">Rata-rata Nilai</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-0">{{ $statistik['lulus'] ?? 0 }}</h4>
                        <small class="text-muted">Lulus (≥D)</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-danger mb-0">{{ $statistik['tidak_lulus'] ?? 0 }}</h4>
                        <small class="text-muted">Tidak Lulus</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Distribusi Nilai</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach(['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'] as $grade)
                    <div class="col-4 col-md mb-2">
                        <div class="text-center p-2 border rounded">
                            <strong>{{ $grade }}</strong>
                            <div class="h5 mb-0 text-{{ in_array($grade, ['A', 'A-', 'B+', 'B']) ? 'success' : (in_array($grade, ['E']) ? 'danger' : 'secondary') }}">
                                {{ $distribusiNilai[$grade] ?? 0 }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Daftar Nilai Mahasiswa</h6>
                <form method="GET" class="d-flex gap-2">
                    <select name="tahun_akademik_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        @foreach($tahunAkademiks ?? [] as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th class="text-center">Nilai Angka</th>
                                <th class="text-center">Nilai Huruf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nilais as $nilai)
                            <tr>
                                <td>{{ $nilai->mahasiswa->nim ?? '-' }}</td>
                                <td>{{ $nilai->mahasiswa->nama ?? '-' }}</td>
                                <td class="text-center">{{ number_format($nilai->nilai_angka, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ in_array($nilai->nilai_huruf, ['A', 'A-', 'B+', 'B']) ? 'success' : ($nilai->nilai_huruf == 'E' ? 'danger' : 'secondary') }}">
                                        {{ $nilai->nilai_huruf }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada data nilai</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
