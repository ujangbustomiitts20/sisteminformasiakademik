@extends('layouts.app')

@section('title', 'Hasil EDOM')

@section('content')
<div class="page-title">
    <h4>Hasil Evaluasi Dosen oleh Mahasiswa (EDOM)</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Hasil EDOM</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Hasil EDOM - {{ $prodi->nama }}</h6>
        <form method="GET" class="d-flex gap-2">
            <select name="tahun_akademik_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                @foreach($tahunAkademiks ?? [] as $ta)
                <option value="{{ $ta->id }}" {{ request('tahun_akademik_id', $tahunAkademikAktif?->id ?? '') == $ta->id ? 'selected' : '' }}>
                    {{ $ta->nama }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body">
        @if($edoms->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Belum ada data EDOM untuk periode ini atau tabel EDOM belum tersedia.
        </div>
        @else
        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $summary['total_dosen'] ?? 0 }}</h3>
                        <small>Total Dosen Dievaluasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $summary['total_responden'] ?? 0 }}</h3>
                        <small>Total Responden</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ number_format($summary['rata_rata'] ?? 0, 2) }}</h3>
                        <small>Rata-rata Nilai</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Dosen</th>
                        <th>Mata Kuliah</th>
                        <th class="text-center">Responden</th>
                        <th class="text-center">Nilai Rata-rata</th>
                        <th class="text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($edoms as $edom)
                    @php
                        $nilai = $edom->nilai_rata ?? $edom->nilai ?? 0;
                        if ($nilai >= 4) {
                            $kategori = 'Sangat Baik';
                            $class = 'success';
                        } elseif ($nilai >= 3) {
                            $kategori = 'Baik';
                            $class = 'primary';
                        } elseif ($nilai >= 2) {
                            $kategori = 'Cukup';
                            $class = 'warning';
                        } else {
                            $kategori = 'Kurang';
                            $class = 'danger';
                        }
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $edom->dosen->nama ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $edom->dosen->nidn ?? $edom->dosen->nip ?? '' }}</small>
                        </td>
                        <td>{{ $edom->mataKuliah->nama ?? $edom->jadwalKuliah->mataKuliah->nama ?? '-' }}</td>
                        <td class="text-center">{{ $edom->jumlah_responden ?? $edom->total_responden ?? '-' }}</td>
                        <td class="text-center">
                            <strong class="text-{{ $class }}">{{ number_format($nilai, 2) }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $class }}">{{ $kategori }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
