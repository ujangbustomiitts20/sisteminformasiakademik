@extends('layouts.app')

@section('title', 'Detail SKP')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Detail SKP</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kepegawaian.skp.index') }}">SKP Pegawai</a></li>
                <li class="breadcrumb-item active">{{ $skp->no_skp }}</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('kepegawaian.skp.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <a href="{{ route('kepegawaian.skp.edit', $skp) }}" class="btn btn-warning btn-sm me-2">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('kepegawaian.skp.cetak', $skp) }}" class="btn btn-primary btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i>Cetak
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Info SKP -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Informasi SKP</h6>
                <span class="badge bg-{{ $skp->status_badge }} fs-6">
                    {{ \App\Models\SkpPegawai::STATUS[$skp->status] ?? $skp->status }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">No. SKP</label>
                        <p class="fw-semibold mb-0">{{ $skp->no_skp }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Periode</label>
                        <p class="fw-semibold mb-0">{{ $skp->periode_format }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Tanggal SKP</label>
                        <p class="mb-0">{{ $skp->tanggal_skp ? $skp->tanggal_skp->format('d F Y') : '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Tanggal Penilaian</label>
                        <p class="mb-0">{{ $skp->tanggal_penilaian ? $skp->tanggal_penilaian->format('d F Y') : '-' }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Nama Pegawai</label>
                        <p class="fw-semibold mb-0">{{ $skp->nama_pegawai }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">NIDN/NIP</label>
                        <p class="mb-0">{{ $skp->dosen->nidn ?? $skp->dosen->nip ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Program Studi</label>
                        <p class="mb-0">{{ $skp->dosen->programStudi->nama ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Target SKP -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-bullseye me-2"></i>Target dan Realisasi Kinerja</h6>
                @if($skp->status !== 'final')
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTarget">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Target
                </button>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">No</th>
                                <th>Uraian Kegiatan</th>
                                <th class="text-center" width="100">Target</th>
                                <th class="text-center" width="100">Realisasi</th>
                                <th class="text-center" width="80">Nilai</th>
                                @if($skp->status !== 'final')
                                <th width="100">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($skp->targetSkp as $index => $target)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $target->uraian_kegiatan }}</strong>
                                    @if($target->keterangan)
                                    <br><small class="text-muted">{{ $target->keterangan }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $target->target_kuantitas ?? '-' }}
                                    @if($target->satuan)
                                    <br><small class="text-muted">{{ $target->satuan }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $target->realisasi_kuantitas ?? '-' }}
                                    @if($target->satuan)
                                    <br><small class="text-muted">{{ $target->satuan }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($target->nilai_capaian)
                                        <span class="badge bg-{{ $target->nilai_capaian >= 76 ? 'success' : ($target->nilai_capaian >= 61 ? 'warning' : 'danger') }}">
                                            {{ number_format($target->nilai_capaian, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                @if($skp->status !== 'final')
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning" 
                                                onclick="editTarget({{ $target->id }}, '{{ addslashes($target->uraian_kegiatan) }}', '{{ $target->satuan }}', {{ $target->target_kuantitas ?? 'null' }}, {{ $target->realisasi_kuantitas ?? 'null' }}, '{{ addslashes($target->keterangan ?? '') }}')" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDeleteTarget('{{ route('kepegawaian.skp.target.destroy', $target) }}')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $skp->status !== 'final' ? 6 : 5 }}" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Belum ada target yang ditetapkan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        @if($skp->catatan)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Catatan</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $skp->catatan }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Penilaian -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Hasil Penilaian</h6>
            </div>
            <div class="card-body">
                <!-- Nilai SKP -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Nilai SKP (60%)</span>
                        <span class="fw-bold fs-5">{{ $skp->nilai_skp ? number_format($skp->nilai_skp, 2) : '-' }}</span>
                    </div>
                    @if($skp->nilai_skp)
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ min($skp->nilai_skp, 100) }}%"></div>
                    </div>
                    @endif
                </div>

                <!-- Nilai Perilaku -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Nilai Perilaku (40%)</span>
                        <span class="fw-bold fs-5">{{ $skp->nilai_perilaku ? number_format($skp->nilai_perilaku, 2) : '-' }}</span>
                    </div>
                    @if($skp->nilai_perilaku)
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: {{ min($skp->nilai_perilaku, 100) }}%"></div>
                    </div>
                    @endif
                </div>

                <hr>

                <!-- Nilai Akhir -->
                <div class="text-center py-3">
                    <p class="text-muted mb-1">Nilai Akhir</p>
                    <div class="display-4 fw-bold text-{{ $skp->predikat_badge ?? 'secondary' }}">
                        {{ $skp->nilai_akhir ? number_format($skp->nilai_akhir, 2) : '-' }}
                    </div>
                    @if($skp->predikat)
                    <span class="badge bg-{{ $skp->predikat_badge }} fs-6 mt-2">
                        {{ $skp->predikat_label }}
                    </span>
                    @endif
                </div>

                @if(in_array($skp->status, ['realisasi', 'dinilai', 'final']))
                <hr>
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalPenilaian">
                    <i class="bi bi-check2-square me-1"></i>{{ $skp->status === 'final' ? 'Update Penilaian' : 'Input Penilaian' }}
                </button>
                @endif
            </div>
        </div>

        <!-- Approval Workflow Actions -->
        @if($skp->status === 'diajukan')
        <div class="card shadow-sm border-warning mb-4">
            <div class="card-header bg-warning text-dark py-3">
                <h6 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Menunggu Persetujuan</h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">SKP ini diajukan oleh pegawai dan menunggu persetujuan target kinerja.</p>
                <div class="d-grid gap-2">
                    <form action="{{ route('kepegawaian.skp.approve', $skp) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui target SKP ini?')">
                            <i class="bi bi-check-lg me-1"></i>Setujui Target
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalRevisi">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Kembalikan untuk Revisi
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if($skp->status === 'disetujui')
        <div class="card shadow-sm border-primary mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Target Disetujui</h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Target SKP sudah disetujui. Buka input realisasi agar pegawai bisa melaporkan capaian.</p>
                <form action="{{ route('kepegawaian.skp.buka-realisasi', $skp) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-info w-100" onclick="return confirm('Buka input realisasi untuk pegawai?')">
                        <i class="bi bi-clipboard-check me-1"></i>Buka Input Realisasi
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($skp->status === 'realisasi')
        <div class="card shadow-sm border-info mb-4">
            <div class="card-header bg-info text-white py-3">
                <h6 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Menunggu Realisasi</h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">Pegawai sedang menginput realisasi capaian. Setelah selesai, lakukan penilaian.</p>
            </div>
        </div>
        @endif

        <!-- Update Status (Admin Override) -->
        @if(!in_array($skp->status, ['final']))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Update Status Manual</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.skp.status', $skp) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <select name="status" class="form-select form-select-sm">
                            @foreach(\App\Models\SkpPegawai::STATUS as $key => $label)
                                <option value="{{ $key }}" {{ $skp->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-arrow-repeat me-1"></i>Update Status
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Info Kriteria -->
        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Kriteria Predikat</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><span class="badge bg-success">≥ 91</span> Sangat Baik</li>
                    <li class="mb-2"><span class="badge bg-primary">76 - 90</span> Baik</li>
                    <li class="mb-2"><span class="badge bg-warning">61 - 75</span> Cukup</li>
                    <li class="mb-2"><span class="badge bg-danger">51 - 60</span> Kurang</li>
                    <li><span class="badge bg-dark">≤ 50</span> Buruk</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Target -->
<div class="modal fade" id="modalTambahTarget" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.skp.target.store', $skp) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Tambah Target SKP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Uraian Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="uraian_kegiatan" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Target Kuantitas</label>
                                <input type="number" name="target_kuantitas" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Satuan</label>
                                <input type="text" name="satuan" class="form-control" placeholder="SKS, dokumen, dll">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Target -->
<div class="modal fade" id="modalEditTarget" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditTarget" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Target SKP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Uraian Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="uraian_kegiatan" id="edit_uraian_kegiatan" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Target Kuantitas</label>
                                <input type="number" name="target_kuantitas" id="edit_target_kuantitas" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Satuan</label>
                                <input type="text" name="satuan" id="edit_satuan" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Realisasi Kuantitas</label>
                                <input type="number" name="realisasi_kuantitas" id="edit_realisasi_kuantitas" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Penilaian -->
<div class="modal fade" id="modalPenilaian" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.skp.penilaian', $skp) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-check2-square me-2"></i>Input Penilaian SKP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nilai SKP (0-100) <span class="text-danger">*</span></label>
                                <input type="number" name="nilai_skp" class="form-control" 
                                       value="{{ $skp->nilai_skp }}" step="0.01" min="0" max="100" required>
                                <small class="text-muted">Bobot: 60%</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nilai Perilaku (0-100) <span class="text-danger">*</span></label>
                                <input type="number" name="nilai_perilaku" class="form-control" 
                                       value="{{ $skp->nilai_perilaku }}" step="0.01" min="0" max="100" required>
                                <small class="text-muted">Bobot: 40%</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="mb-3">Detail Penilaian Perilaku (Opsional)</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Orientasi Pelayanan</label>
                                <input type="number" name="orientasi_pelayanan" class="form-control" 
                                       value="{{ $skp->orientasi_pelayanan }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Integritas</label>
                                <input type="number" name="integritas" class="form-control" 
                                       value="{{ $skp->integritas }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Komitmen</label>
                                <input type="number" name="komitmen" class="form-control" 
                                       value="{{ $skp->komitmen }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Disiplin</label>
                                <input type="number" name="disiplin" class="form-control" 
                                       value="{{ $skp->disiplin }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Kerjasama</label>
                                <input type="number" name="kerjasama" class="form-control" 
                                       value="{{ $skp->kerjasama }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Kepemimpinan</label>
                                <input type="number" name="kepemimpinan" class="form-control" 
                                       value="{{ $skp->kepemimpinan }}" step="0.01" min="0" max="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2">{{ $skp->catatan }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Target -->
<div class="modal fade" id="modalDeleteTarget" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="deleteTargetForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus target ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Revisi -->
<div class="modal fade" id="modalRevisi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.skp.revisi', $skp) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-arrow-counterclockwise me-2"></i>Kembalikan untuk Revisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">SKP akan dikembalikan ke pegawai untuk diperbaiki. Berikan catatan revisi.</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan Revisi <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="4" required placeholder="Jelaskan apa yang perlu diperbaiki..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-send me-1"></i>Kirim Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editTarget(id, uraian, satuan, target, realisasi, keterangan) {
    document.getElementById('formEditTarget').action = '{{ url("kepegawaian/skp/target") }}/' + id;
    document.getElementById('edit_uraian_kegiatan').value = uraian;
    document.getElementById('edit_satuan').value = satuan || '';
    document.getElementById('edit_target_kuantitas').value = target || '';
    document.getElementById('edit_realisasi_kuantitas').value = realisasi || '';
    document.getElementById('edit_keterangan').value = keterangan || '';
    new bootstrap.Modal(document.getElementById('modalEditTarget')).show();
}

function confirmDeleteTarget(url) {
    document.getElementById('deleteTargetForm').action = url;
    new bootstrap.Modal(document.getElementById('modalDeleteTarget')).show();
}
</script>
@endpush
