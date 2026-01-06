@extends('layouts.app')

@section('title', 'Pelanggaran & Sanksi')

@section('content')
<div class="page-title">
    <h4>Pelanggaran & Sanksi Pegawai</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Pelanggaran & Sanksi</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Pelanggaran</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-exclamation-octagon fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Menunggu Proses</h6>
                        <h3 class="mb-0">{{ $stats['dilaporkan'] ?? 0 }}</h3>
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
                        <h6 class="mb-0">Terbukti</h6>
                        <h3 class="mb-0">{{ $stats['terbukti'] ?? 0 }}</h3>
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
                        <h6 class="mb-0">Investigasi</h6>
                        <h3 class="mb-0">{{ $stats['investigasi'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-shield-exclamation fs-1 opacity-50"></i>
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
                    <i class="bi bi-plus-lg me-1"></i> Catat Pelanggaran
                </button>
                <a href="{{ route('kepegawaian.pelanggaran.jenis.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-tags me-1"></i> Kelola Jenis Pelanggaran
                </a>
            </div>
            <div class="col-md-6">
                <form action="{{ route('kepegawaian.pelanggaran.index') }}" method="GET" class="d-flex gap-2 justify-content-end">
                    <select name="jenis_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisPelanggaranList as $jenis)
                            <option value="{{ $jenis->id }}" {{ request('jenis_id') == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama }}
                            </option>
                        @endforeach
                    </select>
                    <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="dilaporkan" {{ request('status') == 'dilaporkan' ? 'selected' : '' }}>Dilaporkan</option>
                        <option value="investigasi" {{ request('status') == 'investigasi' ? 'selected' : '' }}>Investigasi</option>
                        <option value="terbukti" {{ request('status') == 'terbukti' ? 'selected' : '' }}>Terbukti</option>
                        <option value="tidak_terbukti" {{ request('status') == 'tidak_terbukti' ? 'selected' : '' }}>Tidak Terbukti</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
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
                        <th>Jenis Pelanggaran</th>
                        <th>Tanggal</th>
                        <th>Tingkat</th>
                        <th>Status</th>
                        <th>Sanksi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggaranList as $i => $pelanggaran)
                        <tr>
                            <td>{{ $pelanggaranList->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $pelanggaran->nama_pegawai }}</strong><br>
                                <small class="text-muted">{{ $pelanggaran->dosen_id ? 'Dosen' : 'Tenaga Kependidikan' }}</small>
                            </td>
                            <td>{{ $pelanggaran->jenisPelanggaran->nama ?? '-' }}</td>
                            <td>{{ $pelanggaran->tanggal_kejadian->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $pelanggaran->tingkat_color }}">{{ $pelanggaran->tingkat_label }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $pelanggaran->status_color }}">{{ $pelanggaran->status_label }}</span>
                            </td>
                            <td>
                                @if($pelanggaran->sanksi)
                                    <span class="badge bg-dark">{{ $pelanggaran->sanksi->jenis_sanksi_label }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('kepegawaian.pelanggaran.show', $pelanggaran) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($pelanggaran->status == 'terbukti' && !$pelanggaran->sanksi)
                                    <button type="button" class="btn btn-warning" onclick="berikanSanksi({{ $pelanggaran->id }}, '{{ $pelanggaran->hashid }}')" title="Beri Sanksi">
                                        <i class="bi bi-shield-exclamation"></i>
                                    </button>
                                    @endif
                                    <form action="{{ route('kepegawaian.pelanggaran.destroy', $pelanggaran) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Hapus" onclick="return confirm('Yakin hapus data ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada data pelanggaran</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pelanggaranList->links() }}
    </div>
</div>

<!-- Modal Tambah Pelanggaran -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.pelanggaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Catat Pelanggaran Pegawai</h5>
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
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="pegawaiWrapper" style="display: none;">
                            <label class="form-label">Pilih Pegawai <span class="text-danger">*</span></label>
                            <select name="pegawai_id" id="pegawaiId" class="form-select">
                                <option value="">Pilih Pegawai</option>
                                @foreach($pegawaiList as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Pelanggaran <span class="text-danger">*</span></label>
                            <select name="jenis_pelanggaran_id" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                @foreach($jenisPelanggaranList as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Kejadian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pelanggaran" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">File Bukti (PDF)</label>
                            <input type="file" name="file_bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Berikan Sanksi -->
<div class="modal fade" id="modalSanksi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="formSanksi" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Berikan Sanksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Sanksi <span class="text-danger">*</span></label>
                        <select name="jenis_sanksi" class="form-select" required>
                            <option value="">Pilih Jenis</option>
                            <option value="teguran_lisan">Teguran Lisan</option>
                            <option value="teguran_tertulis">Teguran Tertulis</option>
                            <option value="sp1">Surat Peringatan 1</option>
                            <option value="sp2">Surat Peringatan 2</option>
                            <option value="sp3">Surat Peringatan 3</option>
                            <option value="demosi">Demosi</option>
                            <option value="mutasi">Mutasi</option>
                            <option value="skorsing">Skorsing</option>
                            <option value="phk">PHK</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor SK</label>
                        <input type="text" name="nomor_sk" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File SK (PDF)</label>
                        <input type="file" name="file_sk" class="form-control" accept=".pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Berikan Sanksi</button>
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

function berikanSanksi(id, hashid) {
    document.getElementById('formSanksi').action = '{{ url("kepegawaian/pelanggaran") }}/' + hashid + '/sanksi';
    new bootstrap.Modal(document.getElementById('modalSanksi')).show();
}
</script>
@endpush
