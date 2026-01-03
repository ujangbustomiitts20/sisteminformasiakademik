@extends('layouts.app')

@section('title', 'Data Kepegawaian')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <div>
        <h4>Data Kepegawaian</h4>
        <p class="text-muted mb-0">Kelola data kepegawaian Anda secara mandiri</p>
    </div>
    <a href="{{ route('dosen.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
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

<!-- Nav Tabs -->
<ul class="nav nav-tabs mb-4" id="kepegawaianTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pendidikan-tab" data-bs-toggle="tab" data-bs-target="#pendidikan" type="button">
            <i class="bi bi-mortarboard me-1"></i>Pendidikan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="jabatan-tab" data-bs-toggle="tab" data-bs-target="#jabatan" type="button">
            <i class="bi bi-briefcase me-1"></i>Jabatan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pangkat-tab" data-bs-toggle="tab" data-bs-target="#pangkat" type="button">
            <i class="bi bi-award me-1"></i>Pangkat
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pelatihan-tab" data-bs-toggle="tab" data-bs-target="#pelatihan" type="button">
            <i class="bi bi-journal-check me-1"></i>Pelatihan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen" type="button">
            <i class="bi bi-file-earmark me-1"></i>Dokumen
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="kepegawaianTabContent">
    <!-- Tab Pendidikan -->
    <div class="tab-pane fade show active" id="pendidikan" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <span><i class="bi bi-mortarboard me-2"></i>Riwayat Pendidikan</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPendidikan">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenjang</th>
                                <th>Institusi</th>
                                <th>Program Studi</th>
                                <th>Tahun</th>
                                <th>IPK</th>
                                <th>File</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dosen->riwayatPendidikan as $pendidikan)
                            <tr>
                                <td><span class="badge bg-primary">{{ $pendidikan->jenjang }}</span></td>
                                <td>{{ $pendidikan->nama_institusi }}</td>
                                <td>{{ $pendidikan->program_studi }}</td>
                                <td>{{ $pendidikan->tahun_masuk }} - {{ $pendidikan->tahun_lulus ?? 'Sekarang' }}</td>
                                <td>{{ $pendidikan->ipk ?? '-' }}</td>
                                <td>
                                    @if($pendidikan->file_ijazah)
                                    <a href="{{ Storage::url($pendidikan->file_ijazah) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning" onclick="editPendidikan({{ json_encode($pendidikan) }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('dosen.kepegawaian.pendidikan.destroy', $pendidikan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data pendidikan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Jabatan -->
    <div class="tab-pane fade" id="jabatan" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <span><i class="bi bi-briefcase me-2"></i>Riwayat Jabatan</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalJabatan">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Jabatan</th>
                                <th>Jenis</th>
                                <th>TMT Jabatan</th>
                                <th>No SK</th>
                                <th>File</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dosen->riwayatJabatan as $jabatan)
                            <tr>
                                <td>{{ $jabatan->nama_jabatan }}</td>
                                <td><span class="badge bg-{{ $jabatan->jenis_jabatan == 'Struktural' ? 'info' : 'success' }}">{{ $jabatan->jenis_jabatan }}</span></td>
                                <td>{{ $jabatan->tmt_jabatan ? \Carbon\Carbon::parse($jabatan->tmt_jabatan)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $jabatan->no_sk ?? '-' }}</td>
                                <td>
                                    @if($jabatan->file_sk)
                                    <a href="{{ Storage::url($jabatan->file_sk) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning" onclick="editJabatan({{ json_encode($jabatan) }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('dosen.kepegawaian.jabatan.destroy', $jabatan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data jabatan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Pangkat -->
    <div class="tab-pane fade" id="pangkat" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <span><i class="bi bi-award me-2"></i>Riwayat Pangkat/Golongan</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPangkat">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Golongan</th>
                                <th>Pangkat</th>
                                <th>TMT Pangkat</th>
                                <th>No SK</th>
                                <th>Masa Kerja</th>
                                <th>File</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dosen->riwayatPangkat as $pangkat)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $pangkat->golongan }}</span></td>
                                <td>{{ $pangkat->pangkat }}</td>
                                <td>{{ $pangkat->tmt_pangkat ? \Carbon\Carbon::parse($pangkat->tmt_pangkat)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $pangkat->no_sk ?? '-' }}</td>
                                <td>{{ $pangkat->masa_kerja_tahun ?? 0 }} thn {{ $pangkat->masa_kerja_bulan ?? 0 }} bln</td>
                                <td>
                                    @if($pangkat->file_sk)
                                    <a href="{{ Storage::url($pangkat->file_sk) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning" onclick="editPangkat({{ json_encode($pangkat) }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('dosen.kepegawaian.pangkat.destroy', $pangkat) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data pangkat</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Pelatihan -->
    <div class="tab-pane fade" id="pelatihan" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <span><i class="bi bi-journal-check me-2"></i>Riwayat Pelatihan</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPelatihan">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Pelatihan</th>
                                <th>Jenis</th>
                                <th>Penyelenggara</th>
                                <th>Tanggal</th>
                                <th>JP</th>
                                <th>File</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dosen->riwayatPelatihan as $pelatihan)
                            <tr>
                                <td>{{ $pelatihan->nama_pelatihan }}</td>
                                <td><span class="badge bg-info">{{ $pelatihan->jenis_pelatihan }}</span></td>
                                <td>{{ $pelatihan->penyelenggara }}</td>
                                <td>
                                    {{ $pelatihan->tanggal_mulai ? \Carbon\Carbon::parse($pelatihan->tanggal_mulai)->format('d/m/Y') : '-' }}
                                    @if($pelatihan->tanggal_selesai)
                                    - {{ \Carbon\Carbon::parse($pelatihan->tanggal_selesai)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>{{ $pelatihan->jam_pelatihan ?? '-' }}</td>
                                <td>
                                    @if($pelatihan->file_sertifikat)
                                    <a href="{{ Storage::url($pelatihan->file_sertifikat) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning" onclick="editPelatihan({{ json_encode($pelatihan) }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('dosen.kepegawaian.pelatihan.destroy', $pelatihan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data pelatihan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Dokumen -->
    <div class="tab-pane fade" id="dokumen" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <span><i class="bi bi-file-earmark me-2"></i>Dokumen Kepegawaian</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDokumen">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis</th>
                                <th>Nama Dokumen</th>
                                <th>No Dokumen</th>
                                <th>Tgl Terbit</th>
                                <th>File</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dosen->dokumenKepegawaian as $dokumen)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $dokumen->jenis_dokumen }}</span></td>
                                <td>{{ $dokumen->nama_dokumen }}</td>
                                <td>{{ $dokumen->no_dokumen ?? '-' }}</td>
                                <td>{{ $dokumen->tanggal_terbit ? \Carbon\Carbon::parse($dokumen->tanggal_terbit)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($dokumen->file_dokumen)
                                    <a href="{{ Storage::url($dokumen->file_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning" onclick="editDokumen({{ json_encode($dokumen) }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('dosen.kepegawaian.dokumen.destroy', $dokumen) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada dokumen</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pendidikan -->
<div class="modal fade" id="modalPendidikan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formPendidikan" action="{{ route('dosen.kepegawaian.pendidikan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodPendidikan"></div>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-mortarboard me-2"></i><span id="titlePendidikan">Tambah</span> Riwayat Pendidikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenjang <span class="text-danger">*</span></label>
                            <select name="jenjang" class="form-select" required>
                                <option value="">Pilih Jenjang</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA/SMK</option>
                                <option value="D1">D1</option>
                                <option value="D2">D2</option>
                                <option value="D3">D3</option>
                                <option value="D4">D4</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                                <option value="Profesi">Profesi</option>
                                <option value="Spesialis">Spesialis</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Institusi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_institusi" class="form-control" required minlength="3" maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                            <input type="text" name="program_studi" class="form-control" required minlength="3" maxlength="255">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tahun Masuk <span class="text-danger">*</span></label>
                            <input type="number" name="tahun_masuk" class="form-control" min="1950" max="{{ date('Y') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tahun Lulus</label>
                            <input type="number" name="tahun_lulus" class="form-control" min="1950" max="{{ date('Y') + 10 }}">
                            <small class="text-muted">Harus ≥ tahun masuk</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Ijazah</label>
                            <input type="text" name="no_ijazah" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">IPK</label>
                            <input type="number" name="ipk" class="form-control" step="0.01" min="0" max="4">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Judul Tugas Akhir</label>
                            <textarea name="judul_tugas_akhir" class="form-control" rows="2" maxlength="500"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">File Ijazah (PDF/Image, max 5MB)</label>
                            <input type="file" name="file_ijazah" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
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

<!-- Modal Jabatan -->
<div class="modal fade" id="modalJabatan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formJabatan" action="{{ route('dosen.kepegawaian.jabatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodJabatan"></div>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-briefcase me-2"></i><span id="titleJabatan">Tambah</span> Riwayat Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jabatan" class="form-control" required minlength="3" maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Jabatan <span class="text-danger">*</span></label>
                            <select name="jenis_jabatan" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="Struktural">Struktural</option>
                                <option value="Fungsional">Fungsional</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">TMT Jabatan <span class="text-danger">*</span></label>
                            <input type="date" name="tmt_jabatan" class="form-control" required max="{{ date('Y-m-d') }}">
                            <small class="text-muted">Tidak boleh lebih dari hari ini</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No SK</label>
                            <input type="text" name="no_sk" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal SK</label>
                            <input type="date" name="tanggal_sk" class="form-control" max="{{ date('Y-m-d') }}">
                            <small class="text-muted">Harus ≤ TMT Jabatan</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pejabat Penetap</label>
                            <input type="text" name="pejabat_penetap" class="form-control" maxlength="255">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">File SK (PDF/Image, max 5MB)</label>
                            <input type="file" name="file_sk" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
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

<!-- Modal Pangkat -->
<div class="modal fade" id="modalPangkat" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formPangkat" action="{{ route('dosen.kepegawaian.pangkat.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodPangkat"></div>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-award me-2"></i><span id="titlePangkat">Tambah</span> Riwayat Pangkat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Golongan <span class="text-danger">*</span></label>
                            <select name="golongan" class="form-select" required>
                                <option value="">Pilih Golongan</option>
                                <option value="I/a">I/a - Juru Muda</option>
                                <option value="I/b">I/b - Juru Muda Tingkat I</option>
                                <option value="I/c">I/c - Juru</option>
                                <option value="I/d">I/d - Juru Tingkat I</option>
                                <option value="II/a">II/a - Pengatur Muda</option>
                                <option value="II/b">II/b - Pengatur Muda Tingkat I</option>
                                <option value="II/c">II/c - Pengatur</option>
                                <option value="II/d">II/d - Pengatur Tingkat I</option>
                                <option value="III/a">III/a - Penata Muda</option>
                                <option value="III/b">III/b - Penata Muda Tingkat I</option>
                                <option value="III/c">III/c - Penata</option>
                                <option value="III/d">III/d - Penata Tingkat I</option>
                                <option value="IV/a">IV/a - Pembina</option>
                                <option value="IV/b">IV/b - Pembina Tingkat I</option>
                                <option value="IV/c">IV/c - Pembina Utama Muda</option>
                                <option value="IV/d">IV/d - Pembina Utama Madya</option>
                                <option value="IV/e">IV/e - Pembina Utama</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pangkat <span class="text-danger">*</span></label>
                            <input type="text" name="pangkat" class="form-control" placeholder="Contoh: Penata Muda" required minlength="3" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">TMT Pangkat <span class="text-danger">*</span></label>
                            <input type="date" name="tmt_pangkat" class="form-control" required max="{{ date('Y-m-d') }}">
                            <small class="text-muted">Tidak boleh lebih dari hari ini</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No SK</label>
                            <input type="text" name="no_sk" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal SK</label>
                            <input type="date" name="tanggal_sk" class="form-control" max="{{ date('Y-m-d') }}">
                            <small class="text-muted">Harus ≤ TMT Pangkat</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pejabat Penetap</label>
                            <input type="text" name="pejabat_penetap" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Masa Kerja (Tahun)</label>
                            <input type="number" name="masa_kerja_tahun" class="form-control" min="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Masa Kerja (Bulan)</label>
                            <input type="number" name="masa_kerja_bulan" class="form-control" min="0" max="11">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">File SK (PDF/Image, max 5MB)</label>
                            <input type="file" name="file_sk" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
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

<!-- Modal Pelatihan -->
<div class="modal fade" id="modalPelatihan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formPelatihan" action="{{ route('dosen.kepegawaian.pelatihan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodPelatihan"></div>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-journal-check me-2"></i><span id="titlePelatihan">Tambah</span> Riwayat Pelatihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pelatihan" class="form-control" required minlength="3" maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Pelatihan <span class="text-danger">*</span></label>
                            <select name="jenis_pelatihan" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Kursus">Kursus</option>
                                <option value="Diklat">Diklat</option>
                                <option value="Sertifikasi">Sertifikasi</option>
                                <option value="Lokakarya">Lokakarya</option>
                                <option value="Bimtek">Bimtek</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                            <input type="text" name="penyelenggara" class="form-control" required minlength="3" maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat</label>
                            <input type="text" name="tempat" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control" required max="{{ date('Y-m-d') }}">
                            <small class="text-muted">Tidak boleh lebih dari hari ini</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" max="{{ date('Y-m-d') }}">
                            <small class="text-muted">Harus ≥ tanggal mulai</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jam Pelatihan (JP)</label>
                            <input type="number" name="jam_pelatihan" class="form-control" min="1" max="1000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Sertifikat</label>
                            <input type="text" name="no_sertifikat" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">File Sertifikat (PDF/Image, max 5MB)</label>
                            <input type="file" name="file_sertifikat" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
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

<!-- Modal Dokumen -->
<div class="modal fade" id="modalDokumen" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formDokumen" action="{{ route('dosen.kepegawaian.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodDokumen"></div>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-file-earmark me-2"></i><span id="titleDokumen">Tambah</span> Dokumen Kepegawaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                            <select name="jenis_dokumen" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="KTP">KTP</option>
                                <option value="KK">Kartu Keluarga</option>
                                <option value="NPWP">NPWP</option>
                                <option value="BPJS Kesehatan">BPJS Kesehatan</option>
                                <option value="BPJS Ketenagakerjaan">BPJS Ketenagakerjaan</option>
                                <option value="SK CPNS">SK CPNS</option>
                                <option value="SK PNS">SK PNS</option>
                                <option value="SK Pengangkatan">SK Pengangkatan</option>
                                <option value="Sertifikat Pendidik">Sertifikat Pendidik</option>
                                <option value="Sertifikat Profesi">Sertifikat Profesi</option>
                                <option value="Ijazah">Ijazah</option>
                                <option value="Transkrip">Transkrip</option>
                                <option value="Akta Kelahiran">Akta Kelahiran</option>
                                <option value="Surat Nikah">Surat Nikah</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="nama_dokumen" class="form-control" required minlength="3" maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Dokumen</label>
                            <input type="text" name="no_dokumen" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Instansi Penerbit</label>
                            <input type="text" name="instansi_penerbit" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Terbit</label>
                            <input type="date" name="tanggal_terbit" class="form-control" max="{{ date('Y-m-d') }}">
                            <small class="text-muted">Tidak boleh lebih dari hari ini</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Berlaku (s/d)</label>
                            <input type="date" name="tanggal_berlaku" class="form-control">
                            <small class="text-muted">Harus ≥ tanggal terbit</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" maxlength="500"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">File Dokumen <span class="text-danger" id="fileDokumenRequired">*</span> (PDF/Image, max 10MB)</label>
                            <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png" id="fileDokumenInput">
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

@endsection

@push('scripts')
<script>
// Edit Functions
function editPendidikan(data) {
    const form = document.getElementById('formPendidikan');
    form.action = "{{ url('portal-dosen/kepegawaian/pendidikan') }}/" + data.hashid;
    document.getElementById('methodPendidikan').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('titlePendidikan').textContent = 'Edit';
    
    form.querySelector('[name="jenjang"]').value = data.jenjang || '';
    form.querySelector('[name="nama_institusi"]').value = data.nama_institusi || '';
    form.querySelector('[name="program_studi"]').value = data.program_studi || '';
    form.querySelector('[name="tahun_masuk"]').value = data.tahun_masuk || '';
    form.querySelector('[name="tahun_lulus"]').value = data.tahun_lulus || '';
    form.querySelector('[name="no_ijazah"]').value = data.no_ijazah || '';
    form.querySelector('[name="ipk"]').value = data.ipk || '';
    form.querySelector('[name="judul_tugas_akhir"]').value = data.judul_tugas_akhir || '';
    
    new bootstrap.Modal(document.getElementById('modalPendidikan')).show();
}

function editJabatan(data) {
    const form = document.getElementById('formJabatan');
    form.action = "{{ url('portal-dosen/kepegawaian/jabatan') }}/" + data.hashid;
    document.getElementById('methodJabatan').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('titleJabatan').textContent = 'Edit';
    
    form.querySelector('[name="nama_jabatan"]').value = data.nama_jabatan || '';
    form.querySelector('[name="jenis_jabatan"]').value = data.jenis_jabatan || '';
    form.querySelector('[name="tmt_jabatan"]').value = data.tmt_jabatan || '';
    form.querySelector('[name="no_sk"]').value = data.no_sk || '';
    form.querySelector('[name="tanggal_sk"]').value = data.tanggal_sk || '';
    form.querySelector('[name="pejabat_penetap"]').value = data.pejabat_penetap || '';
    
    new bootstrap.Modal(document.getElementById('modalJabatan')).show();
}

function editPangkat(data) {
    const form = document.getElementById('formPangkat');
    form.action = "{{ url('portal-dosen/kepegawaian/pangkat') }}/" + data.hashid;
    document.getElementById('methodPangkat').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('titlePangkat').textContent = 'Edit';
    
    form.querySelector('[name="golongan"]').value = data.golongan || '';
    form.querySelector('[name="pangkat"]').value = data.pangkat || '';
    form.querySelector('[name="tmt_pangkat"]').value = data.tmt_pangkat || '';
    form.querySelector('[name="no_sk"]').value = data.no_sk || '';
    form.querySelector('[name="tanggal_sk"]').value = data.tanggal_sk || '';
    form.querySelector('[name="pejabat_penetap"]').value = data.pejabat_penetap || '';
    form.querySelector('[name="masa_kerja_tahun"]').value = data.masa_kerja_tahun || '';
    form.querySelector('[name="masa_kerja_bulan"]').value = data.masa_kerja_bulan || '';
    
    new bootstrap.Modal(document.getElementById('modalPangkat')).show();
}

function editPelatihan(data) {
    const form = document.getElementById('formPelatihan');
    form.action = "{{ url('portal-dosen/kepegawaian/pelatihan') }}/" + data.hashid;
    document.getElementById('methodPelatihan').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('titlePelatihan').textContent = 'Edit';
    
    form.querySelector('[name="nama_pelatihan"]').value = data.nama_pelatihan || '';
    form.querySelector('[name="jenis_pelatihan"]').value = data.jenis_pelatihan || '';
    form.querySelector('[name="penyelenggara"]').value = data.penyelenggara || '';
    form.querySelector('[name="tempat"]').value = data.tempat || '';
    form.querySelector('[name="tanggal_mulai"]').value = data.tanggal_mulai || '';
    form.querySelector('[name="tanggal_selesai"]').value = data.tanggal_selesai || '';
    form.querySelector('[name="jam_pelatihan"]').value = data.jam_pelatihan || '';
    form.querySelector('[name="no_sertifikat"]').value = data.no_sertifikat || '';
    
    new bootstrap.Modal(document.getElementById('modalPelatihan')).show();
}

function editDokumen(data) {
    const form = document.getElementById('formDokumen');
    form.action = "{{ url('portal-dosen/kepegawaian/dokumen') }}/" + data.hashid;
    document.getElementById('methodDokumen').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('titleDokumen').textContent = 'Edit';
    document.getElementById('fileDokumenInput').removeAttribute('required');
    document.getElementById('fileDokumenRequired').style.display = 'none';
    
    form.querySelector('[name="jenis_dokumen"]').value = data.jenis_dokumen || '';
    form.querySelector('[name="nama_dokumen"]').value = data.nama_dokumen || '';
    form.querySelector('[name="no_dokumen"]').value = data.no_dokumen || '';
    form.querySelector('[name="instansi_penerbit"]').value = data.instansi_penerbit || '';
    form.querySelector('[name="tanggal_terbit"]').value = data.tanggal_terbit || '';
    form.querySelector('[name="tanggal_berlaku"]').value = data.tanggal_berlaku || '';
    form.querySelector('[name="keterangan"]').value = data.keterangan || '';
    
    new bootstrap.Modal(document.getElementById('modalDokumen')).show();
}

// Reset modals on close
['Pendidikan', 'Jabatan', 'Pangkat', 'Pelatihan', 'Dokumen'].forEach(type => {
    document.getElementById('modal' + type).addEventListener('hidden.bs.modal', function () {
        const form = document.getElementById('form' + type);
        form.reset();
        form.action = "{{ url('portal-dosen/kepegawaian') }}/" + type.toLowerCase();
        document.getElementById('method' + type).innerHTML = '';
        document.getElementById('title' + type).textContent = 'Tambah';
        if (type === 'Dokumen') {
            document.getElementById('fileDokumenInput').setAttribute('required', 'required');
            document.getElementById('fileDokumenRequired').style.display = 'inline';
        }
    });
});

// Preserve active tab on page reload
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`[data-bs-target="${hash}"]`);
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }
    
    // Update URL hash on tab change
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            history.replaceState(null, null, e.target.getAttribute('data-bs-target'));
        });
    });
});
</script>
@endpush
