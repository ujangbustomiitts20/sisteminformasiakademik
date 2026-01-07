@extends('layouts.app')

@section('title', 'Detail SKP')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Detail SKP</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dosen.skp.index') }}">SKP</a></li>
                <li class="breadcrumb-item active">{{ $skp->no_skp }}</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('dosen.skp.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        @if(in_array($skp->status, ['draft', 'revisi']))
        <a href="{{ route('dosen.skp.edit', $skp) }}" class="btn btn-warning btn-sm me-2">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @endif
        @if($skp->status === 'final')
        <a href="{{ route('dosen.skp.cetak', $skp) }}" class="btn btn-primary btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i>Cetak
        </a>
        @endif
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
                    <div class="col-md-4">
                        <label class="form-label text-muted small">No. SKP</label>
                        <p class="fw-semibold mb-0">{{ $skp->no_skp }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Tahun</label>
                        <p class="fw-semibold mb-0">{{ $skp->tahun }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Periode</label>
                        <p class="fw-semibold mb-0">{{ $skp->periode ?? 'Tahunan' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Nama Pegawai</label>
                        <p class="fw-semibold mb-0">{{ $dosen->nama_lengkap ?? $dosen->nama }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Jabatan</label>
                        <p class="fw-semibold mb-0">{{ $skp->jabatan ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Unit Kerja</label>
                        <p class="fw-semibold mb-0">{{ $skp->unit_kerja ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Action -->
        @if($skp->status === 'revisi' && $skp->catatan)
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Catatan Revisi dari Atasan</h6>
            <p class="mb-0">{{ $skp->catatan }}</p>
        </div>
        @endif

        @if(in_array($skp->status, ['draft', 'revisi']) && $skp->targetSkp->count() > 0)
        <div class="alert alert-warning mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-send me-2"></i>
                    <strong>Siap mengajukan SKP?</strong> Pastikan semua target kinerja sudah lengkap.
                </div>
                <form action="{{ route('dosen.skp.ajukan', $skp) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Ajukan SKP ini ke atasan untuk persetujuan?')">
                        <i class="bi bi-send me-1"></i>Ajukan SKP
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($skp->status === 'diajukan')
        <div class="alert alert-info mb-4">
            <i class="bi bi-hourglass-split me-2"></i>
            SKP sedang menunggu <strong>persetujuan dari atasan</strong>. Anda akan diberitahu setelah disetujui.
        </div>
        @endif

        @if($skp->status === 'disetujui')
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle me-2"></i>
            Target SKP telah <strong>disetujui</strong>. Silakan lakukan kegiatan sesuai target yang ditetapkan.
        </div>
        @endif

        <!-- Target Kinerja -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Target Kinerja ({{ $skp->targetSkp->count() }} target)</h6>
                @if(in_array($skp->status, ['draft', 'revisi']))
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTarget">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Target
                </button>
                @endif
            </div>
            <div class="card-body p-0">
                @if($skp->targetSkp->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px">No</th>
                                <th>Uraian Kegiatan</th>
                                <th class="text-center">Target</th>
                                @if(in_array($skp->status, ['realisasi', 'dinilai', 'final']))
                                <th class="text-center">Realisasi</th>
                                <th class="text-center">Capaian</th>
                                @endif
                                @if(in_array($skp->status, ['draft', 'revisi', 'realisasi']))
                                <th class="text-center" style="width: 100px">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($skp->targetSkp as $index => $target)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $target->uraian_kegiatan }}</div>
                                    @if($target->keterangan)
                                    <small class="text-muted">{{ $target->keterangan }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="small">
                                        @if($target->target_kuantitas)
                                        <div>Kuantitas: {{ $target->target_kuantitas }} {{ $target->satuan }}</div>
                                        @endif
                                        @if($target->target_kualitas)
                                        <div>Kualitas: {{ $target->target_kualitas }}%</div>
                                        @endif
                                        @if($target->target_waktu)
                                        <div>Waktu: {{ $target->target_waktu }} bln</div>
                                        @endif
                                    </div>
                                </td>
                                @if(in_array($skp->status, ['realisasi', 'dinilai', 'final']))
                                <td class="text-center">
                                    <div class="small">
                                        @if($target->realisasi_kuantitas)
                                        <div>Kuantitas: {{ $target->realisasi_kuantitas }} {{ $target->satuan }}</div>
                                        @endif
                                        @if($target->realisasi_kualitas)
                                        <div>Kualitas: {{ $target->realisasi_kualitas }}%</div>
                                        @endif
                                        @if($target->realisasi_waktu)
                                        <div>Waktu: {{ $target->realisasi_waktu }} bln</div>
                                        @endif
                                    </div>
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
                                @endif
                                @if(in_array($skp->status, ['draft', 'revisi', 'realisasi']))
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning" 
                                                onclick="editTarget({{ json_encode(array_merge($target->toArray(), ['hashid' => $target->hashid])) }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if(in_array($skp->status, ['draft', 'revisi']))
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDeleteTarget('{{ route('dosen.skp.target.destroy', $target) }}')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                    <p class="mb-2">Belum ada target kinerja</p>
                    @if(in_array($skp->status, ['draft', 'revisi']))
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTarget">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Target Pertama
                    </button>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Realisasi Info -->
        @if($skp->status === 'realisasi' && $skp->targetSkp->count() > 0)
        <div class="card shadow-sm border-info mb-4">
            <div class="card-header bg-info text-white py-3">
                <h6 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Input Realisasi Capaian</h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Silakan input realisasi capaian untuk setiap target kinerja dengan mengklik tombol Edit pada tabel di atas.</p>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Setelah semua realisasi diinput, atasan akan melakukan penilaian terhadap capaian Anda.
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Hasil Penilaian -->
        @if(in_array($skp->status, ['dinilai', 'final']))
        <div class="card shadow-sm border-success mb-4">
            <div class="card-header bg-success text-white py-3">
                <h6 class="mb-0"><i class="bi bi-award me-2"></i>Hasil Penilaian</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="display-4 fw-bold text-{{ $skp->predikat_badge }}">
                        {{ number_format($skp->nilai_akhir, 2) }}
                    </div>
                    <span class="badge bg-{{ $skp->predikat_badge }} fs-5">{{ $skp->predikat_label }}</span>
                </div>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Nilai SKP (60%)</td>
                        <td class="text-end fw-semibold">{{ number_format($skp->nilai_skp, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nilai Perilaku (40%)</td>
                        <td class="text-end fw-semibold">{{ number_format($skp->nilai_perilaku, 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold">Nilai Akhir</td>
                        <td class="text-end fw-bold">{{ number_format($skp->nilai_akhir, 2) }}</td>
                    </tr>
                </table>
                @if($skp->tanggal_penilaian)
                <div class="text-muted small text-center mt-3">
                    Dinilai pada {{ $skp->tanggal_penilaian->format('d/m/Y') }}
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Alur Status -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Alur Status</h6>
            </div>
            <div class="card-body">
                <div class="timeline-steps">
                    @php
                        $statusOrder = ['draft', 'diajukan', 'disetujui', 'realisasi', 'dinilai', 'final'];
                        $currentIndex = array_search($skp->status, $statusOrder);
                        if ($skp->status === 'revisi') $currentIndex = 0;
                    @endphp
                    @foreach($statusOrder as $index => $status)
                    @php
                        $isActive = $index <= $currentIndex;
                        $isCurrent = $skp->status === $status || ($skp->status === 'revisi' && $status === 'draft');
                    @endphp
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2 {{ $isActive ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width: 28px; height: 28px; font-size: 12px;">
                            @if($isActive && !$isCurrent)
                            <i class="bi bi-check"></i>
                            @else
                            {{ $index + 1 }}
                            @endif
                        </div>
                        <span class="{{ $isCurrent ? 'fw-bold' : ($isActive ? '' : 'text-muted') }}">
                            {{ \App\Models\SkpPegawai::STATUS[$status] ?? ucfirst($status) }}
                        </span>
                        @if($isCurrent)
                        <span class="badge bg-primary ms-2">Saat ini</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($skp->catatan && !in_array($skp->status, ['revisi']))
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Catatan</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $skp->catatan }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Target -->
@if(in_array($skp->status, ['draft', 'revisi']))
<div class="modal fade" id="modalTambahTarget" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dosen.skp.target.store', $skp) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Tambah Target Kinerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" id="add_kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach(\App\Models\UraianKegiatanSkp::KATEGORI as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Uraian Kegiatan <span class="text-danger">*</span></label>
                        <select name="uraian_kegiatan_skp_id" id="add_uraian_kegiatan_select" class="form-select" required disabled>
                            <option value="">-- Pilih kategori terlebih dahulu --</option>
                        </select>
                        <small class="text-muted">Pilih dari daftar uraian kegiatan yang sudah ditetapkan</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Uraian Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="uraian_kegiatan" id="add_uraian_kegiatan" class="form-control" rows="3" required readonly placeholder="Akan terisi otomatis dari pilihan di atas"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Target Kuantitas</label>
                                <input type="number" name="target_kuantitas" id="add_target_kuantitas" class="form-control" step="0.01" min="0" placeholder="Contoh: 10">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Satuan</label>
                                <input type="text" name="satuan" id="add_satuan" class="form-control" readonly placeholder="Terisi dari pilihan">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Target Kualitas (%)</label>
                                <input type="number" name="target_kualitas" class="form-control" min="0" max="100" placeholder="0-100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Target Waktu (bulan)</label>
                                <input type="number" name="target_waktu" class="form-control" min="0" placeholder="Contoh: 6">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
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
@endif

<!-- Modal Edit Target -->
<div class="modal fade" id="modalEditTarget" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTargetForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Target Kinerja</h5>
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
                                <label class="form-label">Target Kualitas (%)</label>
                                <input type="number" name="target_kualitas" id="edit_target_kualitas" class="form-control" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Target Waktu (bulan)</label>
                                <input type="number" name="target_waktu" id="edit_target_waktu" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                    
                    @if($skp->status === 'realisasi')
                    <hr>
                    <h6 class="mb-3"><i class="bi bi-clipboard-check me-2"></i>Input Realisasi</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Realisasi Kuantitas</label>
                                <input type="number" name="realisasi_kuantitas" id="edit_realisasi_kuantitas" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Realisasi Kualitas (%)</label>
                                <input type="number" name="realisasi_kualitas" id="edit_realisasi_kualitas" class="form-control" min="0" max="100">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Realisasi Waktu (bulan)</label>
                                <input type="number" name="realisasi_waktu" id="edit_realisasi_waktu" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                    @endif

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

<!-- Modal Delete -->
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
                    <p class="mb-0">Yakin ingin menghapus target kinerja ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Store uraian kegiatan data
let uraianKegiatanData = [];

// Load uraian kegiatan data on page load (filtered for dosen)
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("dosen.skp.uraian-kegiatan") }}?tipe_pegawai=dosen')
        .then(response => response.json())
        .then(data => {
            uraianKegiatanData = data;
        })
        .catch(error => console.error('Error loading uraian kegiatan:', error));
});

// Handle kategori change for add modal
document.getElementById('add_kategori')?.addEventListener('change', function() {
    const kategori = this.value;
    const selectElement = document.getElementById('add_uraian_kegiatan_select');
    
    if (!kategori) {
        selectElement.innerHTML = '<option value="">-- Pilih kategori terlebih dahulu --</option>';
        selectElement.disabled = true;
        return;
    }
    
    // Filter uraian kegiatan by kategori
    const filteredData = uraianKegiatanData.filter(item => item.kategori === kategori);
    
    if (filteredData.length === 0) {
        selectElement.innerHTML = '<option value="">-- Tidak ada data untuk kategori ini --</option>';
        selectElement.disabled = true;
        return;
    }
    
    // Group by sub_kategori
    const grouped = {};
    filteredData.forEach(item => {
        const subKat = item.sub_kategori_label || 'Lainnya';
        if (!grouped[subKat]) grouped[subKat] = [];
        grouped[subKat].push(item);
    });
    
    // Build options
    let html = '<option value="">-- Pilih Uraian Kegiatan --</option>';
    Object.keys(grouped).forEach(subKat => {
        html += `<optgroup label="${subKat}">`;
        grouped[subKat].forEach(item => {
            html += `<option value="${item.id}" data-uraian="${item.uraian_kegiatan}" data-satuan="${item.satuan}" data-target="${item.target_default || ''}">${item.kode} - ${item.uraian_kegiatan}</option>`;
        });
        html += '</optgroup>';
    });
    
    selectElement.innerHTML = html;
    selectElement.disabled = false;
});

// Handle uraian kegiatan select change for add modal
document.getElementById('add_uraian_kegiatan_select')?.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option && option.value) {
        document.getElementById('add_uraian_kegiatan').value = option.dataset.uraian || '';
        document.getElementById('add_satuan').value = option.dataset.satuan || '';
        if (option.dataset.target) {
            document.getElementById('add_target_kuantitas').value = option.dataset.target;
        }
    } else {
        document.getElementById('add_uraian_kegiatan').value = '';
        document.getElementById('add_satuan').value = '';
    }
});

function editTarget(target) {
    document.getElementById('editTargetForm').action = '{{ url("portal-dosen/skp/target") }}/' + target.hashid;
    document.getElementById('edit_uraian_kegiatan').value = target.uraian_kegiatan || '';
    document.getElementById('edit_target_kuantitas').value = target.target_kuantitas || '';
    document.getElementById('edit_satuan').value = target.satuan || '';
    document.getElementById('edit_target_kualitas').value = target.target_kualitas || '';
    document.getElementById('edit_target_waktu').value = target.target_waktu || '';
    document.getElementById('edit_keterangan').value = target.keterangan || '';
    
    if (document.getElementById('edit_realisasi_kuantitas')) {
        document.getElementById('edit_realisasi_kuantitas').value = target.realisasi_kuantitas || '';
    }
    if (document.getElementById('edit_realisasi_kualitas')) {
        document.getElementById('edit_realisasi_kualitas').value = target.realisasi_kualitas || '';
    }
    if (document.getElementById('edit_realisasi_waktu')) {
        document.getElementById('edit_realisasi_waktu').value = target.realisasi_waktu || '';
    }
    
    new bootstrap.Modal(document.getElementById('modalEditTarget')).show();
}

function confirmDeleteTarget(url) {
    document.getElementById('deleteTargetForm').action = url;
    new bootstrap.Modal(document.getElementById('modalDeleteTarget')).show();
}
</script>
@endpush
