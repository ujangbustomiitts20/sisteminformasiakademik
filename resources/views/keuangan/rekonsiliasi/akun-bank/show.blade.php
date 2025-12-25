@extends('layouts.app')

@section('title', 'Detail Akun Bank')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $akunBank->nama_bank }}</h1>
            <p class="text-muted mb-0">{{ $akunBank->nomor_rekening }} - {{ $akunBank->nama_rekening }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('akun-bank.edit', $akunBank) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('akun-bank.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Info Akun -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Rekening</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Bank</td>
                            <td class="text-end fw-bold">{{ $akunBank->nama_bank }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kode Bank</td>
                            <td class="text-end">{{ $akunBank->kode_bank ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Rekening</td>
                            <td class="text-end fw-bold">{{ $akunBank->nomor_rekening }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Rekening</td>
                            <td class="text-end">{{ $akunBank->nama_rekening }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Cabang</td>
                            <td class="text-end">{{ $akunBank->cabang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe</td>
                            <td class="text-end">
                                @if($akunBank->tipe == 'penampungan')
                                    <span class="badge bg-primary">Penampungan</span>
                                @elseif($akunBank->tipe == 'operasional')
                                    <span class="badge bg-success">Operasional</span>
                                @else
                                    <span class="badge bg-info">Beasiswa</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td class="text-end">
                                @if($akunBank->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Saldo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Saldo Awal</small>
                        <h5>Rp {{ number_format($akunBank->saldo_awal, 0, ',', '.') }}</h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Saldo Sistem</small>
                        <h4 class="text-primary">Rp {{ number_format($akunBank->saldo_sistem, 0, ',', '.') }}</h4>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted">Total Kredit</small>
                            <h6 class="text-success">
                                Rp {{ number_format($stats['total_kredit'] ?? 0, 0, ',', '.') }}
                            </h6>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Total Debit</small>
                            <h6 class="text-danger">
                                Rp {{ number_format($stats['total_debit'] ?? 0, 0, ',', '.') }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistik Mutasi</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Mutasi</span>
                        <strong>{{ $stats['total_mutasi'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Matched</span>
                        <span class="badge bg-success">{{ $stats['matched'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pending</span>
                        <span class="badge bg-warning">{{ $stats['pending'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Unmatched</span>
                        <span class="badge bg-danger">{{ $stats['unmatched'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Manual</span>
                        <span class="badge bg-secondary">{{ $stats['manual'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mutasi Terbaru -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Mutasi Terakhir</h5>
                    <a href="{{ route('mutasi-bank.index', ['akun_bank_id' => $akunBank->id]) }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akunBank->mutasiBank as $mutasi)
                                <tr>
                                    <td>{{ $mutasi->tanggal->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $mutasi->keterangan }}">
                                            {{ $mutasi->keterangan ?? '-' }}
                                        </span>
                                        @if($mutasi->referensi)
                                            <br><small class="text-muted">Ref: {{ $mutasi->referensi }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($mutasi->tipe == 'kredit')
                                            <span class="badge bg-success">Kredit</span>
                                        @else
                                            <span class="badge bg-danger">Debit</span>
                                        @endif
                                    </td>
                                    <td class="text-end {{ $mutasi->tipe == 'kredit' ? 'text-success' : 'text-danger' }}">
                                        {{ $mutasi->tipe == 'kredit' ? '+' : '-' }} Rp {{ number_format($mutasi->nominal, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($mutasi->status == 'matched')
                                            <span class="badge bg-success">Matched</span>
                                        @elseif($mutasi->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($mutasi->status == 'unmatched')
                                            <span class="badge bg-danger">Unmatched</span>
                                        @else
                                            <span class="badge bg-secondary">Manual</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        Belum ada mutasi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Rekonsiliasi History -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Riwayat Rekonsiliasi</h5>
                    <a href="{{ route('rekonsiliasi.index', ['akun_bank_id' => $akunBank->id]) }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Rekon</th>
                                    <th>Periode</th>
                                    <th class="text-end">Selisih</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($akunBank->rekonsiliasi()->latest()->take(5)->get() as $rekon)
                                <tr>
                                    <td>
                                        <a href="{{ route('rekonsiliasi.show', $rekon) }}">
                                            {{ $rekon->nomor_rekonsiliasi }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $rekon->periode_awal->format('d/m/Y') }} - {{ $rekon->periode_akhir->format('d/m/Y') }}
                                    </td>
                                    <td class="text-end {{ $rekon->selisih == 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format(abs($rekon->selisih), 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($rekon->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($rekon->status == 'completed')
                                            <span class="badge bg-info">Completed</span>
                                        @elseif($rekon->status == 'in_progress')
                                            <span class="badge bg-warning">In Progress</span>
                                        @else
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        Belum ada rekonsiliasi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
