@extends('layouts.app')

@section('title', 'KHS')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Kartu Hasil Studi (KHS)</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">KHS</li>
            </ol>
        </nav>
    </div>
    @if(isset($krs) && $krs->count() > 0)
    <a href="{{ route('cetak.khs') }}{{ request('tahun_akademik_id') ? '?tahun_akademik_id='.request('tahun_akademik_id') : '' }}" target="_blank" class="btn btn-danger">
        <i class="bi bi-file-pdf me-1"></i>Cetak KHS
    </a>
    @endif
</div>

<!-- Filter Semester -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Pilih Semester</label>
                <select name="tahun_akademik_id" class="form-select" onchange="this.form.submit()">
                    @foreach($tahunAkademik as $ta)
                    <option value="{{ $ta->id }}" {{ ($tahunAkademikSelected && $tahunAkademikSelected->id == $ta->id) ? 'selected' : '' }}>
                        {{ $ta->nama_lengkap }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8 text-md-end">
                <div class="row">
                    <div class="col">
                        <div class="bg-primary text-white rounded p-3">
                            <h4 class="mb-0">{{ $ips }}</h4>
                            <small>IPS Semester Ini</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bg-success text-white rounded p-3">
                            <h4 class="mb-0">{{ $ipk }}</h4>
                            <small>IPK Kumulatif</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bg-info text-white rounded p-3">
                            <h4 class="mb-0">{{ $totalSks }}</h4>
                            <small>Total SKS</small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Nilai -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-text me-2"></i>Nilai Semester {{ $tahunAkademikSelected->nama_lengkap ?? '-' }}</span>
        <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak
        </button>
    </div>
    <div class="card-body">
        @if(isset($khs) && $khs->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th class="text-center">SKS</th>
                        <th class="text-center">Tugas</th>
                        <th class="text-center">UTS</th>
                        <th class="text-center">UAS</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Huruf</th>
                        <th class="text-center">Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($khs as $index => $k)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><code>{{ $k->jadwalKuliah->mataKuliah->kode }}</code></td>
                        <td>{{ $k->jadwalKuliah->mataKuliah->nama }}</td>
                        <td class="text-center">{{ $k->jadwalKuliah->mataKuliah->sks }}</td>
                        <td class="text-center">{{ $k->nilai->tugas ?? '-' }}</td>
                        <td class="text-center">{{ $k->nilai->uts ?? '-' }}</td>
                        <td class="text-center">{{ $k->nilai->uas ?? '-' }}</td>
                        <td class="text-center fw-bold">{{ $k->nilai?->nilai_akhir ? number_format($k->nilai->nilai_akhir, 2) : '-' }}</td>
                        <td class="text-center">
                            @if($k->nilai && $k->nilai->huruf)
                            <span class="badge bg-{{ in_array($k->nilai->huruf, ['A', 'A-', 'B+', 'B']) ? 'success' : (in_array($k->nilai->huruf, ['B-', 'C+', 'C']) ? 'warning' : 'danger') }}">
                                {{ $k->nilai->huruf }}
                            </span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-center">{{ $k->nilai->bobot ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-center">{{ $totalSks }}</th>
                        <th colspan="4"></th>
                        <th class="text-center">IPS</th>
                        <th class="text-center fw-bold">{{ $ips }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mb-0 mt-2">Belum ada data nilai untuk semester ini</p>
        </div>
        @endif
    </div>
</div>

<style>
@media print {
    .sidebar, .top-navbar, .page-title nav, .btn, form { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .card { box-shadow: none !important; }
}
</style>
@endsection
