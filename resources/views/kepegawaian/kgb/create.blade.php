@extends('layouts.app')

@section('title', isset($kgb) ? 'Edit KGB' : 'Tambah KGB')

@section('content')
<div class="page-title">
    <h4>{{ isset($kgb) ? 'Edit' : 'Tambah' }} Kenaikan Gaji Berkala</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.kgb.index') }}">KGB</a></li>
            <li class="breadcrumb-item active">{{ isset($kgb) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-stack me-2"></i>Form KGB
            </div>
            <div class="card-body">
                <form action="{{ isset($kgb) ? route('kepegawaian.kgb.update', $kgb) : route('kepegawaian.kgb.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($kgb))
                    @method('PUT')
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                            <select name="tipe_pegawai" id="tipe_pegawai" class="form-select @error('tipe_pegawai') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="dosen" {{ old('tipe_pegawai', isset($kgb) && $kgb->dosen_id ? 'dosen' : '') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="pegawai" {{ old('tipe_pegawai', isset($kgb) && $kgb->pegawai_id ? 'pegawai' : '') == 'pegawai' ? 'selected' : '' }}>Tenaga Kependidikan</option>
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
                            <input type="hidden" name="dosen_id" id="dosen_id" value="{{ old('dosen_id', $kgb->dosen_id ?? '') }}">
                            <input type="hidden" name="pegawai_id" id="pegawai_id" value="{{ old('pegawai_id', $kgb->pegawai_id ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">TMT KGB <span class="text-danger">*</span></label>
                            <input type="date" name="tmt_kgb" class="form-control @error('tmt_kgb') is-invalid @enderror" value="{{ old('tmt_kgb', $kgb->tmt_kgb ?? '') }}" required>
                            @error('tmt_kgb')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">TMT KGB Berikutnya</label>
                            <input type="date" name="tmt_kgb_berikutnya" class="form-control @error('tmt_kgb_berikutnya') is-invalid @enderror" value="{{ old('tmt_kgb_berikutnya', $kgb->tmt_kgb_berikutnya ?? '') }}">
                            <div class="form-text">Otomatis 2 tahun dari TMT KGB</div>
                            @error('tmt_kgb_berikutnya')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Golongan Ruang <span class="text-danger">*</span></label>
                            <select name="golongan_ruang" class="form-select @error('golongan_ruang') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @foreach(['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'] as $gol)
                                <option value="{{ $gol }}" {{ old('golongan_ruang', $kgb->golongan_ruang ?? '') == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                @endforeach
                            </select>
                            @error('golongan_ruang')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok Lama</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="gaji_pokok_lama" class="form-control @error('gaji_pokok_lama') is-invalid @enderror" value="{{ old('gaji_pokok_lama', $kgb->gaji_pokok_lama ?? '') }}">
                            </div>
                            @error('gaji_pokok_lama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok Baru</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="gaji_pokok_baru" class="form-control @error('gaji_pokok_baru') is-invalid @enderror" value="{{ old('gaji_pokok_baru', $kgb->gaji_pokok_baru ?? '') }}">
                            </div>
                            @error('gaji_pokok_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Masa Kerja Tahun</label>
                            <div class="input-group">
                                <input type="number" name="masa_kerja_tahun" class="form-control @error('masa_kerja_tahun') is-invalid @enderror" value="{{ old('masa_kerja_tahun', $kgb->masa_kerja_tahun ?? '') }}" min="0">
                                <span class="input-group-text">tahun</span>
                            </div>
                            @error('masa_kerja_tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Masa Kerja Bulan</label>
                            <div class="input-group">
                                <input type="number" name="masa_kerja_bulan" class="form-control @error('masa_kerja_bulan') is-invalid @enderror" value="{{ old('masa_kerja_bulan', $kgb->masa_kerja_bulan ?? '') }}" min="0" max="11">
                                <span class="input-group-text">bulan</span>
                            </div>
                            @error('masa_kerja_bulan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. SK KGB</label>
                            <input type="text" name="no_sk" class="form-control @error('no_sk') is-invalid @enderror" value="{{ old('no_sk', $kgb->no_sk ?? '') }}">
                            @error('no_sk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dokumen SK KGB</label>
                        <input type="file" name="dokumen_sk" class="form-control @error('dokumen_sk') is-invalid @enderror" accept=".pdf">
                        <div class="form-text">Format: PDF. Maks: 5MB</div>
                        @if(isset($kgb) && $kgb->dokumen_sk)
                        <div class="mt-2">
                            <a href="{{ Storage::url($kgb->dokumen_sk) }}" target="_blank" class="btn btn-sm btn-outline-primary">
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
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan', $kgb->keterangan ?? '') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>{{ isset($kgb) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('kepegawaian.kgb.index') }}" class="btn btn-secondary">
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
                <i class="bi bi-info-circle me-2"></i>Informasi KGB
            </div>
            <div class="card-body">
                <p class="small mb-2">Kenaikan Gaji Berkala (KGB) adalah kenaikan gaji pokok pegawai yang diberikan setiap 2 (dua) tahun sekali sesuai ketentuan yang berlaku.</p>
                <hr>
                <h6 class="small">Syarat KGB:</h6>
                <ul class="small mb-0">
                    <li>Telah mencapai masa kerja 2 tahun</li>
                    <li>Penilaian prestasi kerja minimal "Baik"</li>
                    <li>Tidak sedang menjalani hukuman disiplin</li>
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
const editMode = {{ isset($kgb) ? 'true' : 'false' }};
const currentDosenId = '{{ $kgb->dosen_id ?? '' }}';
const currentPegawaiId = '{{ $kgb->pegawai_id ?? '' }}';

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

// Auto calculate TMT KGB Berikutnya (2 years from TMT KGB)
document.querySelector('input[name="tmt_kgb"]').addEventListener('change', function() {
    const tmtKgb = new Date(this.value);
    if (!isNaN(tmtKgb.getTime())) {
        tmtKgb.setFullYear(tmtKgb.getFullYear() + 2);
        document.querySelector('input[name="tmt_kgb_berikutnya"]').value = tmtKgb.toISOString().split('T')[0];
    }
});
</script>
@endpush
