@extends('layouts.app')

@section('title', 'Yudisium')

@section('content')
<div class="page-title">
    <h4>Yudisium</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Yudisium</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Data Yudisium - {{ $prodi->nama }}</h6>
        <form method="GET" class="d-flex gap-2">
            <select name="tahun" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                @foreach($tahuns ?? [] as $tahun)
                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                    {{ $tahun }}
                </option>
                @endforeach
            </select>
            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                <option value="tidak_lulus" {{ request('status') == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </form>
    </div>
    <div class="card-body">
        @if($yudisiums->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Belum ada data yudisium atau tabel yudisium belum tersedia.
        </div>
        @else
        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['total'] ?? 0 }}</h4>
                        <small>Total Peserta</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['lulus'] ?? 0 }}</h4>
                        <small>Lulus</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ $summary['tidak_lulus'] ?? 0 }}</h4>
                        <small>Tidak Lulus</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center py-2">
                        <h4 class="mb-0">{{ number_format($summary['rata_ipk'] ?? 0, 2) }}</h4>
                        <small>Rata-rata IPK</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Predikat Distribution -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Distribusi Predikat Kelulusan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col">
                                <h4 class="text-success">{{ $predikat['cumlaude'] ?? 0 }}</h4>
                                <small class="text-muted">Cumlaude<br>(≥3.50)</small>
                            </div>
                            <div class="col">
                                <h4 class="text-primary">{{ $predikat['sangat_memuaskan'] ?? 0 }}</h4>
                                <small class="text-muted">Sangat Memuaskan<br>(3.00-3.49)</small>
                            </div>
                            <div class="col">
                                <h4 class="text-info">{{ $predikat['memuaskan'] ?? 0 }}</h4>
                                <small class="text-muted">Memuaskan<br>(2.50-2.99)</small>
                            </div>
                            <div class="col">
                                <h4 class="text-secondary">{{ $predikat['cukup'] ?? 0 }}</h4>
                                <small class="text-muted">Cukup<br>(2.00-2.49)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Tanggal Yudisium</th>
                        <th class="text-center">IPK</th>
                        <th class="text-center">Total SKS</th>
                        <th class="text-center">Predikat</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($yudisiums as $yudisium)
                    @php
                        $ipk = $yudisium->mahasiswa->ipk ?? $yudisium->ipk ?? 0;
                        if ($ipk >= 3.50) {
                            $predikatLabel = 'Cumlaude';
                            $predikatClass = 'success';
                        } elseif ($ipk >= 3.00) {
                            $predikatLabel = 'Sangat Memuaskan';
                            $predikatClass = 'primary';
                        } elseif ($ipk >= 2.50) {
                            $predikatLabel = 'Memuaskan';
                            $predikatClass = 'info';
                        } else {
                            $predikatLabel = 'Cukup';
                            $predikatClass = 'secondary';
                        }
                    @endphp
                    <tr>
                        <td>{{ $yudisium->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $yudisium->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $yudisium->tanggal_yudisium?->format('d/m/Y') ?? $yudisium->tanggal?->format('d/m/Y') ?? '-' }}</td>
                        <td class="text-center"><strong>{{ number_format($ipk, 2) }}</strong></td>
                        <td class="text-center">{{ $yudisium->mahasiswa->total_sks ?? $yudisium->total_sks ?? 0 }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $predikatClass }}">{{ $predikatLabel }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $status = $yudisium->status ?? 'pending';
                                $statusClass = match(strtolower($status)) {
                                    'lulus' => 'success',
                                    'tidak_lulus' => 'danger',
                                    default => 'warning'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $yudisiums->links() }}
        @endif
    </div>
</div>
@endsection
