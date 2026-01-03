@extends('layouts.app')

@section('title', 'Tambah Riwayat Pendidikan - ' . $dosen->nama)

@section('content')
<div class="page-title">
    <h4>Tambah Riwayat Pendidikan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.index', $dosen) }}">Kepegawaian</a></li>
            <li class="breadcrumb-item active">Tambah Pendidikan</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-mortarboard me-2"></i>Form Riwayat Pendidikan - {{ $dosen->nama }}
    </div>
    <div class="card-body">
        <form action="{{ route('kepegawaian.pendidikan.store', $dosen) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenjang" class="form-label">Jenjang <span class="text-danger">*</span></label>
                        <select name="jenjang" id="jenjang" class="form-select @error('jenjang') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenjang --</option>
                            <option value="D3" {{ old('jenjang') == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="D4" {{ old('jenjang') == 'D4' ? 'selected' : '' }}>D4</option>
                            <option value="S1" {{ old('jenjang') == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('jenjang') == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('jenjang') == 'S3' ? 'selected' : '' }}>S3</option>
                            <option value="Profesi" {{ old('jenjang') == 'Profesi' ? 'selected' : '' }}>Profesi</option>
                            <option value="Spesialis" {{ old('jenjang') == 'Spesialis' ? 'selected' : '' }}>Spesialis</option>
                        </select>
                        @error('jenjang')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama_institusi" class="form-label">Nama Institusi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_institusi" id="nama_institusi" class="form-control @error('nama_institusi') is-invalid @enderror" value="{{ old('nama_institusi') }}" required>
                        @error('nama_institusi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="program_studi" class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <input type="text" name="program_studi" id="program_studi" class="form-control @error('program_studi') is-invalid @enderror" value="{{ old('program_studi') }}" required>
                        @error('program_studi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                        <input type="number" name="tahun_masuk" id="tahun_masuk" class="form-control @error('tahun_masuk') is-invalid @enderror" value="{{ old('tahun_masuk') }}" min="1950" max="{{ date('Y') }}">
                        @error('tahun_masuk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="tahun_lulus" class="form-label">Tahun Lulus <span class="text-danger">*</span></label>
                        <input type="number" name="tahun_lulus" id="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus') }}" required min="1950" max="{{ date('Y') }}">
                        @error('tahun_lulus')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="no_ijazah" class="form-label">No. Ijazah</label>
                        <input type="text" name="no_ijazah" id="no_ijazah" class="form-control @error('no_ijazah') is-invalid @enderror" value="{{ old('no_ijazah') }}">
                        @error('no_ijazah')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_ijazah" class="form-label">Tanggal Ijazah</label>
                        <input type="date" name="tanggal_ijazah" id="tanggal_ijazah" class="form-control @error('tanggal_ijazah') is-invalid @enderror" value="{{ old('tanggal_ijazah') }}">
                        @error('tanggal_ijazah')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="ipk" class="form-label">IPK</label>
                        <input type="number" step="0.01" name="ipk" id="ipk" class="form-control @error('ipk') is-invalid @enderror" value="{{ old('ipk') }}" min="0" max="4">
                        @error('ipk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="judul_tugas_akhir" class="form-label">Judul Tugas Akhir/Thesis/Disertasi</label>
                <textarea name="judul_tugas_akhir" id="judul_tugas_akhir" class="form-control @error('judul_tugas_akhir') is-invalid @enderror" rows="2">{{ old('judul_tugas_akhir') }}</textarea>
                @error('judul_tugas_akhir')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="file_ijazah" class="form-label">File Ijazah</label>
                        <input type="file" name="file_ijazah" id="file_ijazah" class="form-control @error('file_ijazah') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_ijazah')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="file_transkrip" class="form-label">File Transkrip</label>
                        <input type="file" name="file_transkrip" id="file_transkrip" class="form-control @error('file_transkrip') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_transkrip')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <a href="{{ route('kepegawaian.index', $dosen) }}#pendidikan" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
