@extends('layouts.app')

@section('title', isset($kenaikanPangkat) ? 'Edit Kenaikan Pangkat' : 'Tambah Kenaikan Pangkat')

@section('content')
<div class="page-title">
    <h4>{{ isset($kenaikanPangkat) ? 'Edit' : 'Tambah' }} Kenaikan Pangkat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.kenaikan-pangkat.index') }}">Kenaikan Pangkat</a></li>
            <li class="breadcrumb-item active">{{ isset($kenaikanPangkat) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up-arrow me-2"></i>Form Kenaikan Pangkat
            </div>
            <div class="card-body">
                <form action="{{ isset($kenaikanPangkat) ? route('kepegawaian.kenaikan-pangkat.update', $kenaikanPangkat) : route('kepegawaian.kenaikan-pangkat.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($kenaikanPangkat))
                    @method('PUT')
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" id="tipe_pegawai" class="form-select @error('tipe_pegawai') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="dosen" {{ old('tipe_pegawai', isset($kenaikanPangkat) && $kenaikanPangkat->dosen_id ? 'dosen' : '') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="pegawai" {{ old('tipe_pegawai', isset($kenaikanPangkat) && $kenaikanPangkat->pegawai_id ? 'pegawai' : '') == 'pegawai' ? 'selected' : '' }}>Tenaga Kependidikan</option>
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
                            <input type="hidden" name="dosen_id" id="dosen_id" value="{{ old('dosen_id', $kenaikanPangkat->dosen_id ?? '') }}">
                            <input type="hidden" name="pegawai_id" id="pegawai_id" value="{{ old('pegawai_id', $kenaikanPangkat->pegawai_id ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kenaikan <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                <option value="reguler" {{ old('jenis', $kenaikanPangkat->jenis ?? '') == 'reguler' ? 'selected' : '' }}>Reguler</option>
                                <option value="pilihan" {{ old('jenis', $kenaikanPangkat->jenis ?? '') == 'pilihan' ? 'selected' : '' }}>Pilihan</option>
                                <option value="struktural" {{ old('jenis', $kenaikanPangkat->jenis ?? '') == 'struktural' ? 'selected' : '' }}>Struktural</option>
                                <option value="fungsional" {{ old('jenis', $kenaikanPangkat->jenis ?? '') == 'fungsional' ? 'selected' : '' }}>Fungsional</option>
                                <option value="pengabdian" {{ old('jenis', $kenaikanPangkat->jenis ?? '') == 'pengabdian' ? 'selected' : '' }}>Pengabdian</option>
                            </select>
                            @error('jenis')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Periode <span class="text-danger">*</span></label>
                            <select name="periode" class="form-select @error('periode') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                <option value="april" {{ old('periode', $kenaikanPangkat->periode ?? '') == 'april' ? 'selected' : '' }}>April</option>
                                <option value="oktober" {{ old('periode', $kenaikanPangkat->periode ?? '') == 'oktober' ? 'selected' : '' }}>Oktober</option>
                            </select>
                            @error('periode')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" class="form-select @error('tahun') is-invalid @enderror" required>
                                @for($y = date('Y') + 1; $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ old('tahun', $kenaikanPangkat->tahun ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            @error('tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3"><i class="bi bi-arrow-bar-left me-2"></i>Pangkat Lama</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Pangkat Lama</label>
                            <input type="text" name="pangkat_lama" class="form-control @error('pangkat_lama') is-invalid @enderror" value="{{ old('pangkat_lama', $kenaikanPangkat->pangkat_lama ?? '') }}" placeholder="Contoh: Penata Muda">
                            @error('pangkat_lama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Golongan Lama</label>
                            <select name="golongan_lama" class="form-select @error('golongan_lama') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                @foreach(['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'] as $gol)
                                <option value="{{ $gol }}" {{ old('golongan_lama', $kenaikanPangkat->golongan_lama ?? '') == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                @endforeach
                            </select>
                            @error('golongan_lama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3"><i class="bi bi-arrow-bar-right me-2"></i>Pangkat Baru</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Pangkat Baru <span class="text-danger">*</span></label>
                            <input type="text" name="pangkat_baru" class="form-control @error('pangkat_baru') is-invalid @enderror" value="{{ old('pangkat_baru', $kenaikanPangkat->pangkat_baru ?? '') }}" placeholder="Contoh: Penata Muda Tk.I" required>
                            @error('pangkat_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Golongan Baru <span class="text-danger">*</span></label>
                            <select name="golongan_baru" class="form-select @error('golongan_baru') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @foreach(['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'] as $gol)
                                <option value="{{ $gol }}" {{ old('golongan_baru', $kenaikanPangkat->golongan_baru ?? '') == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                @endforeach
                            </select>
                            @error('golongan_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">TMT (Terhitung Mulai Tanggal)</label>
                            <input type="date" name="tmt" class="form-control @error('tmt') is-invalid @enderror" value="{{ old('tmt', $kenaikanPangkat->tmt ?? '') }}">
                            @error('tmt')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. SK</label>
                            <input type="text" name="no_sk" class="form-control @error('no_sk') is-invalid @enderror" value="{{ old('no_sk', $kenaikanPangkat->no_sk ?? '') }}">
                            @error('no_sk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal SK</label>
                            <input type="date" name="tanggal_sk" class="form-control @error('tanggal_sk') is-invalid @enderror" value="{{ old('tanggal_sk', $kenaikanPangkat->tanggal_sk ?? '') }}">
                            @error('tanggal_sk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Angka Kredit</label>
                        <input type="number" step="0.01" name="angka_kredit" class="form-control @error('angka_kredit') is-invalid @enderror" value="{{ old('angka_kredit', $kenaikanPangkat->angka_kredit ?? '') }}">
                        <div class="form-text">Untuk kenaikan pangkat fungsional</div>
                        @error('angka_kredit')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dokumen SK</label>
                        <input type="file" name="dokumen_sk" class="form-control @error('dokumen_sk') is-invalid @enderror" accept=".pdf">
                        <div class="form-text">Format: PDF. Maks: 5MB</div>
                        @if(isset($kenaikanPangkat) && $kenaikanPangkat->dokumen_sk)
                        <div class="mt-2">
                            <a href="{{ Storage::url($kenaikanPangkat->dokumen_sk) }}" target="_blank" class="btn btn-sm btn-outline-primary">
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
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan', $kenaikanPangkat->keterangan ?? '') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>{{ isset($kenaikanPangkat) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('kepegawaian.kenaikan-pangkat.index') }}" class="btn btn-secondary">
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
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <h6 class="small">Jenis Kenaikan Pangkat:</h6>
                <ul class="small mb-3">
                    <li><strong>Reguler:</strong> Kenaikan setiap 4 tahun sekali</li>
                    <li><strong>Pilihan:</strong> Kenaikan karena prestasi kerja luar biasa</li>
                    <li><strong>Struktural:</strong> Kenaikan untuk jabatan struktural</li>
                    <li><strong>Fungsional:</strong> Kenaikan berdasarkan angka kredit</li>
                    <li><strong>Pengabdian:</strong> Kenaikan menjelang pensiun</li>
                </ul>
                <hr>
                <h6 class="small">Periode:</h6>
                <p class="small mb-0">Kenaikan pangkat dilaksanakan pada periode <strong>April</strong> dan <strong>Oktober</strong> setiap tahunnya.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dosenList = @json($dosen ?? []);
const pegawaiList = @json($pegawai ?? []);
const editMode = {{ isset($kenaikanPangkat) ? 'true' : 'false' }};
const currentDosenId = '{{ $kenaikanPangkat->dosen_id ?? '' }}';
const currentPegawaiId = '{{ $kenaikanPangkat->pegawai_id ?? '' }}';

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
