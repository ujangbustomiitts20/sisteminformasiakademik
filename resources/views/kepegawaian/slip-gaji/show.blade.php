@extends('layouts.app')

@section('title', 'Detail Slip Gaji')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Slip Gaji</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.slip-gaji.index') }}">Slip Gaji</a></li>
                    <li class="breadcrumb-item active">{{ $slipGaji->no_slip }}</li>
                </ol>
            </nav>
        </div>
        <div>
            @if($slipGaji->status === 'draft')
                <form action="{{ route('kepegawaian.slip-gaji.sync', $slipGaji->hashid) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info" title="Sinkronkan gaji dari Pengaturan Gaji">
                        <i class="bi bi-arrow-repeat me-1"></i> Sync
                    </button>
                </form>
                <form action="{{ route('kepegawaian.slip-gaji.process', $slipGaji->hashid) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-right-circle me-1"></i> Proses
                    </button>
                </form>
                <a href="{{ route('kepegawaian.slip-gaji.edit', $slipGaji->hashid) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            @elseif($slipGaji->status === 'diproses')
                <form action="{{ route('kepegawaian.slip-gaji.sync', $slipGaji->hashid) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info" title="Sinkronkan gaji dari Pengaturan Gaji">
                        <i class="bi bi-arrow-repeat me-1"></i> Sync
                    </button>
                </form>
                <form action="{{ route('kepegawaian.slip-gaji.approve', $slipGaji->hashid) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Setujui
                    </button>
                </form>
                <a href="{{ route('kepegawaian.slip-gaji.edit', $slipGaji->hashid) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            @elseif($slipGaji->status === 'disetujui')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bayarModal">
                    <i class="bi bi-cash me-1"></i> Tandai Dibayar
                </button>
            @endif
            <a href="{{ route('kepegawaian.slip-gaji.cetak', $slipGaji->hashid) }}" class="btn btn-primary" target="_blank">
                <i class="bi bi-printer me-1"></i> Cetak
            </a>
            <a href="{{ route('kepegawaian.slip-gaji.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($pengaturanGaji && $pengaturanGaji->gaji_pokok != $slipGaji->gaji_pokok && in_array($slipGaji->status, ['draft', 'diproses']))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Perhatian!</strong> Gaji pokok di slip ini (<strong>Rp {{ number_format($slipGaji->gaji_pokok, 0, ',', '.') }}</strong>) 
            berbeda dengan Pengaturan Gaji (<strong>Rp {{ number_format($pengaturanGaji->gaji_pokok, 0, ',', '.') }}</strong>).
            <a href="#" onclick="event.preventDefault(); document.getElementById('sync-form').submit();" class="alert-link">Klik Sync</a> untuk menyamakan.
            <form id="sync-form" action="{{ route('kepegawaian.slip-gaji.sync', $slipGaji->hashid) }}" method="POST" class="d-none">@csrf</form>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!$pengaturanGaji && in_array($slipGaji->status, ['draft', 'diproses']))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            Pegawai ini belum memiliki <strong>Pengaturan Gaji</strong>. 
            <a href="{{ route('kepegawaian.slip-gaji.pengaturan.index') }}" class="alert-link">Buat pengaturan</a> untuk memudahkan generate slip gaji.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Info Slip Gaji -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Slip Gaji</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">No. Slip</td>
                            <td><strong>{{ $slipGaji->no_slip }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Periode</td>
                            <td>{{ $slipGaji->periode }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Slip</td>
                            <td>{{ $slipGaji->tanggal_slip->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'diproses' => 'warning',
                                        'disetujui' => 'info',
                                        'dibayar' => 'success',
                                        'dibatalkan' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$slipGaji->status] ?? 'secondary' }}">
                                    {{ ucfirst($slipGaji->status) }}
                                </span>
                            </td>
                        </tr>
                        @if($slipGaji->tanggal_bayar)
                        <tr>
                            <td class="text-muted">Tanggal Bayar</td>
                            <td>{{ $slipGaji->tanggal_bayar->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                        @if($slipGaji->metode_pembayaran)
                        <tr>
                            <td class="text-muted">Metode</td>
                            <td>{{ ucfirst($slipGaji->metode_pembayaran) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Pegawai</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">Nama</td>
                            <td><strong>{{ $slipGaji->nama_pegawai }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe</td>
                            <td>
                                <span class="badge bg-{{ $slipGaji->tipe_pegawai == 'Dosen' ? 'success' : 'info' }}">
                                    {{ $slipGaji->tipe_pegawai }}
                                </span>
                            </td>
                        </tr>
                        @if($slipGaji->dosen)
                        <tr>
                            <td class="text-muted">NIDN</td>
                            <td>{{ $slipGaji->dosen->nidn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Prodi</td>
                            <td>{{ $slipGaji->dosen->programStudi->nama ?? '-' }}</td>
                        </tr>
                        @endif
                        @if($slipGaji->pegawai)
                        <tr>
                            <td class="text-muted">NIP</td>
                            <td>{{ $slipGaji->pegawai->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Unit Kerja</td>
                            <td>{{ $slipGaji->pegawai->unitKerja->nama ?? '-' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Gaji -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Rincian Gaji</h5>
                </div>
                <div class="card-body">
                    <!-- Gaji Pokok -->
                    <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                        <h5 class="mb-0">Gaji Pokok</h5>
                        <h5 class="mb-0">Rp {{ number_format($slipGaji->gaji_pokok, 0, ',', '.') }}</h5>
                    </div>

                    <!-- Pendapatan/Tunjangan -->
                    <h6 class="text-success mb-3"><i class="bi bi-plus-circle me-1"></i> Pendapatan/Tunjangan</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Komponen</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendapatan as $item)
                                <tr>
                                    <td>{{ $item->nama_komponen }}</td>
                                    <td class="text-end text-success">Rp {{ number_format($item->nilai, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada tunjangan</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>Total Tunjangan</th>
                                    <th class="text-end text-success">Rp {{ number_format($slipGaji->total_tunjangan, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Potongan -->
                    <h6 class="text-danger mb-3"><i class="bi bi-dash-circle me-1"></i> Potongan</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Komponen</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($potongan as $item)
                                <tr>
                                    <td>{{ $item->nama_komponen }}</td>
                                    <td class="text-end text-danger">Rp {{ number_format($item->nilai, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada potongan</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>Total Potongan</th>
                                    <th class="text-end text-danger">Rp {{ number_format($slipGaji->total_potongan, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="bg-light p-3 rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Gaji Kotor (Gaji Pokok + Tunjangan)</span>
                            <span>Rp {{ number_format($slipGaji->gaji_kotor, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Potongan</span>
                            <span class="text-danger">- Rp {{ number_format($slipGaji->total_potongan, 0, ',', '.') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0">Gaji Bersih (Take Home Pay)</h5>
                            <h4 class="mb-0 text-success">Rp {{ number_format($slipGaji->gaji_bersih, 0, ',', '.') }}</h4>
                        </div>
                    </div>

                    @if($slipGaji->catatan)
                    <div class="mt-3">
                        <strong>Catatan:</strong>
                        <p class="mb-0 text-muted">{{ $slipGaji->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bayar -->
<div class="modal fade" id="bayarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.slip-gaji.bayar', $slipGaji->hashid) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tandai Sudah Dibayar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_bayar" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="metode_pembayaran" class="form-select" required>
                            <option value="transfer">Transfer Bank</option>
                            <option value="tunai">Tunai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Referensi/Bukti</label>
                        <input type="text" name="no_referensi" class="form-control" placeholder="No. transfer, dll">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Konfirmasi Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
