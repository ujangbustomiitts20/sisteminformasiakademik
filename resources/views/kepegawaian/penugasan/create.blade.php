@extends('layouts.app')

@section('title', isset($penugasan) ? 'Edit Penugasan/Mutasi' : 'Tambah Penugasan/Mutasi')

@section('content')
<div class="page-title">
    <h4>{{ isset($penugasan) ? 'Edit' : 'Tambah' }} Penugasan/Mutasi</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.penugasan.index') }}">Penugasan & Mutasi</a></li>
            <li class="breadcrumb-item active">{{ isset($penugasan) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-arrow-left-right me-2"></i>Form Penugasan/Mutasi
            </div>
            <div class="card-body">
                <form action="{{ isset($penugasan) ? route('kepegawaian.penugasan.update', $penugasan) : route('kepegawaian.penugasan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($penugasan))
                    @method('PUT')
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" id="tipe_pegawai" class="form-select @error('tipe_pegawai') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="dosen" {{ old('tipe_pegawai', isset($penugasan) && $penugasan->dosen_id ? 'dosen' : '') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="pegawai" {{ old('tipe_pegawai', isset($penugasan) && $penugasan->pegawai_id ? 'pegawai' : '') == 'pegawai' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                            </select>
                            @error('tipe_pegawai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilih Pegawai <span class="text-danger">*</span></label>
                            <select name="pegawai_selected" id="pegawai_selected" class="form-select" required>
                                <option value="">-- Pilih Pegawai --</option>
                            </select>
                            <input type="hidden" name="dosen_id" id="dosen_id" value="{{ old('dosen_id', $penugasan->dosen_id ?? '') }}">
                            <input type="hidden" name="pegawai_id" id="pegawai_id" value="{{ old('pegawai_id', $penugasan->pegawai_id ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="penugasan" {{ old('jenis', $penugasan->jenis ?? '') == 'penugasan' ? 'selected' : '' }}>Penugasan</option>
                                <option value="mutasi" {{ old('jenis', $penugasan->jenis ?? '') == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                                <option value="promosi" {{ old('jenis', $penugasan->jenis ?? '') == 'promosi' ? 'selected' : '' }}>Promosi</option>
                                <option value="demosi" {{ old('jenis', $penugasan->jenis ?? '') == 'demosi' ? 'selected' : '' }}>Demosi</option>
                                <option value="rotasi" {{ old('jenis', $penugasan->jenis ?? '') == 'rotasi' ? 'selected' : '' }}>Rotasi</option>
                            </select>
                            @error('jenis')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. SK</label>
                            <input type="text" name="no_sk" class="form-control @error('no_sk') is-invalid @enderror" value="{{ old('no_sk', $penugasan->no_sk ?? '') }}" placeholder="Nomor SK">
                            @error('no_sk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Unit Kerja Asal</label>
                            <select name="unit_kerja_asal_id" class="form-select @error('unit_kerja_asal_id') is-invalid @enderror">
                                <option value="">-- Pilih Unit Asal --</option>
                                @foreach($unitKerja as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_kerja_asal_id', $penugasan->unit_kerja_asal_id ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
                                @endforeach
                            </select>
                            @error('unit_kerja_asal_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Kerja Tujuan <span class="text-danger">*</span></label>
                            <select name="unit_kerja_tujuan_id" class="form-select @error('unit_kerja_tujuan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Unit Tujuan --</option>
                                @foreach($unitKerja as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_kerja_tujuan_id', $penugasan->unit_kerja_tujuan_id ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
                                @endforeach
                            </select>
                            @error('unit_kerja_tujuan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jabatan Lama</label>
                            <input type="text" name="jabatan_lama" class="form-control @error('jabatan_lama') is-invalid @enderror" value="{{ old('jabatan_lama', $penugasan->jabatan_lama ?? '') }}">
                            @error('jabatan_lama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan Baru</label>
                            <input type="text" name="jabatan_baru" class="form-control @error('jabatan_baru') is-invalid @enderror" value="{{ old('jabatan_baru', $penugasan->jabatan_baru ?? '') }}">
                            @error('jabatan_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">TMT (Terhitung Mulai Tanggal) <span class="text-danger">*</span></label>
                            <input type="date" name="tmt" class="form-control @error('tmt') is-invalid @enderror" value="{{ old('tmt', isset($penugasan) ? $penugasan->tmt : '') }}" required>
                            @error('tmt')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal SK</label>
                            <input type="date" name="tanggal_sk" class="form-control @error('tanggal_sk') is-invalid @enderror" value="{{ old('tanggal_sk', $penugasan->tanggal_sk ?? '') }}">
                            @error('tanggal_sk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Berakhir</label>
                            <input type="date" name="tanggal_berakhir" class="form-control @error('tanggal_berakhir') is-invalid @enderror" value="{{ old('tanggal_berakhir', $penugasan->tanggal_berakhir ?? '') }}">
                            <div class="form-text">Kosongkan jika tidak ada batas waktu</div>
                            @error('tanggal_berakhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan/Dasar</label>
                        <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" placeholder="Alasan atau dasar penugasan/mutasi...">{{ old('alasan', $penugasan->alasan ?? '') }}</textarea>
                        @error('alasan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dokumen SK</label>
                        <input type="file" name="dokumen_sk" class="form-control @error('dokumen_sk') is-invalid @enderror" accept=".pdf">
                        <div class="form-text">Format: PDF. Maks: 5MB</div>
                        @if(isset($penugasan) && $penugasan->dokumen_sk)
                        <div class="mt-2">
                            <a href="{{ Storage::url($penugasan->dokumen_sk) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Lihat Dokumen
                            </a>
                        </div>
                        @endif
                        @error('dokumen_sk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan', $penugasan->keterangan ?? '') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>{{ isset($penugasan) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('kepegawaian.penugasan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Keterangan Jenis
            </div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li><strong>Penugasan:</strong> Penugasan sementara ke unit kerja lain</li>
                    <li><strong>Mutasi:</strong> Perpindahan permanen ke unit kerja lain</li>
                    <li><strong>Promosi:</strong> Kenaikan jabatan</li>
                    <li><strong>Demosi:</strong> Penurunan jabatan</li>
                    <li><strong>Rotasi:</strong> Perputaran jabatan dalam level yang sama</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dosenList = @json($dosen ?? []);
const pegawaiList = @json($pegawai ?? []);
const editMode = {{ isset($penugasan) ? 'true' : 'false' }};
const currentDosenId = '{{ $penugasan->dosen_id ?? '' }}';
const currentPegawaiId = '{{ $penugasan->pegawai_id ?? '' }}';

document.addEventListener('DOMContentLoaded', function() {
    if (editMode) {
        const tipePegawai = currentDosenId ? 'dosen' : 'pegawai';
        document.getElementById('tipe_pegawai').value = tipePegawai;
        loadPegawai(tipePegawai, currentDosenId || currentPegawaiId);
    }
});

document.getElementById('tipe_pegawai').addEventListener('change', function() {
    loadPegawai(this.value);
});

function loadPegawai(tipePegawai, selectedId = null) {
    const select = document.getElementById('pegawai_selected');
    select.innerHTML = '<option value="">-- Pilih Pegawai --</option>';
    document.getElementById('dosen_id').value = '';
    document.getElementById('pegawai_id').value = '';
    
    if (tipePegawai === 'dosen') {
        dosenList.forEach(d => {
            const selected = selectedId && d.id == selectedId ? 'selected' : '';
            select.innerHTML += `<option value="${d.id}" ${selected}>${d.nama_lengkap} (${d.nidn || d.nip})</option>`;
        });
        if (selectedId) document.getElementById('dosen_id').value = selectedId;
    } else if (tipePegawai === 'pegawai') {
        pegawaiList.forEach(p => {
            const selected = selectedId && p.id == selectedId ? 'selected' : '';
            select.innerHTML += `<option value="${p.id}" ${selected}>${p.nama} (${p.nip})</option>`;
        });
        if (selectedId) document.getElementById('pegawai_id').value = selectedId;
    }
}

document.getElementById('pegawai_selected').addEventListener('change', function() {
    const tipePegawai = document.getElementById('tipe_pegawai').value;
    const value = this.value;
    
    if (tipePegawai === 'dosen') {
        document.getElementById('dosen_id').value = value;
        document.getElementById('pegawai_id').value = '';
    } else {
        document.getElementById('pegawai_id').value = value;
        document.getElementById('dosen_id').value = '';
    }
});
</script>
@endpush
