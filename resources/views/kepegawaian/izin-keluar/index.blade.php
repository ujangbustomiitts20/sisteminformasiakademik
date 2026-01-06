@extends('layouts.app')

@section('title', 'Izin Keluar')

@section('content')
<div class="page-title">
    <h4>Izin Keluar Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Izin Keluar</li>
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
                    <i class="bi bi-door-open fs-1 opacity-50"></i>
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
                        <h6 class="mb-0">Sedang Keluar</h6>
                        <h3 class="mb-0">{{ $stats['sedang_keluar'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-person-walking fs-1 opacity-50"></i>
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
                    <i class="bi bi-plus-lg me-1"></i> Ajukan Izin Keluar
                </button>
            </div>
            <div class="col-md-6">
                <form action="{{ route('kepegawaian.izin-keluar.index') }}" method="GET" class="d-flex gap-2 justify-content-end">
                    <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <input type="date" name="tanggal" class="form-control form-control-sm" style="width: auto;" value="{{ request('tanggal') }}" onchange="this.form.submit()">
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
                        <th>Jam Keluar</th>
                        <th>Jam Kembali</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinList as $i => $izin)
                        <tr>
                            <td>{{ $izinList->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $izin->nama_pegawai }}</strong><br>
                                <small class="text-muted">{{ $izin->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                            </td>
                            <td>{{ $izin->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $izin->jam_keluar }}</td>
                            <td>
                                @if($izin->jam_kembali_aktual)
                                    {{ $izin->jam_kembali_aktual }}
                                    <br><small class="text-muted">Est: {{ $izin->jam_kembali }}</small>
                                @else
                                    {{ $izin->jam_kembali }}
                                @endif
                            </td>
                            <td>{{ Str::limit($izin->keperluan, 30) }}</td>
                            <td>
                                <span class="badge bg-{{ $izin->status_color }}">{{ $izin->full_status_label }}</span>
                                @if($izin->status_kaprodi === 'disetujui' && $izin->status === 'menunggu_admin')
                                    <br><small class="text-success"><i class="bi bi-check-circle"></i> Kaprodi OK</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('kepegawaian.izin-keluar.show', $izin) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array($izin->status, ['diajukan', 'menunggu_admin']))
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $izin->id }}" title="Setujui">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $izin->id }}" title="Tolak">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    @endif
                                    @if($izin->status == 'disetujui')
                                    <button type="button" class="btn btn-warning" onclick="selesaikanIzin('{{ $izin->hashid }}')" title="Selesaikan">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                    @endif
                                </div>

                                @if(in_array($izin->status, ['diajukan', 'menunggu_admin']))
                                <!-- Approve Modal -->
                                <div class="modal fade" id="approveModal{{ $izin->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('kepegawaian.izin-keluar.approve', $izin) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Setujui Izin Keluar</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Setujui izin keluar untuk <strong>{{ $izin->nama_pegawai }}</strong>?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan (opsional)</label>
                                                        <textarea name="catatan" class="form-control" rows="3"></textarea>
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
                                <div class="modal fade" id="rejectModal{{ $izin->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('kepegawaian.izin-keluar.reject', $izin) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Tolak Izin Keluar</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Tolak izin keluar untuk <strong>{{ $izin->nama_pegawai }}</strong>?</p>
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
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada data izin keluar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $izinList->links() }}
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.izin-keluar.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Ajukan Izin Keluar</h5>
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
                            <input type="date" name="tanggal" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Keluar <span class="text-danger">*</span></label>
                            <input type="time" name="jam_keluar" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estimasi Jam Kembali <span class="text-danger">*</span></label>
                            <input type="time" name="jam_kembali" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                            <textarea name="keperluan" class="form-control" rows="3" required placeholder="Jelaskan keperluan izin keluar"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" class="form-control" placeholder="Alamat/lokasi tujuan">
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

<!-- Modal Selesaikan -->
<div class="modal fade" id="modalSelesai" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="formSelesai">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Izin Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jam Kembali Aktual <span class="text-danger">*</span></label>
                        <input type="time" name="jam_kembali_aktual" class="form-control" required value="{{ date('H:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan</button>
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

function selesaikanIzin(hashid) {
    document.getElementById('formSelesai').action = '{{ url("kepegawaian/izin-keluar") }}/' + hashid + '/selesai';
    new bootstrap.Modal(document.getElementById('modalSelesai')).show();
}
</script>
@endpush
