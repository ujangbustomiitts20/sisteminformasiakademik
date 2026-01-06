@extends('layouts.app')

@section('title', 'Slip Gaji')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Slip Gaji</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
                    <li class="breadcrumb-item active">Slip Gaji</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('kepegawaian.slip-gaji.komponen.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-gear me-1"></i> Komponen Gaji
            </a>
            <a href="{{ route('kepegawaian.slip-gaji.pengaturan.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-sliders me-1"></i> Pengaturan Gaji
            </a>
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="bi bi-lightning me-1"></i> Generate Massal
            </button>
            <a href="{{ route('kepegawaian.slip-gaji.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah Slip Gaji
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

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Total</h6>
                    <h3 class="mb-0 text-primary">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Draft</h6>
                    <h3 class="mb-0 text-secondary">{{ $stats['draft'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Diproses</h6>
                    <h3 class="mb-0 text-warning">{{ $stats['diproses'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Disetujui</h6>
                    <h3 class="mb-0 text-info">{{ $stats['disetujui'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Dibayar</h6>
                    <h3 class="mb-0 text-success">{{ $stats['dibayar'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Total Gaji</h6>
                    <h5 class="mb-0 text-success">Rp {{ number_format($stats['total_gaji'], 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('tahun', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @php
                            $bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        @endphp
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ $bulanNames[$i-1] }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipe Pegawai</label>
                    <select name="tipe" class="form-select">
                        <option value="">Semua</option>
                        <option value="dosen" {{ request('tipe') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="tendik" {{ request('tipe') == 'tendik' ? 'selected' : '' }}>Tendik</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="No slip, nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No Slip</th>
                            <th>Pegawai</th>
                            <th>Periode</th>
                            <th class="text-end">Gaji Pokok</th>
                            <th class="text-end">Tunjangan</th>
                            <th class="text-end">Potongan</th>
                            <th class="text-end">Gaji Bersih</th>
                            <th>Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slipGajis as $slip)
                        <tr>
                            <td><strong>{{ $slip->no_slip }}</strong></td>
                            <td>
                                {{ $slip->nama_pegawai }}
                                <br><span class="badge bg-{{ $slip->tipe_pegawai == 'Dosen' ? 'success' : 'info' }} bg-opacity-10 text-{{ $slip->tipe_pegawai == 'Dosen' ? 'success' : 'info' }}">{{ $slip->tipe_pegawai }}</span>
                            </td>
                            <td>{{ $slip->periode }}</td>
                            <td class="text-end">Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</td>
                            <td class="text-end text-success">+Rp {{ number_format($slip->total_tunjangan, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">-Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}</td>
                            <td class="text-end"><strong>Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}</strong></td>
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
                                <span class="badge bg-{{ $statusColors[$slip->status] ?? 'secondary' }}">
                                    {{ ucfirst($slip->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('kepegawaian.slip-gaji.show', $slip->hashid) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array($slip->status, ['draft', 'diproses']))
                                    <a href="{{ route('kepegawaian.slip-gaji.edit', $slip->hashid) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('kepegawaian.slip-gaji.cetak', $slip->hashid) }}" class="btn btn-sm btn-outline-primary" title="Cetak" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mb-0">Tidak ada data slip gaji</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $slipGajis->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate Massal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.slip-gaji.generate-massal') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Slip Gaji Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Slip gaji akan di-generate untuk semua pegawai aktif yang belum memiliki slip gaji di periode ini.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <select name="tahun" class="form-select" required>
                            @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bulan <span class="text-danger">*</span></label>
                        <select name="bulan" class="form-select" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>{{ $bulanNames[$i-1] }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-select" required>
                            <option value="semua">Semua (Dosen & Tendik)</option>
                            <option value="dosen">Dosen Saja</option>
                            <option value="tendik">Tendik Saja</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnGenerateSlip">
                        <i class="bi bi-lightning me-1"></i> Generate
                    </button>
                    <button type="button" class="btn btn-success disabled" id="btnGenerateSlipLoading" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Memproses...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Loading state untuk Generate Slip Gaji Massal
    const generateSlipForm = document.querySelector('#generateModal form');
    const btnGenerateSlip = document.getElementById('btnGenerateSlip');
    const btnGenerateSlipLoading = document.getElementById('btnGenerateSlipLoading');

    generateSlipForm?.addEventListener('submit', function(e) {
        // Tampilkan loading
        btnGenerateSlip.style.display = 'none';
        btnGenerateSlipLoading.style.display = 'inline-block';
        
        // Disable semua input dalam form
        const inputs = generateSlipForm.querySelectorAll('input, select, button');
        inputs.forEach(input => input.disabled = true);
        btnGenerateSlipLoading.disabled = false;
    });
});
</script>
@endpush
@endsection
