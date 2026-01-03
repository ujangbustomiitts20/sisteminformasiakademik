@extends('layouts.app')

@section('title', 'Detail Periode Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $periodeUjian->nama }}</h1>
            <p class="text-muted mb-0">{{ $periodeUjian->tahunAkademik->nama ?? '-' }}</p>
        </div>
        <div>
            <form action="{{ route('admin.periode-ujian.generate-kartu', $periodeUjian->hashid) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-card-checklist me-1"></i>Generate Kartu Ujian
                </button>
            </form>
            <a href="{{ route('admin.periode-ujian.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
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

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Kartu</h6>
                            <h2 class="mb-0">{{ number_format($statistik['total_kartu']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-card-checklist fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Eligible</h6>
                            <h2 class="mb-0">{{ number_format($statistik['eligible']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Tidak Eligible</h6>
                            <h2 class="mb-0">{{ number_format($statistik['tidak_eligible']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Sudah Cetak</h6>
                            <h2 class="mb-0">{{ number_format($statistik['sudah_cetak']) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-printer fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Periode -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Periode</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Jenis Ujian</td>
                            <td><span class="badge bg-info">{{ $periodeUjian->jenis }}</span></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td><strong>{{ $periodeUjian->tanggal_mulai->format('d M Y') }} - {{ $periodeUjian->tanggal_selesai->format('d M Y') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal Cetak Kartu</td>
                            <td>{{ $periodeUjian->tanggal_cetak_kartu?->format('d M Y') ?? 'Kapan saja' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Minimal Kehadiran</td>
                            <td><strong>{{ $periodeUjian->minimal_kehadiran }}%</strong></td>
                        </tr>
                        <tr>
                            <td>Cek Pembayaran</td>
                            <td>
                                @if($periodeUjian->cek_pembayaran)
                                    <span class="badge bg-success">Ya</span>
                                @else
                                    <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                @if($periodeUjian->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($periodeUjian->status == 'selesai')
                                    <span class="badge bg-secondary">Selesai</span>
                                @else
                                    <span class="badge bg-warning">Draft</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Kartu Ujian -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-card-list me-2"></i>Daftar Kartu Ujian</h5>
            <form action="{{ route('admin.kartu-ujian.cetak-batch', $periodeUjian->hashid) }}" method="POST" id="formCetakBatch">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary" disabled id="btnCetakBatch">
                    <i class="bi bi-printer me-1"></i>Cetak Terpilih
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="checkAll" class="form-check-input">
                            </th>
                            <th>No. Kartu</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Prodi</th>
                            <th>Kehadiran</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kartuUjian as $kartu)
                        <tr>
                            <td>
                                @if($kartu->eligible)
                                <input type="checkbox" name="mahasiswa_ids[]" value="{{ $kartu->id }}" 
                                       class="form-check-input check-item" form="formCetakBatch">
                                @endif
                            </td>
                            <td><code>{{ $kartu->nomor_kartu }}</code></td>
                            <td>{{ $kartu->mahasiswa->nim ?? '-' }}</td>
                            <td>{{ $kartu->mahasiswa->nama ?? '-' }}</td>
                            <td>{{ $kartu->mahasiswa->programStudi->nama ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $kartu->persentase_kehadiran >= $periodeUjian->minimal_kehadiran ? 'success' : 'danger' }}">
                                    {{ number_format($kartu->persentase_kehadiran, 1) }}%
                                </span>
                            </td>
                            <td>
                                @if($kartu->pembayaran_lunas)
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-danger">Belum Lunas</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $kartu->status_badge }}">{{ ucfirst($kartu->status) }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($kartu->eligible)
                                        <a href="{{ route('admin.kartu-ujian.cetak', $kartu->hashid) }}" class="btn btn-outline-primary" title="Cetak">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    @endif
                                    @if(!$kartu->eligible)
                                        <form action="{{ route('admin.kartu-ujian.approve', $kartu->hashid) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Approve Manual">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-outline-danger" title="Revoke" 
                                                data-bs-toggle="modal" data-bs-target="#revokeModal{{ $kartu->id }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Revoke Modal -->
                                <div class="modal fade" id="revokeModal{{ $kartu->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.kartu-ujian.revoke', $kartu->hashid) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Revoke Kartu Ujian</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Revoke kartu ujian <strong>{{ $kartu->mahasiswa->nama ?? '' }}</strong>?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan</label>
                                                        <input type="text" name="alasan" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Revoke</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada kartu ujian. Klik "Generate Kartu Ujian" untuk membuat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $kartuUjian->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.check-item').forEach(cb => cb.checked = this.checked);
    updateCetakButton();
});

document.querySelectorAll('.check-item').forEach(cb => {
    cb.addEventListener('change', updateCetakButton);
});

function updateCetakButton() {
    const checked = document.querySelectorAll('.check-item:checked').length;
    document.getElementById('btnCetakBatch').disabled = checked === 0;
}
</script>
@endpush
@endsection
