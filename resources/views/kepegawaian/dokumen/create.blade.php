@extends('layouts.app')

@section('title', 'Tambah Dokumen Kepegawaian - ' . $dosen->nama)

@section('content')
<div class="page-title">
    <h4>Tambah Dokumen Kepegawaian</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.index', $dosen) }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Tambah Dokumen</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-file-earmark-text me-2"></i>Form Dokumen Kepegawaian - {{ $dosen->nama }}
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.dokumen.store', $dosen) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenis_dokumen" class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                        <select name="jenis_dokumen" id="jenis_dokumen" class="form-select @error('jenis_dokumen') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Dokumen --</option>
                            <option value="KTP" {{ old('jenis_dokumen') == 'KTP' ? 'selected' : '' }}>KTP</option>
                            <option value="KK" {{ old('jenis_dokumen') == 'KK' ? 'selected' : '' }}>Kartu Keluarga</option>
                            <option value="NPWP" {{ old('jenis_dokumen') == 'NPWP' ? 'selected' : '' }}>NPWP</option>
                            <option value="BPJS Kesehatan" {{ old('jenis_dokumen') == 'BPJS Kesehatan' ? 'selected' : '' }}>BPJS Kesehatan</option>
                            <option value="BPJS Ketenagakerjaan" {{ old('jenis_dokumen') == 'BPJS Ketenagakerjaan' ? 'selected' : '' }}>BPJS Ketenagakerjaan</option>
                            <option value="SK CPNS" {{ old('jenis_dokumen') == 'SK CPNS' ? 'selected' : '' }}>SK CPNS</option>
                            <option value="SK PNS" {{ old('jenis_dokumen') == 'SK PNS' ? 'selected' : '' }}>SK PNS</option>
                            <option value="SK Pengangkatan" {{ old('jenis_dokumen') == 'SK Pengangkatan' ? 'selected' : '' }}>SK Pengangkatan</option>
                            <option value="Surat Kontrak" {{ old('jenis_dokumen') == 'Surat Kontrak' ? 'selected' : '' }}>Surat Kontrak Kerja</option>
                            <option value="Sertifikasi Dosen" {{ old('jenis_dokumen') == 'Sertifikasi Dosen' ? 'selected' : '' }}>Sertifikasi Dosen</option>
                            <option value="NIDN" {{ old('jenis_dokumen') == 'NIDN' ? 'selected' : '' }}>SK NIDN</option>
                            <option value="Paspor" {{ old('jenis_dokumen') == 'Paspor' ? 'selected' : '' }}>Paspor</option>
                            <option value="Akta Nikah" {{ old('jenis_dokumen') == 'Akta Nikah' ? 'selected' : '' }}>Akta Nikah</option>
                            <option value="Akta Kelahiran" {{ old('jenis_dokumen') == 'Akta Kelahiran' ? 'selected' : '' }}>Akta Kelahiran Anak</option>
                            <option value="Foto" {{ old('jenis_dokumen') == 'Foto' ? 'selected' : '' }}>Pas Foto</option>
                            <option value="Lainnya" {{ old('jenis_dokumen') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama_dokumen" class="form-label">Nama Dokumen <span class="text-danger">*</span></label>
                        <input type="text" name="nama_dokumen" id="nama_dokumen" class="form-control @error('nama_dokumen') is-invalid @enderror" value="{{ old('nama_dokumen') }}" required>
                        @error('nama_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Contoh: KTP - Ahmad Susanto</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="no_dokumen" class="form-label">No. Dokumen</label>
                        <input type="text" name="no_dokumen" id="no_dokumen" class="form-control @error('no_dokumen') is-invalid @enderror" value="{{ old('no_dokumen') }}">
                        @error('no_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
                        <input type="date" name="tanggal_terbit" id="tanggal_terbit" class="form-control @error('tanggal_terbit') is-invalid @enderror" value="{{ old('tanggal_terbit') }}">
                        @error('tanggal_terbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_berlaku" class="form-label">Masa Berlaku s/d</label>
                        <input type="date" name="tanggal_berlaku" id="tanggal_berlaku" class="form-control @error('tanggal_berlaku') is-invalid @enderror" value="{{ old('tanggal_berlaku') }}">
                        @error('tanggal_berlaku')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Kosongkan jika seumur hidup</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="file_dokumen" class="form-label">File Dokumen <span class="text-danger">*</span></label>
                        <input type="file" name="file_dokumen" id="file_dokumen" class="form-control @error('file_dokumen') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                        @error('file_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="penerbit" class="form-label">Instansi Penerbit</label>
                        <input type="text" name="penerbit" id="penerbit" class="form-control @error('penerbit') is-invalid @enderror" value="{{ old('penerbit') }}">
                        @error('penerbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <a href="{{ route('kepegawaian.index', $dosen) }}#dokumen" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-fill nama_dokumen when jenis_dokumen is selected
    document.getElementById('jenis_dokumen').addEventListener('change', function() {
        const namaDokumen = document.getElementById('nama_dokumen');
        const dosenNama = "{{ $dosen->nama }}";
        
        if (this.value && !namaDokumen.value) {
            namaDokumen.value = this.options[this.selectedIndex].text + ' - ' + dosenNama;
        }
    });
</script>
@endpush
@endsection
