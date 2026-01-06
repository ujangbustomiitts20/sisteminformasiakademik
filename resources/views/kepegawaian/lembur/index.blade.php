@extends('layouts.app')

@section('title', 'Lembur Pegawai')

@section('content')
<div class="page-title">
    <h4>Lembur Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Lembur</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Pengajuan</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-clock-history fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Menunggu</h6>
                        <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Disetujui</h6>
                        <h3 class="mb-0">{{ $stats['disetujui'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Jam</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_jam'] ?? 0, 1) }}</h3>
                    </div>
                    <i class="bi bi-stopwatch fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions & Filters -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Ajukan Lembur
                </button>
                <a href="{{ route('kepegawaian.lembur.tarif.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-currency-dollar me-1"></i> Kelola Tarif
                </a>
                <a href="{{ route('kepegawaian.lembur.laporan') }}" class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-text me-1"></i> Laporan
                </a>
            </div>
            <div class="col-md-6">
                <form action="{{ route('kepegawaian.lembur.index') }}" method="GET" class="d-flex gap-2 justify-content-end align-items-center">
                    <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <input type="month" name="bulan" class="form-control form-control-sm" style="width: auto;" value="{{ request('bulan') }}" onchange="this.form.submit()" placeholder="Semua Bulan">
                    @if(request('status') || request('bulan'))
                    <a href="{{ route('kepegawaian.lembur.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Durasi</th>
                        <th>Jenis Hari</th>
                        <th>Total Upah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lemburList as $i => $lembur)
                        <tr>
                            <td>{{ $lemburList->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $lembur->nama_pegawai }}</strong><br>
                                <small class="text-muted">{{ $lembur->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                            </td>
                            <td>{{ $lembur->tanggal->format('d/m/Y') }}</td>
                            <td>
                                {{ $lembur->jam_mulai }} - {{ $lembur->jam_selesai }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ number_format($lembur->total_jam, 1) }} jam</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $lembur->jenis_hari_color }}">{{ $lembur->jenis_hari_label }}</span>
                            </td>
                            <td class="fw-bold text-primary">{{ format_rupiah($lembur->total_bayar ?? 0) }}</td>
                            <td>
                                <span class="badge bg-{{ $lembur->status_color }}">{{ $lembur->full_status_label ?? $lembur->status_label }}</span>
                                @if($lembur->status_kaprodi === 'disetujui' && $lembur->status === 'menunggu_admin')
                                    <br><small class="text-success"><i class="bi bi-check-circle"></i> Kaprodi OK</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('kepegawaian.lembur.show', $lembur) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array($lembur->status, ['diajukan', 'menunggu_admin']))
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $lembur->id }}" title="Setujui">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $lembur->id }}" title="Tolak">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    @endif
                                    @if($lembur->status == 'disetujui')
                                    <form action="{{ route('kepegawaian.lembur.selesai', $lembur) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning" title="Selesaikan" onclick="return confirm('Tandai selesai?')">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>

                                @if(in_array($lembur->status, ['diajukan', 'menunggu_admin']))
                                <!-- Approve Modal -->
                                <div class="modal fade" id="approveModal{{ $lembur->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('kepegawaian.lembur.approve', $lembur) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Setujui Lembur</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Setujui lembur untuk <strong>{{ $lembur->nama_pegawai }}</strong>?</p>
                                                    <table class="table table-sm table-borderless">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <td>: {{ $lembur->tanggal->format('d/m/Y') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Durasi</th>
                                                            <td>: {{ number_format($lembur->durasi_jam, 1) }} jam</td>
                                                        </tr>
                                                    </table>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tarif Per Jam <span class="text-danger">*</span></label>
                                                        <input type="number" name="tarif_per_jam" class="form-control" value="{{ $lembur->tarif_per_jam ?? 50000 }}" required min="0">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan (opsional)</label>
                                                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="bi bi-check-lg me-1"></i> Setujui
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $lembur->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('kepegawaian.lembur.reject', $lembur) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Tolak Lembur</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Tolak lembur untuk <strong>{{ $lembur->nama_pegawai }}</strong>?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                        <textarea name="catatan" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-x-lg me-1"></i> Tolak
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada data lembur</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $lemburList->links() }}
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.lembur.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Ajukan Lembur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" id="tipePegawai" class="form-select" required onchange="togglePegawaiSelect(this.value)">
                                <option value="">Pilih Tipe</option>
                                <option value="dosen">Dosen</option>
                                <option value="pegawai">Tenaga Kependidikan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="dosenWrapper" style="display: none;">
                            <label class="form-label">Pilih Dosen <span class="text-danger">*</span></label>
                            <select name="dosen_id" id="dosenId" class="form-select">
                                <option value="">Pilih Dosen</option>
                                @foreach($dosenList as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="pegawaiWrapper" style="display: none;">
                            <label class="form-label">Pilih Pegawai <span class="text-danger">*</span></label>
                            <select name="pegawai_id" id="pegawaiId" class="form-select">
                                <option value="">Pilih Pegawai</option>
                                @foreach($pegawaiList as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Hari <span class="text-danger">*</span></label>
                            <select name="jenis_hari" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="biasa">Hari Biasa</option>
                                <option value="weekend">Weekend</option>
                                <option value="libur_nasional">Libur Nasional</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alasan Lembur <span class="text-danger">*</span></label>
                            <textarea name="alasan" class="form-control" rows="3" required placeholder="Jelaskan pekerjaan yang dilakukan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Ajukan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePegawaiSelect(value) {
    const dosenWrapper = document.getElementById('dosenWrapper');
    const pegawaiWrapper = document.getElementById('pegawaiWrapper');
    
    if (value === 'dosen') {
        dosenWrapper.style.display = 'block';
        pegawaiWrapper.style.display = 'none';
    } else if (value === 'pegawai') {
        dosenWrapper.style.display = 'none';
        pegawaiWrapper.style.display = 'block';
    } else {
        dosenWrapper.style.display = 'none';
        pegawaiWrapper.style.display = 'none';
    }
}
</script>
@endpush
