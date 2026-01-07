@extends('layouts.app')

@section('title', 'Aktivitas Harian')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Aktivitas Harian</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Aktivitas Harian</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('dosen.aktivitas-harian.rekap') }}" class="btn btn-outline-primary me-2">
            <i class="bi bi-bar-chart me-1"></i>Rekap Bulanan
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Aktivitas
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                        <div class="small">Total Aktivitas</div>
                    </div>
                    <i class="bi bi-list-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-secondary text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['draft'] }}</div>
                        <div class="small">Draft</div>
                    </div>
                    <i class="bi bi-pencil-square fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-warning text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['diajukan'] }}</div>
                        <div class="small">Menunggu Approval</div>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['disetujui'] }}</div>
                        <div class="small">Disetujui</div>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="bulan" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (request('bulan', now()->month) == $m) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="tahun" class="form-select form-select-sm">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ (request('tahun', now()->year) == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\AktivitasHarian::STATUS as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Cari kegiatan..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('dosen.aktivitas-harian.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Action -->
@if($stats['draft'] > 0)
<div class="card shadow-sm mb-4 border-warning">
    <div class="card-body py-2 d-flex justify-content-between align-items-center">
        <span class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Ada <strong>{{ $stats['draft'] }}</strong> aktivitas draft yang belum diajukan
        </span>
        <form action="{{ route('dosen.aktivitas-harian.ajukan-semua') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="bulan" value="{{ request('bulan', now()->month) }}">
            <input type="hidden" name="tahun" value="{{ request('tahun', now()->year) }}">
            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Ajukan semua aktivitas draft bulan ini?')">
                <i class="bi bi-send me-1"></i>Ajukan Semua Draft
            </button>
        </form>
    </div>
</div>
@endif

<!-- Daftar Aktivitas -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Aktivitas Harian</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="110">Tanggal</th>
                        <th width="100">Jam</th>
                        <th>Uraian Kegiatan</th>
                        <th>Output/Hasil</th>
                        <th class="text-center" width="100">Volume</th>
                        <th class="text-center" width="100">Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aktivitas as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->tanggal->format('d/m/Y') }}</strong>
                            <br><small class="text-muted">{{ $item->tanggal->translatedFormat('l') }}</small>
                        </td>
                        <td>
                            @if($item->jam_mulai)
                                {{ $item->jam_mulai_format }}
                                @if($item->jam_selesai)
                                    - {{ $item->jam_selesai_format }}
                                @endif
                                @if($item->durasi)
                                    <br><small class="text-muted">{{ $item->durasi }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ Str::limit($item->uraian_kegiatan, 60) }}</div>
                            @if($item->uraianKegiatanSkp)
                                <small class="badge bg-light text-dark">{{ $item->uraianKegiatanSkp->kategori_label }}</small>
                            @endif
                            @if($item->lokasi)
                                <br><small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $item->lokasi }}</small>
                            @endif
                        </td>
                        <td>{{ Str::limit($item->output_hasil, 40) ?: '-' }}</td>
                        <td class="text-center">
                            @if($item->volume)
                                {{ number_format($item->volume, 0) }}
                                @if($item->satuan)
                                    <br><small class="text-muted">{{ $item->satuan }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $item->status_badge }}">
                                {{ \App\Models\AktivitasHarian::STATUS[$item->status] }}
                            </span>
                            @if($item->status === 'ditolak' && $item->catatan_atasan)
                                <br><small class="text-danger" title="{{ $item->catatan_atasan }}">
                                    <i class="bi bi-info-circle"></i> Ada catatan
                                </small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" title="Detail"
                                        onclick="showDetail({{ json_encode($item) }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($item->canEdit())
                                <button type="button" class="btn btn-outline-warning" title="Edit"
                                        onclick="editAktivitas({{ json_encode($item) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Hapus"
                                        onclick="confirmDelete('{{ route('dosen.aktivitas-harian.destroy', $item) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada aktivitas harian
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($aktivitas->hasPages())
    <div class="card-footer bg-white">
        {{ $aktivitas->links() }}
    </div>
    @endif
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('dosen.aktivitas-harian.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Tambah Aktivitas Harian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Pilih dari Master Kegiatan (Opsional)</label>
                            <select name="uraian_kegiatan_skp_id" class="form-select" id="select_uraian_master">
                                <option value="">-- Pilih atau ketik manual di bawah --</option>
                                @foreach($uraianList->groupBy('kategori') as $kategori => $items)
                                    <optgroup label="{{ \App\Models\UraianKegiatanSkp::KATEGORI[$kategori] ?? $kategori }}">
                                        @foreach($items as $uraian)
                                            <option value="{{ $uraian->id }}" 
                                                    data-uraian="{{ $uraian->uraian_kegiatan }}"
                                                    data-satuan="{{ $uraian->satuan }}">
                                                {{ $uraian->uraian_kegiatan }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Uraian Kegiatan <span class="text-danger">*</span></label>
                            <textarea name="uraian_kegiatan" class="form-control" rows="3" required 
                                      id="input_uraian_kegiatan" placeholder="Jelaskan kegiatan yang dilakukan..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Output/Hasil</label>
                            <input type="text" name="output_hasil" class="form-control" placeholder="Hasil yang dicapai...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Volume</label>
                            <input type="number" name="volume" class="form-control" step="0.01" min="0" placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" id="input_satuan" placeholder="dokumen, jam, orang, dll">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Kampus, WFH, dll">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>
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

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Aktivitas Harian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" id="edit_jam_selesai" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Pilih dari Master Kegiatan (Opsional)</label>
                            <select name="uraian_kegiatan_skp_id" class="form-select" id="edit_uraian_kegiatan_skp_id">
                                <option value="">-- Pilih atau ketik manual di bawah --</option>
                                @foreach($uraianList->groupBy('kategori') as $kategori => $items)
                                    <optgroup label="{{ \App\Models\UraianKegiatanSkp::KATEGORI[$kategori] ?? $kategori }}">
                                        @foreach($items as $uraian)
                                            <option value="{{ $uraian->id }}" 
                                                    data-uraian="{{ $uraian->uraian_kegiatan }}"
                                                    data-satuan="{{ $uraian->satuan }}">
                                                {{ $uraian->uraian_kegiatan }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Uraian Kegiatan <span class="text-danger">*</span></label>
                            <textarea name="uraian_kegiatan" id="edit_uraian_kegiatan" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Output/Hasil</label>
                            <input type="text" name="output_hasil" id="edit_output_hasil" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Volume</label>
                            <input type="number" name="volume" id="edit_volume" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" id="edit_satuan" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="lokasi" id="edit_lokasi" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Detail Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="120" class="text-muted">Tanggal</td>
                        <td><strong id="detail_tanggal"></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jam</td>
                        <td id="detail_jam"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kegiatan</td>
                        <td id="detail_uraian"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Output</td>
                        <td id="detail_output"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Volume</td>
                        <td id="detail_volume"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Lokasi</td>
                        <td id="detail_lokasi"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td id="detail_status"></td>
                    </tr>
                    <tr id="row_catatan" style="display:none;">
                        <td class="text-muted">Catatan Atasan</td>
                        <td id="detail_catatan" class="text-danger"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formDelete" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Yakin ingin menghapus aktivitas ini?</p>
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
// Auto-fill uraian when selecting from master
document.getElementById('select_uraian_master').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        document.getElementById('input_uraian_kegiatan').value = selected.dataset.uraian || '';
        document.getElementById('input_satuan').value = selected.dataset.satuan || '';
    }
});

document.getElementById('edit_uraian_kegiatan_skp_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        document.getElementById('edit_uraian_kegiatan').value = selected.dataset.uraian || '';
        document.getElementById('edit_satuan').value = selected.dataset.satuan || '';
    }
});

function editAktivitas(item) {
    document.getElementById('formEdit').action = '{{ url("portal-dosen/aktivitas-harian") }}/' + item.hashid;
    document.getElementById('edit_tanggal').value = item.tanggal.split('T')[0];
    document.getElementById('edit_jam_mulai').value = item.jam_mulai_format || '';
    document.getElementById('edit_jam_selesai').value = item.jam_selesai_format || '';
    document.getElementById('edit_uraian_kegiatan_skp_id').value = item.uraian_kegiatan_skp_id || '';
    document.getElementById('edit_uraian_kegiatan').value = item.uraian_kegiatan || '';
    document.getElementById('edit_output_hasil').value = item.output_hasil || '';
    document.getElementById('edit_volume').value = item.volume || '';
    document.getElementById('edit_satuan').value = item.satuan || '';
    document.getElementById('edit_lokasi').value = item.lokasi || '';
    document.getElementById('edit_keterangan').value = item.keterangan || '';
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function showDetail(item) {
    const tanggal = new Date(item.tanggal);
    document.getElementById('detail_tanggal').textContent = tanggal.toLocaleDateString('id-ID', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});
    
    let jam = '-';
    if (item.jam_mulai_format) {
        jam = item.jam_mulai_format;
        if (item.jam_selesai_format) jam += ' - ' + item.jam_selesai_format;
        if (item.durasi) jam += ' (' + item.durasi + ')';
    }
    document.getElementById('detail_jam').textContent = jam;
    document.getElementById('detail_uraian').textContent = item.uraian_kegiatan;
    document.getElementById('detail_output').textContent = item.output_hasil || '-';
    document.getElementById('detail_volume').textContent = item.volume ? (item.volume + ' ' + (item.satuan || '')) : '-';
    document.getElementById('detail_lokasi').textContent = item.lokasi || '-';
    document.getElementById('detail_status').innerHTML = '<span class="badge bg-' + item.status_badge + '">' + item.status.charAt(0).toUpperCase() + item.status.slice(1) + '</span>';
    
    if (item.catatan_atasan) {
        document.getElementById('row_catatan').style.display = '';
        document.getElementById('detail_catatan').textContent = item.catatan_atasan;
    } else {
        document.getElementById('row_catatan').style.display = 'none';
    }
    
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
}

function confirmDelete(url) {
    document.getElementById('formDelete').action = url;
    new bootstrap.Modal(document.getElementById('modalDelete')).show();
}
</script>
@endpush
