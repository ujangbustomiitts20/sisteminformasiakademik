@extends('layouts.app')

@section('title', 'Edit Dokumen Kepegawaian')

@section('content')
<div class="page-title">
    <h4>Edit Dokumen Kepegawaian</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai) }}">{{ $pegawai->nama }}</a></li>
            <li class="breadcrumb-item active">Edit Dokumen</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark me-2"></i>Form Dokumen Kepegawaian
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.pegawai.riwayat.dokumen.update', [$pegawai, $dokumen]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="jenis_dokumen" class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                                <select name="jenis_dokumen" id="jenis_dokumen" class="form-select @error('jenis_dokumen') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    <optgroup label="Dokumen SK">
                                        <option value="SK_CPNS" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'SK_CPNS' ? 'selected' : '' }}>SK CPNS</option>
                                        <option value="SK_PNS" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'SK_PNS' ? 'selected' : '' }}>SK PNS</option>
                                        <option value="SK_Pengangkatan" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'SK_Pengangkatan' ? 'selected' : '' }}>SK Pengangkatan</option>
                                        <option value="SK_Kenaikan_Pangkat" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'SK_Kenaikan_Pangkat' ? 'selected' : '' }}>SK Kenaikan Pangkat</option>
                                        <option value="SK_Jabatan" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'SK_Jabatan' ? 'selected' : '' }}>SK Jabatan</option>
                                    </optgroup>
                                    <optgroup label="Dokumen Pendidikan">
                                        <option value="Ijazah" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Ijazah' ? 'selected' : '' }}>Ijazah</option>
                                        <option value="Sertifikat" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Sertifikat' ? 'selected' : '' }}>Sertifikat</option>
                                        <option value="Piagam" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Piagam' ? 'selected' : '' }}>Piagam</option>
                                    </optgroup>
                                    <optgroup label="Dokumen Pribadi">
                                        <option value="KTP" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'KTP' ? 'selected' : '' }}>KTP</option>
                                        <option value="KK" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'KK' ? 'selected' : '' }}>Kartu Keluarga</option>
                                        <option value="NPWP" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'NPWP' ? 'selected' : '' }}>NPWP</option>
                                        <option value="BPJS" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'BPJS' ? 'selected' : '' }}>BPJS</option>
                                    </optgroup>
                                    <option value="Lainnya" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('jenis_dokumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="nama_dokumen" class="form-label">Nama Dokumen <span class="text-danger">*</span></label>
                                <input type="text" name="nama_dokumen" id="nama_dokumen" class="form-control @error('nama_dokumen') is-invalid @enderror" value="{{ old('nama_dokumen', $dokumen->nama_dokumen) }}" required>
                                @error('nama_dokumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nomor_dokumen" class="form-label">Nomor Dokumen</label>
                                <input type="text" name="nomor_dokumen" id="nomor_dokumen" class="form-control @error('nomor_dokumen') is-invalid @enderror" value="{{ old('nomor_dokumen', $dokumen->nomor_dokumen) }}">
                                @error('nomor_dokumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="penerbit" class="form-label">Penerbit</label>
                                <input type="text" name="penerbit" id="penerbit" class="form-control @error('penerbit') is-invalid @enderror" value="{{ old('penerbit', $dokumen->penerbit) }}">
                                @error('penerbit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" id="tanggal_terbit" class="form-control @error('tanggal_terbit') is-invalid @enderror" value="{{ old('tanggal_terbit', $dokumen->tanggal_terbit) }}">
                                @error('tanggal_terbit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tanggal_berlaku" class="form-label">Tanggal Berlaku</label>
                                <input type="date" name="tanggal_berlaku" id="tanggal_berlaku" class="form-control @error('tanggal_berlaku') is-invalid @enderror" value="{{ old('tanggal_berlaku', $dokumen->tanggal_berlaku) }}">
                                @error('tanggal_berlaku')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tanggal_expired" class="form-label">Tanggal Expired</label>
                                <input type="date" name="tanggal_expired" id="tanggal_expired" class="form-control @error('tanggal_expired') is-invalid @enderror" value="{{ old('tanggal_expired', $dokumen->tanggal_expired) }}">
                                <small class="text-muted">Kosongkan jika berlaku selamanya</small>
                                @error('tanggal_expired')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_dokumen" class="form-label">File Dokumen</label>
                        @if($dokumen->file_dokumen)
                        <div class="mb-2">
                            <a href="{{ asset('storage/'.$dokumen->file_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Lihat File Saat Ini
                            </a>
                        </div>
                        @endif
                        <input type="file" name="file_dokumen" id="file_dokumen" class="form-control @error('file_dokumen') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maks: 5MB. Kosongkan jika tidak ingin mengubah file.</small>
                        @error('file_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan', $dokumen->keterangan) }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
