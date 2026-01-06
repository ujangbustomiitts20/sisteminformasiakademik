@extends('layouts.app')

@section('title', 'Tambah Kontrak Kerja')

@section('content')
<div class="page-title">
    <h4>Tambah Kontrak Kerja</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.kontrak.index') }}">Kontrak Kerja</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('kepegawaian.kontrak.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipe Pegawai <span class="text-danger">*</span></label>
                    <select name="tipe_pegawai" id="tipePegawai" class="form-select @error('tipe_pegawai') is-invalid @enderror" required>
                        <option value="">Pilih Tipe</option>
                        <option value="dosen" {{ old('tipe_pegawai') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="pegawai" {{ old('tipe_pegawai') == 'pegawai' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                    </select>
                    @error('tipe_pegawai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3" id="dosenWrapper" style="display: none;">
                    <label class="form-label">Pilih Dosen <span class="text-danger">*</span></label>
                    <select name="dosen_id" id="dosenId" class="form-select @error('dosen_id') is-invalid @enderror">
                        <option value="">Pilih Dosen</option>
                        @foreach($dosenList as $dosen)
                            <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                {{ $dosen->nama }} - {{ $dosen->nidn }}
                            </option>
                        @endforeach
                    </select>
                    @error('dosen_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3" id="pegawaiWrapper" style="display: none;">
                    <label class="form-label">Pilih Pegawai <span class="text-danger">*</span></label>
                    <select name="pegawai_id" id="pegawaiId" class="form-select @error('pegawai_id') is-invalid @enderror">
                        <option value="">Pilih Pegawai</option>
                        @foreach($pegawaiList as $pegawai)
                            <option value="{{ $pegawai->id }}" {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                {{ $pegawai->nama }} - {{ $pegawai->nip }}
                            </option>
                        @endforeach
                    </select>
                    @error('pegawai_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Kontrak <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_kontrak" class="form-control @error('nomor_kontrak') is-invalid @enderror" value="{{ old('nomor_kontrak') }}" required>
                    @error('nomor_kontrak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Kontrak <span class="text-danger">*</span></label>
                    <select name="jenis_kontrak" class="form-select @error('jenis_kontrak') is-invalid @enderror" required>
                        <option value="">Pilih Jenis</option>
                        <option value="pkwt" {{ old('jenis_kontrak') == 'pkwt' ? 'selected' : '' }}>PKWT (Waktu Tertentu)</option>
                        <option value="pkwtt" {{ old('jenis_kontrak') == 'pkwtt' ? 'selected' : '' }}>PKWTT (Waktu Tidak Tertentu)</option>
                        <option value="honorer" {{ old('jenis_kontrak') == 'honorer' ? 'selected' : '' }}>Honorer</option>
                        <option value="magang" {{ old('jenis_kontrak') == 'magang' ? 'selected' : '' }}>Magang</option>
                    </select>
                    @error('jenis_kontrak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                    @error('tanggal_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Berakhir</label>
                    <input type="date" name="tanggal_berakhir" class="form-control @error('tanggal_berakhir') is-invalid @enderror" value="{{ old('tanggal_berakhir') }}">
                    <small class="text-muted">Kosongkan jika tidak terbatas (PKWTT)</small>
                    @error('tanggal_berakhir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="gaji_pokok" class="form-control @error('gaji_pokok') is-invalid @enderror" value="{{ old('gaji_pokok') }}" required>
                        @error('gaji_pokok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">File Kontrak (PDF)</label>
                    <input type="file" name="file_kontrak" class="form-control @error('file_kontrak') is-invalid @enderror" accept=".pdf">
                    @error('file_kontrak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <a href="{{ route('kepegawaian.kontrak.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan Kontrak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('tipePegawai').addEventListener('change', function() {
    const dosenWrapper = document.getElementById('dosenWrapper');
    const pegawaiWrapper = document.getElementById('pegawaiWrapper');
    
    if (this.value === 'dosen') {
        dosenWrapper.style.display = 'block';
        pegawaiWrapper.style.display = 'none';
        document.getElementById('pegawaiId').value = '';
    } else if (this.value === 'pegawai') {
        dosenWrapper.style.display = 'none';
        pegawaiWrapper.style.display = 'block';
        document.getElementById('dosenId').value = '';
    } else {
        dosenWrapper.style.display = 'none';
        pegawaiWrapper.style.display = 'none';
    }
});

// Trigger on page load
document.getElementById('tipePegawai').dispatchEvent(new Event('change'));
</script>
@endpush
