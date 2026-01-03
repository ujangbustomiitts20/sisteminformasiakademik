@extends('layouts.app')

@section('title', 'Seleksi PMB')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Seleksi PMB</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                <li class="breadcrumb-item active">Seleksi</li>
            </ol>
        </nav>
    </div>

    <!-- Menu Seleksi -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-pencil-square fs-1 text-primary"></i>
                    </div>
                    <h5>Input Nilai</h5>
                    <p class="text-muted">Input nilai ujian seleksi untuk setiap peserta PMB berdasarkan komponen penilaian.</p>
                    <a href="{{ route('pmb.seleksi.input-nilai') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-right me-1"></i> Input Nilai
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-gear fs-1 text-warning"></i>
                    </div>
                    <h5>Proses Seleksi</h5>
                    <p class="text-muted">Proses seleksi otomatis berdasarkan nilai dan kuota yang tersedia per program studi.</p>
                    <a href="{{ route('pmb.seleksi.proses') }}" class="btn btn-warning">
                        <i class="bi bi-arrow-right me-1"></i> Proses Seleksi
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-trophy fs-1 text-success"></i>
                    </div>
                    <h5>Hasil Seleksi</h5>
                    <p class="text-muted">Lihat dan export hasil seleksi peserta PMB yang sudah diproses.</p>
                    <a href="{{ route('pmb.seleksi.hasil') }}" class="btn btn-success">
                        <i class="bi bi-arrow-right me-1"></i> Lihat Hasil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-bar-chart me-2"></i>Statistik Seleksi per Gelombang
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Periode</th>
                            <th>Gelombang</th>
                            <th class="text-center">Total Peserta</th>
                            <th class="text-center">Sudah Dinilai</th>
                            <th class="text-center">Lulus</th>
                            <th class="text-center">Tidak Lulus</th>
                            <th class="text-center">Progress</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statistikGelombang as $stat)
                        <tr>
                            <td>{{ $stat['periode'] }}</td>
                            <td>{{ $stat['gelombang'] }}</td>
                            <td class="text-center">{{ $stat['total_peserta'] }}</td>
                            <td class="text-center">{{ $stat['sudah_dinilai'] }}</td>
                            <td class="text-center"><span class="badge bg-success">{{ $stat['lulus'] }}</span></td>
                            <td class="text-center"><span class="badge bg-danger">{{ $stat['tidak_lulus'] }}</span></td>
                            <td class="text-center">
                                @php
                                    $progress = $stat['total_peserta'] > 0 ? round(($stat['sudah_dinilai'] / $stat['total_peserta']) * 100) : 0;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%">
                                        {{ $progress }}%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('pmb.seleksi.input-nilai', ['gelombang' => $stat['gelombang_id']]) }}" class="btn btn-sm btn-primary" title="Input Nilai">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('pmb.seleksi.proses', ['gelombang' => $stat['gelombang_id']]) }}" class="btn btn-sm btn-warning" title="Proses Seleksi">
                                        <i class="bi bi-gear"></i>
                                    </a>
                                    <a href="{{ route('pmb.seleksi.hasil', ['gelombang' => $stat['gelombang_id']]) }}" class="btn btn-sm btn-success" title="Lihat Hasil">
                                        <i class="bi bi-trophy"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data gelombang PMB
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
