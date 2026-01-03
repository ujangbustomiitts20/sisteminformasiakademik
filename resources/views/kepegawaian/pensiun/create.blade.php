@extends('layouts.app')

@section('title', isset($pensiun) ? 'Edit Data Pensiun' : 'Tambah Data Pensiun')

@section('content')
<div class="page-title">
    <h4>{{ isset($pensiun) ? 'Edit' : 'Tambah' }} Data Pensiun</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pensiun.index') }}">Pensiun</a></li>
            <li class="breadcrumb-item active">{{ isset($pensiun) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Form Data Pensiun
            </div>
            <div class="card-body">
                <form action="{{ isset($pensiun) ? route('kepegawaian.pensiun.update', $pensiun) : route('kepegawaian.pensiun.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($pensiun))
                    @method('PUT')
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" id="tipe_pegawai" class="form-select @error('tipe_pegawai') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="dosen" {{ old('tipe_pegawai', isset($pensiun) && $pensiun->dosen_id ? 'dosen' : '') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="pegawai" {{ old('tipe_pegawai', isset($pensiun) && $pensiun->pegawai_id ? 'pegawai' : '') == 'pegawai' ? 'selected' : '' }}>Tenaga Kependidikan</option>
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
                            <input type="hidden" name="dosen_id" id="dosen_id" value="{{ old('dosen_id', $pensiun->dosen_id ?? '') }}">
                            <input type="hidden" name="pegawai_id" id="pegawai_id" value="{{ old('pegawai_id', $pensiun->pegawai_id ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Jenis Pensiun <span class="text-danger">*</span></label>
                            <select name="jenis_pensiun" class="form-select @error('jenis_pensiun') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                <option value="bup" {{ old('jenis_pensiun', $pensiun->jenis_pensiun ?? '') == 'bup' ? 'selected' : '' }}>BUP (Batas Usia Pensiun)</option>
                                <option value="atas_permintaan" {{ old('jenis_pensiun', $pensiun->jenis_pensiun ?? '') == 'atas_permintaan' ? 'selected' : '' }}>Atas Permintaan Sendiri</option>
                                <option value="uzur" {{ old('jenis_pensiun', $pensiun->jenis_pensiun ?? '') == 'uzur' ? 'selected' : '' }}>Uzur/Cacat</option>
                                <option value="duda_janda" {{ old('jenis_pensiun', $pensiun->jenis_pensiun ?? '') == 'duda_janda' ? 'selected' : '' }}>Duda/Janda</option>
                                <option value="meninggal" {{ old('jenis_pensiun', $pensiun->jenis_pensiun ?? '') == 'meninggal' ? 'selected' : '' }}>Meninggal Dunia</option>
                            </select>
                            @error('jenis_pensiun')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Usia BUP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="usia_bup" class="form-control @error('usia_bup') is-invalid @enderror" value="{{ old('usia_bup', $pensiun->usia_bup ?? 60) }}" min="50" max="70" required>
                                <span class="input-group-text">tahun</span>
                            </div>
                            <div class="form-text">Dosen: 65/70 thn, Tendik: 58/60 thn</div>
                            @error('usia_bup')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal BUP <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_bup" class="form-control @error('tanggal_bup') is-invalid @enderror" value="{{ old('tanggal_bup', $pensiun->tanggal_bup ?? '') }}" required>
                            @error('tanggal_bup')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">No. SK Pensiun</label>
                            <input type="text" name="no_sk_pensiun" class="form-control @error('no_sk_pensiun') is-invalid @enderror" value="{{ old('no_sk_pensiun', $pensiun->no_sk_pensiun ?? '') }}">
                            @error('no_sk_pensiun')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal SK Pensiun</label>
                            <input type="date" name="tanggal_sk_pensiun" class="form-control @error('tanggal_sk_pensiun') is-invalid @enderror" value="{{ old('tanggal_sk_pensiun', $pensiun->tanggal_sk_pensiun ?? '') }}">
                            @error('tanggal_sk_pensiun')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3"><i class="bi bi-wallet2 me-2"></i>Informasi Dana Pensiun</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok Terakhir</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="gaji_pokok_terakhir" class="form-control @error('gaji_pokok_terakhir') is-invalid @enderror" value="{{ old('gaji_pokok_terakhir', $pensiun->gaji_pokok_terakhir ?? '') }}">
                            </div>
                            @error('gaji_pokok_terakhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dana Pensiun (Estimasi)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="dana_pensiun" class="form-control @error('dana_pensiun') is-invalid @enderror" value="{{ old('dana_pensiun', $pensiun->dana_pensiun ?? '') }}">
                            </div>
                            @error('dana_pensiun')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">No. TASPEN</label>
                            <input type="text" name="no_taspen" class="form-control @error('no_taspen') is-invalid @enderror" value="{{ old('no_taspen', $pensiun->no_taspen ?? '') }}">
                            @error('no_taspen')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pangkat Terakhir</label>
                            <input type="text" name="pangkat_terakhir" class="form-control @error('pangkat_terakhir') is-invalid @enderror" value="{{ old('pangkat_terakhir', $pensiun->pangkat_terakhir ?? '') }}" placeholder="Contoh: Pembina Utama Muda (IV/c)">
                            @error('pangkat_terakhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Masa Kerja Tahun</label>
                            <div class="input-group">
                                <input type="number" name="masa_kerja_tahun" class="form-control @error('masa_kerja_tahun') is-invalid @enderror" value="{{ old('masa_kerja_tahun', $pensiun->masa_kerja_tahun ?? '') }}" min="0">
                                <span class="input-group-text">tahun</span>
                            </div>
                            @error('masa_kerja_tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Masa Kerja Bulan</label>
                            <div class="input-group">
                                <input type="number" name="masa_kerja_bulan" class="form-control @error('masa_kerja_bulan') is-invalid @enderror" value="{{ old('masa_kerja_bulan', $pensiun->masa_kerja_bulan ?? '') }}" min="0" max="11">
                                <span class="input-group-text">bulan</span>
                            </div>
                            @error('masa_kerja_bulan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3"><i class="bi bi-geo-alt me-2"></i>Alamat Setelah Pensiun</h6>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat_pensiun" class="form-control @error('alamat_pensiun') is-invalid @enderror" rows="2">{{ old('alamat_pensiun', $pensiun->alamat_pensiun ?? '') }}</textarea>
                        @error('alamat_pensiun')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telepon_pensiun" class="form-control @error('no_telepon_pensiun') is-invalid @enderror" value="{{ old('no_telepon_pensiun', $pensiun->no_telepon_pensiun ?? '') }}">
                            @error('no_telepon_pensiun')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Rekening</label>
                            <input type="text" name="no_rekening" class="form-control @error('no_rekening') is-invalid @enderror" value="{{ old('no_rekening', $pensiun->no_rekening ?? '') }}">
                            @error('no_rekening')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dokumen SK Pensiun</label>
                        <input type="file" name="dokumen_sk" class="form-control @error('dokumen_sk') is-invalid @enderror" accept=".pdf">
                        <div class="form-text">Format: PDF. Maks: 5MB</div>
                        @if(isset($pensiun) && $pensiun->dokumen_sk)
                        <div class="mt-2">
                            <a href="{{ Storage::url($pensiun->dokumen_sk) }}" target="_blank" class="btn btn-sm btn-outline-primary">
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
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan', $pensiun->keterangan ?? '') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>{{ isset($pensiun) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('kepegawaian.pensiun.index') }}" class="btn btn-secondary">
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
                <i class="bi bi-info-circle me-2"></i>Informasi BUP
            </div>
            <div class="card-body">
                <h6 class="small">Batas Usia Pensiun (BUP):</h6>
                <ul class="small mb-3">
                    <li><strong>Dosen (Lektor Kepala):</strong> 65 tahun</li>
                    <li><strong>Dosen (Profesor):</strong> 70 tahun</li>
                    <li><strong>Tendik (Non-Struktural):</strong> 58 tahun</li>
                    <li><strong>Tendik (Struktural):</strong> 60 tahun</li>
                </ul>
                <hr>
                <h6 class="small">Jenis Pensiun:</h6>
                <ul class="small mb-0">
                    <li><strong>BUP:</strong> Mencapai batas usia</li>
                    <li><strong>Atas Permintaan:</strong> Pengajuan sendiri</li>
                    <li><strong>Uzur:</strong> Tidak mampu bekerja</li>
                    <li><strong>Duda/Janda:</strong> Pensiun ahli waris</li>
                    <li><strong>Meninggal:</strong> Meninggal dunia</li>
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
const editMode = {{ isset($pensiun) ? 'true' : 'false' }};
const currentDosenId = '{{ $pensiun->dosen_id ?? '' }}';
const currentPegawaiId = '{{ $pensiun->pegawai_id ?? '' }}';

document.addEventListener('DOMContentLoaded', function() {
    if (editMode) {
        const tipePegawai = currentDosenId ? 'dosen' : 'pegawai';
        document.getElementById('tipe_pegawai').value = tipePegawai;
        loadPegawai(tipePegawai, currentDosenId || currentPegawaiId);
    }
});

document.getElementById('tipe_pegawai').addEventListener('change', function() {
    loadPegawai(this.value);
    
    // Set default BUP age based on type
    const usiaBup = document.querySelector('input[name="usia_bup"]');
    if (this.value === 'dosen') {
        usiaBup.value = 65;
    } else if (this.value === 'pegawai') {
        usiaBup.value = 58;
    }
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
