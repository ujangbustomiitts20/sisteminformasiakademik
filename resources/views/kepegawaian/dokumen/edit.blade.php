@extends('layouts.app')

@section('title', 'Edit Dokumen Kepegawaian - ' . $dosen->nama)

@section('content')
<div class="page-title">
    <h4>Edit Dokumen Kepegawaian</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.index', $dosen) }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Edit Dokumen</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-file-earmark-text me-2"></i>Edit Dokumen Kepegawaian - {{ $dosen->nama }}
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.dokumen.update', [$dosen, $dokumen]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenis_dokumen" class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                        <select name="jenis_dokumen" id="jenis_dokumen" class="form-select @error('jenis_dokumen') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Dokumen --</option>
                            <option value="KTP" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'KTP' ? 'selected' : '' }}>KTP</option>
                            <option value="KK" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'KK' ? 'selected' : '' }}>Kartu Keluarga</option>
                            <option value="NPWP" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'NPWP' ? 'selected' : '' }}>NPWP</option>
                            <option value="BPJS Kesehatan" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'BPJS Kesehatan' ? 'selected' : '' }}>BPJS Kesehatan</option>
                            <option value="BPJS Ketenagakerjaan" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'BPJS Ketenagakerjaan' ? 'selected' : '' }}>BPJS Ketenagakerjaan</option>
                            <option value="SK CPNS" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'SK CPNS' ? 'selected' : '' }}>SK CPNS</option>
                            <option value="SK PNS" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'SK PNS' ? 'selected' : '' }}>SK PNS</option>
                            <option value="SK Pengangkatan" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'SK Pengangkatan' ? 'selected' : '' }}>SK Pengangkatan</option>
                            <option value="Surat Kontrak" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Surat Kontrak' ? 'selected' : '' }}>Surat Kontrak Kerja</option>
                            <option value="Sertifikasi Dosen" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Sertifikasi Dosen' ? 'selected' : '' }}>Sertifikasi Dosen</option>
                            <option value="NIDN" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'NIDN' ? 'selected' : '' }}>SK NIDN</option>
                            <option value="Paspor" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Paspor' ? 'selected' : '' }}>Paspor</option>
                            <option value="Akta Nikah" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Akta Nikah' ? 'selected' : '' }}>Akta Nikah</option>
                            <option value="Akta Kelahiran" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Akta Kelahiran' ? 'selected' : '' }}>Akta Kelahiran Anak</option>
                            <option value="Foto" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Foto' ? 'selected' : '' }}>Pas Foto</option>
                            <option value="Lainnya" {{ old('jenis_dokumen', $dokumen->jenis_dokumen) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
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
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="no_dokumen" class="form-label">No. Dokumen</label>
                        <input type="text" name="no_dokumen" id="no_dokumen" class="form-control @error('no_dokumen') is-invalid @enderror" value="{{ old('no_dokumen', $dokumen->no_dokumen) }}">
                        @error('no_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
                        <input type="date" name="tanggal_terbit" id="tanggal_terbit" class="form-control @error('tanggal_terbit') is-invalid @enderror" value="{{ old('tanggal_terbit', $dokumen->tanggal_terbit ? $dokumen->tanggal_terbit->format('Y-m-d') : '') }}">
                        @error('tanggal_terbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_berlaku" class="form-label">Masa Berlaku s/d</label>
                        <input type="date" name="tanggal_berlaku" id="tanggal_berlaku" class="form-control @error('tanggal_berlaku') is-invalid @enderror" value="{{ old('tanggal_berlaku', $dokumen->tanggal_berlaku ? $dokumen->tanggal_berlaku->format('Y-m-d') : '') }}">
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
                        <label for="file_dokumen" class="form-label">File Dokumen</label>
                        <input type="file" name="file_dokumen" id="file_dokumen" class="form-control @error('file_dokumen') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF, JPG, PNG. Max: 5MB</small>
                        @if($dokumen->file_dokumen)
                        <div class="mt-2">
                            <a href="{{ Storage::url($dokumen->file_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-file-earmark me-1"></i>Lihat File Dokumen
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="penerbit" class="form-label">Instansi Penerbit</label>
                        <input type="text" name="penerbit" id="penerbit" class="form-control @error('penerbit') is-invalid @enderror" value="{{ old('penerbit', $dokumen->penerbit) }}">
                        @error('penerbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            @if($dokumen->tanggal_berlaku)
            <div class="row">
                <div class="col-md-12">
                    <div class="alert {{ $dokumen->isValid() ? 'alert-success' : 'alert-warning' }}">
                        <i class="bi bi-{{ $dokumen->isValid() ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                        @if($dokumen->isValid())
                            Dokumen ini masih berlaku hingga {{ $dokumen->tanggal_berlaku->format('d/m/Y') }}
                        @else
                            <strong>Perhatian:</strong> Dokumen ini telah kedaluwarsa sejak {{ $dokumen->tanggal_berlaku->format('d/m/Y') }}. Segera perbarui dokumen ini.
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $dokumen->keterangan) }}</textarea>
                @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('kepegawaian.index', $dosen) }}#dokumen" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
