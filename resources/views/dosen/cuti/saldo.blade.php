@extends('layouts.app')

@section('title', 'Saldo Cuti')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Saldo Cuti</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dosen.cuti.index') }}">Pengajuan Cuti</a></li>
                <li class="breadcrumb-item active">Saldo Cuti</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('dosen.cuti.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Saldo Tahun Ini -->
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Saldo Cuti {{ date('Y') }}</h6>
            </div>
            <div class="card-body text-center">
                <div class="display-3 fw-bold text-primary mb-2">{{ $saldoTahunIni->sisa_cuti ?? 0 }}</div>
                <p class="text-muted mb-3">Sisa hari cuti</p>
                
                <div class="row text-center border-top pt-3">
                    <div class="col-4">
                        <div class="h5 mb-0">{{ $saldoTahunIni->jatah_cuti ?? 12 }}</div>
                        <small class="text-muted">Jatah</small>
                    </div>
                    <div class="col-4">
                        <div class="h5 mb-0 text-danger">{{ $saldoTahunIni->cuti_digunakan ?? 0 }}</div>
                        <small class="text-muted">Terpakai</small>
                    </div>
                    <div class="col-4">
                        <div class="h5 mb-0 text-warning">{{ $saldoTahunIni->cuti_hangus ?? 0 }}</div>
                        <small class="text-muted">Hangus</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <p class="small text-muted mb-2">Penggunaan Cuti {{ date('Y') }}</p>
                @php
                    $persentase = ($saldoTahunIni->jatah_cuti ?? 12) > 0 
                        ? (($saldoTahunIni->cuti_digunakan ?? 0) / ($saldoTahunIni->jatah_cuti ?? 12)) * 100 
                        : 0;
                @endphp
                <div class="progress mb-2" style="height: 20px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $persentase }}%">
                        {{ number_format($persentase, 0) }}%
                    </div>
                </div>
                <small class="text-muted">
                    {{ $saldoTahunIni->cuti_digunakan ?? 0 }} dari {{ $saldoTahunIni->jatah_cuti ?? 12 }} hari terpakai
                </small>
            </div>
        </div>

        <!-- Riwayat Saldo Per Tahun -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Per Tahun</h6>
            </div>
            <div class="list-group list-group-flush">
                @forelse($saldoCutiList as $saldo)
                <div class="list-group-item d-flex justify-content-between align-items-center {{ $saldo->tahun == date('Y') ? 'bg-light' : '' }}">
                    <div>
                        <strong>{{ $saldo->tahun }}</strong>
                        @if($saldo->tahun == date('Y'))
                        <span class="badge bg-primary ms-1">Aktif</span>
                        @endif
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success">{{ $saldo->sisa_cuti }} sisa</span>
                        <br>
                        <small class="text-muted">{{ $saldo->cuti_digunakan }}/{{ $saldo->jatah_cuti }} digunakan</small>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-muted py-4">
                    Belum ada data saldo cuti
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Filter Tahun -->
        <div class="card shadow-sm mb-4">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label">Riwayat Cuti Tahun:</label>
                    </div>
                    <div class="col-auto">
                        <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Riwayat Cuti -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Riwayat Cuti Disetujui Tahun {{ $tahun }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Jenis Cuti</th>
                                <th>Tanggal</th>
                                <th>Jumlah Hari</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatCuti as $cuti)
                            <tr>
                                <td>
                                    <a href="{{ route('dosen.cuti.show', $cuti) }}" class="text-decoration-none">
                                        {{ $cuti->no_pengajuan }}
                                    </a>
                                </td>
                                <td>{{ \App\Models\CutiPegawai::JENIS_CUTI[$cuti->jenis_cuti] ?? $cuti->jenis_cuti }}</td>
                                <td>{{ $cuti->tanggal_mulai->format('d/m/Y') }} - {{ $cuti->tanggal_selesai->format('d/m/Y') }}</td>
                                <td>{{ $cuti->jumlah_hari }} hari</td>
                                <td>
                                    <span class="badge bg-success">Disetujui</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada riwayat cuti disetujui tahun {{ $tahun }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($riwayatCuti->isNotEmpty())
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Cuti Tahun {{ $tahun }}:</th>
                                <th>{{ $riwayatCuti->sum('jumlah_hari') }} hari</th>
                                <th></th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
