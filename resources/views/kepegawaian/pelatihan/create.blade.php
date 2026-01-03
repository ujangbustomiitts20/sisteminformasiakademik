@extends('layouts.app')

@section('title', 'Tambah Riwayat Pelatihan - ' . $dosen->nama)

@section('content')
<div class="page-title">
    <h4>Tambah Riwayat Pelatihan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.index', $dosen) }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Tambah Pelatihan</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-journal-bookmark me-2"></i>Form Riwayat Pelatihan/Sertifikasi - {{ $dosen->nama }}
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.pelatihan.store', $dosen) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
                        <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Diklat" {{ old('jenis') == 'Diklat' ? 'selected' : '' }}>Diklat (Pendidikan & Pelatihan)</option>
                            <option value="Workshop" {{ old('jenis') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="Seminar" {{ old('jenis') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                            <option value="Sertifikasi" {{ old('jenis') == 'Sertifikasi' ? 'selected' : '' }}>Sertifikasi</option>
                            <option value="Kursus" {{ old('jenis') == 'Kursus' ? 'selected' : '' }}>Kursus</option>
                            <option value="Lainnya" {{ old('jenis') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama_pelatihan" class="form-label">Nama Pelatihan/Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pelatihan" id="nama_pelatihan" class="form-control @error('nama_pelatihan') is-invalid @enderror" value="{{ old('nama_pelatihan') }}" required>
                        @error('nama_pelatihan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="penyelenggara" class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                        <input type="text" name="penyelenggara" id="penyelenggara" class="form-control @error('penyelenggara') is-invalid @enderror" value="{{ old('penyelenggara') }}" required>
                        @error('penyelenggara')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tempat" class="form-label">Tempat</label>
                        <input type="text" name="tempat" id="tempat" class="form-control @error('tempat') is-invalid @enderror" value="{{ old('tempat') }}">
                        @error('tempat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" required>
                        @error('tanggal_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="jumlah_jam" class="form-label">Jumlah Jam</label>
                        <input type="number" name="jumlah_jam" id="jumlah_jam" class="form-control @error('jumlah_jam') is-invalid @enderror" value="{{ old('jumlah_jam') }}" min="0">
                        @error('jumlah_jam')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="no_sertifikat" class="form-label">No. Sertifikat</label>
                        <input type="text" name="no_sertifikat" id="no_sertifikat" class="form-control @error('no_sertifikat') is-invalid @enderror" value="{{ old('no_sertifikat') }}">
                        @error('no_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" id="tahun" class="form-control @error('tahun') is-invalid @enderror" value="{{ old('tahun', date('Y')) }}" min="1990" max="{{ date('Y') }}" required>
                        @error('tahun')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="file_sertifikat" class="form-label">File Sertifikat</label>
                        <input type="file" name="file_sertifikat" id="file_sertifikat" class="form-control @error('file_sertifikat') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF, JPG, PNG. Max: 5MB</small>
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
                <a href="{{ route('kepegawaian.index', $dosen) }}#pelatihan" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
