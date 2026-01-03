@extends('layouts.app')

@section('title', 'Pembayaran PMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pembayaran PMB</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item active">Pembayaran</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Tagihan</h6>
                            <h3 class="mb-0">{{ $totalTagihan ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-receipt fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Menunggu Verifikasi</h6>
                            <h3 class="mb-0">{{ $menungguVerifikasi ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-clock-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Terverifikasi</h6>
                            <h3 class="mb-0">{{ $terverifikasi ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Pendapatan</h6>
                            <h3 class="mb-0">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pmb.pembayaran.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Gelombang</label>
                        <select name="gelombang" class="form-select">
                            <option value="">-- Semua Gelombang --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}" {{ request('gelombang') == $gelombang->id ? 'selected' : '' }}>
                                    {{ $gelombang->nama }} - {{ $gelombang->periodePmb->nama ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jenis Pembayaran</label>
                        <select name="jenis" class="form-select">
                            <option value="">-- Semua --</option>
                            <option value="pendaftaran" {{ request('jenis') == 'pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                            <option value="daftar_ulang" {{ request('jenis') == 'daftar_ulang' ? 'selected' : '' }}>Daftar Ulang</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">-- Semua --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="terverifikasi" {{ request('status') == 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="No. Pembayaran / Nama" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('pmb.pembayaran.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul me-2"></i>Daftar Pembayaran</span>
            @if(request()->has('status') && request('status') == 'menunggu_verifikasi')
            <form action="{{ route('pmb.pembayaran.batch-verifikasi') }}" method="POST" id="batchForm">
                @csrf
                <input type="hidden" name="ids" id="selectedIds">
                <input type="hidden" name="status" value="terverifikasi">
                <button type="submit" class="btn btn-sm btn-success" id="btnBatchVerify" disabled>
                    <i class="bi bi-check-all me-1"></i> Verifikasi Terpilih
                </button>
            </form>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            @if(request()->has('status') && request('status') == 'menunggu_verifikasi')
                            <th><input type="checkbox" id="selectAll"></th>
                            @endif
                            <th>No. Pembayaran</th>
                            <th>Calon Mahasiswa</th>
                            <th>Jenis</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Tanggal Bayar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $pembayaran)
                        <tr>
                            @if(request()->has('status') && request('status') == 'menunggu_verifikasi')
                            <td>
                                <input type="checkbox" class="item-checkbox" value="{{ $pembayaran->hashid }}">
                            </td>
                            @endif
                            <td>
                                <a href="{{ route('pmb.pembayaran.show', $pembayaran->hashid) }}">
                                    <strong>{{ $pembayaran->no_pembayaran }}</strong>
                                </a>
                            </td>
                            <td>
                                {{ $pembayaran->calonMahasiswa->nama_lengkap ?? 'N/A' }}
                                <br><small class="text-muted">{{ $pembayaran->calonMahasiswa->no_pendaftaran ?? '' }}</small>
                            </td>
                            <td>
                                @if($pembayaran->jenis_pembayaran == 'pendaftaran')
                                    <span class="badge bg-primary">Pendaftaran</span>
                                @else
                                    <span class="badge bg-info">Daftar Ulang</span>
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">
                                {{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $pembayaran->status_badge }}">{{ $pembayaran->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('pmb.pembayaran.show', $pembayaran->hashid) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($pembayaran->status == 'menunggu_verifikasi')
                                    <form action="{{ route('pmb.pembayaran.verifikasi', $pembayaran->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Verifikasi pembayaran ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Verifikasi">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Tidak ada data pembayaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">Menampilkan {{ $pembayarans->firstItem() ?? 0 }} - {{ $pembayarans->lastItem() ?? 0 }} dari {{ $pembayarans->total() }} data</small>
                </div>
                {{ $pembayarans->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const btnBatchVerify = document.getElementById('btnBatchVerify');
        const selectedIdsInput = document.getElementById('selectedIds');

        function updateBatchButton() {
            const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            if (btnBatchVerify) {
                btnBatchVerify.disabled = checkedBoxes.length === 0;
                selectedIdsInput.value = Array.from(checkedBoxes).map(cb => cb.value).join(',');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                itemCheckboxes.forEach(cb => cb.checked = this.checked);
                updateBatchButton();
            });
        }

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBatchButton);
        });
    });
</script>
@endpush
