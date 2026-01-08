@extends('layouts.app')

@section('title', 'Kontrak Kerja')

@section('content')
<div class="page-title">
    <h4>Kontrak Kerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Kontrak Kerja</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Kontrak</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <i class="bi bi-file-earmark-text stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Kontrak Aktif</h6>
                        <h3 class="mb-0">{{ $stats['aktif'] }}</h3>
                    </div>
                    <i class="bi bi-check-circle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Akan Berakhir</h6>
                        <h3 class="mb-0">{{ $stats['akan_berakhir'] }}</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Berakhir</h6>
                        <h3 class="mb-0">{{ $stats['berakhir'] }}</h3>
                    </div>
                    <i class="bi bi-x-circle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Kontrak Kerja</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kontrak
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="kontrakTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>No. Kontrak</th>
                        <th>Nama Pegawai</th>
                        <th>Jenis</th>
                        <th>Periode</th>
                        <th>Gaji Pokok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kontrakList as $index => $kontrak)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $kontrak->nomor_kontrak }}</td>
                        <td>
                            {{ $kontrak->nama_pegawai }}
                            <br><small class="text-muted">{{ $kontrak->dosen_id ? 'Dosen' : 'Tendik' }}</small>
                        </td>
                        <td>{{ $kontrak->jenis_kontrak_label }}</td>
                        <td>
                            {{ $kontrak->tanggal_mulai->format('d/m/Y') }}
                            @if($kontrak->tanggal_berakhir)
                                <br><small class="text-muted">s/d {{ $kontrak->tanggal_berakhir->format('d/m/Y') }}</small>
                                @if($kontrak->isExpiringSoon())
                                    <br><span class="badge bg-warning">Akan berakhir</span>
                                @endif
                            @else
                                <br><small class="text-muted">Tidak Terbatas</small>
                            @endif
                        </td>
                        <td>{{ format_rupiah($kontrak->gaji_pokok) }}</td>
                        <td>
                            <span class="badge bg-{{ $kontrak->status_color }}">{{ $kontrak->status_label }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kepegawaian.kontrak.show', $kontrak) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('kepegawaian.kontrak.edit', $kontrak) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($kontrak->status == 'draft')
                                <form action="{{ route('kepegawaian.kontrak.aktivasi', $kontrak) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Aktifkan" onclick="return confirm('Aktifkan kontrak ini?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @endif
                                @if($kontrak->status == 'aktif' && $kontrak->tanggal_berakhir)
                                <button type="button" class="btn btn-outline-primary" title="Perpanjang" data-bs-toggle="modal" data-bs-target="#modalPerpanjang{{ $kontrak->id }}">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                @endif
                                <form action="{{ route('kepegawaian.kontrak.destroy', $kontrak) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" onclick="return confirm('Yakin hapus?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada data kontrak kerja</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $kontrakList->links() }}
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.kontrak.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kontrak Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" class="form-select" id="tipePegawai" required>
                                <option value="">Pilih Tipe</option>
                                <option value="dosen">Dosen</option>
                                <option value="pegawai">Tenaga Kependidikan</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="dosenField" style="display: none;">
                            <label class="form-label">Dosen <span class="text-danger">*</span></label>
                            <select name="dosen_id" class="form-select">
                                <option value="">Pilih Dosen</option>
                                @foreach($dosenList as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }} ({{ $dosen->nidn ?? $dosen->nip }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="pegawaiField" style="display: none;">
                            <label class="form-label">Pegawai <span class="text-danger">*</span></label>
                            <select name="pegawai_id" class="form-select">
                                <option value="">Pilih Pegawai</option>
                                @foreach($pegawaiList as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama }} ({{ $pegawai->nip }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kontrak <span class="text-danger">*</span></label>
                            <select name="jenis_kontrak" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                @foreach(\App\Models\KontrakKerja::JENIS_KONTRAK as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Berakhir</label>
                            <input type="date" name="tanggal_berakhir" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak terbatas</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok</label>
                            <input type="number" name="gaji_pokok" class="form-control" min="0" step="1000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">File Kontrak (PDF)</label>
                            <input type="file" name="file_kontrak" class="form-control" accept=".pdf">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"></textarea>
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

<!-- Modal Perpanjang -->
@foreach($kontrakList as $kontrak)
@if($kontrak->status == 'aktif' && $kontrak->tanggal_berakhir)
<div class="modal fade" id="modalPerpanjang{{ $kontrak->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepegawaian.kontrak.perpanjang', $kontrak) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Perpanjang Kontrak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Kontrak: <strong>{{ $kontrak->nomor_kontrak }}</strong></p>
                    <p>Pegawai: <strong>{{ $kontrak->nama_pegawai }}</strong></p>
                    <p>Berakhir: <strong>{{ $kontrak->tanggal_berakhir->format('d/m/Y') }}</strong></p>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Berakhir Baru <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_berakhir_baru" class="form-control" required min="{{ $kontrak->tanggal_berakhir->addDay()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perpanjang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection

@push('scripts')
<script>
document.getElementById('tipePegawai').addEventListener('change', function() {
    const dosenField = document.getElementById('dosenField');
    const pegawaiField = document.getElementById('pegawaiField');
    
    if (this.value === 'dosen') {
        dosenField.style.display = 'block';
        pegawaiField.style.display = 'none';
    } else if (this.value === 'pegawai') {
        dosenField.style.display = 'none';
        pegawaiField.style.display = 'block';
    } else {
        dosenField.style.display = 'none';
        pegawaiField.style.display = 'none';
    }
});
</script>
@endpush
