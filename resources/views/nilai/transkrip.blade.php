@extends('layouts.app')

@section('title', 'Transkrip Nilai')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Transkrip Nilai</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Transkrip</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('cetak.transkrip') }}" class="btn btn-primary" target="_blank">
        <i class="bi bi-file-earmark-pdf me-1"></i>Cetak Transkrip PDF
    </a>
</div>

<!-- Header Transkrip -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="150">NIM</td>
                        <td>: <strong>{{ $mahasiswa->nim }}</strong></td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td>: <strong>{{ $mahasiswa->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>: {{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Fakultas</td>
                        <td>: {{ $mahasiswa->programStudi->fakultas->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="bg-primary text-white rounded p-3">
                            <h2 class="mb-0">{{ $ipk }}</h2>
                            <small>IPK</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-success text-white rounded p-3">
                            <h2 class="mb-0">{{ $totalSks }}</h2>
                            <small>Total SKS Lulus</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Nilai per Semester -->
@if($transkrip->count() > 0)
    @foreach($transkrip as $semester => $nilai)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong><i class="bi bi-calendar3 me-2"></i>{{ $semester }}</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th width="100">Kode</th>
                            <th>Mata Kuliah</th>
                            <th class="text-center" width="60">SKS</th>
                            <th class="text-center" width="80">Nilai</th>
                            <th class="text-center" width="80">Bobot</th>
                            <th class="text-center" width="100">SKS x Bobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $semesterSks = 0; $semesterBobot = 0; @endphp
                        @foreach($nilai as $index => $n)
                        @php
                            $sks = $n->jadwalKuliah->mataKuliah->sks;
                            $bobot = $n->nilai->bobot ?? 0;
                            $semesterSks += $sks;
                            $semesterBobot += ($sks * $bobot);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><code>{{ $n->jadwalKuliah->mataKuliah->kode }}</code></td>
                            <td>{{ $n->jadwalKuliah->mataKuliah->nama }}</td>
                            <td class="text-center">{{ $sks }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ in_array($n->nilai->huruf, ['A', 'A-', 'B+', 'B']) ? 'success' : (in_array($n->nilai->huruf, ['B-', 'C+', 'C']) ? 'warning' : 'danger') }}">
                                    {{ $n->nilai->huruf }}
                                </span>
                            </td>
                            <td class="text-center">{{ $bobot }}</td>
                            <td class="text-center">{{ number_format($sks * $bobot, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Subtotal</th>
                            <th class="text-center">{{ $semesterSks }}</th>
                            <th></th>
                            <th class="text-center">IPS</th>
                            <th class="text-center">{{ $semesterSks > 0 ? number_format($semesterBobot / $semesterSks, 2) : 0 }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-file-earmark-x text-muted" style="font-size: 4rem;"></i>
        <p class="text-muted mt-3 mb-0">Belum ada data transkrip nilai</p>
    </div>
</div>
@endif

<style>
@media print {
    .sidebar, .top-navbar, .page-title nav, .btn { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    body { background: #fff !important; }
}
</style>
@endsection
