@extends('layouts.app')

@section('title', 'Daftar Ulang PMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Daftar Ulang PMB</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item active">Daftar Ulang</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('pmb.daftar-ulang.generate') }}" class="btn btn-primary">
                <i class="bi bi-arrow-repeat me-1"></i> Generate Daftar Ulang
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Peserta</h6>
                            <h3 class="mb-0">{{ $summary['total'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-people-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Menunggu Bayar</h6>
                            <h3 class="mb-0">{{ $summary['menunggu_bayar'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-clock-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Sudah Bayar</h6>
                            <h3 class="mb-0">{{ $summary['sudah_bayar'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Menjadi Mahasiswa</h6>
                            <h3 class="mb-0">{{ $summary['menjadi_mahasiswa'] ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-mortarboard-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pmb.daftar-ulang.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Gelombang</label>
                        <select name="gelombang" class="form-select">
                            <option value="">-- Semua --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}" {{ request('gelombang') == $gelombang->id ? 'selected' : '' }}>
                                    {{ $gelombang->periodePmb->nama ?? '' }} - {{ $gelombang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Program Studi</label>
                        <select name="prodi" class="form-select">
                            <option value="">-- Semua --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">-- Semua --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Bayar</option>
                            <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="verifikasi_dokumen" {{ request('status') == 'verifikasi_dokumen' ? 'selected' : '' }}>Verifikasi Dokumen</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Nama / No. Daftar Ulang" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('pmb.daftar-ulang.index') }}" class="btn btn-secondary">
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
            <span><i class="bi bi-list-ul me-2"></i>Daftar Peserta Daftar Ulang</span>
            <form action="{{ route('pmb.daftar-ulang.batch-proses') }}" method="POST" id="batchForm">
                @csrf
                <input type="hidden" name="ids" id="selectedIds">
                <button type="submit" class="btn btn-sm btn-success" id="btnBatchProses" disabled>
                    <i class="bi bi-mortarboard me-1"></i> Jadikan Mahasiswa Terpilih
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>No. Daftar Ulang</th>
                            <th>Nama</th>
                            <th>Prodi Diterima</th>
                            <th class="text-end">Biaya</th>
                            <th class="text-center">Batas Waktu</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarUlangs as $du)
                        <tr>
                            <td>
                                @if($du->status == 'lunas')
                                <input type="checkbox" class="item-checkbox" value="{{ $du->hashid }}">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pmb.daftar-ulang.show', $du->hashid) }}">
                                    <strong>{{ $du->no_daftar_ulang }}</strong>
                                </a>
                            </td>
                            <td>
                                {{ $du->calonMahasiswa->nama_lengkap ?? 'N/A' }}
                                <br><small class="text-muted">{{ $du->calonMahasiswa->no_pendaftaran ?? '' }}</small>
                            </td>
                            <td>{{ $du->programStudi->nama ?? '-' }}</td>
                            <td class="text-end">Rp {{ number_format($du->biaya_daftar_ulang, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($du->tanggal_expired)
                                    {{ $du->tanggal_expired->format('d/m/Y') }}
                                    @if($du->tanggal_expired->isPast() && !in_array($du->status, ['lunas', 'selesai']))
                                        <br><span class="badge bg-danger">Expired</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $du->status_badge }}">{{ $du->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('pmb.daftar-ulang.show', $du->hashid) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($du->status == 'lunas')
                                    <form action="{{ route('pmb.daftar-ulang.proses-mahasiswa', $du->hashid) }}" method="POST" class="d-inline" onsubmit="return confirm('Jadikan sebagai mahasiswa?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Jadikan Mahasiswa">
                                            <i class="bi bi-mortarboard"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data daftar ulang
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">Menampilkan {{ $daftarUlangs->firstItem() ?? 0 }} - {{ $daftarUlangs->lastItem() ?? 0 }} dari {{ $daftarUlangs->total() }} data</small>
                </div>
                {{ $daftarUlangs->withQueryString()->links() }}
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
        const btnBatchProses = document.getElementById('btnBatchProses');
        const selectedIdsInput = document.getElementById('selectedIds');

        function updateBatchButton() {
            const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            if (btnBatchProses) {
                btnBatchProses.disabled = checkedBoxes.length === 0;
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
