@extends('layouts.app')

@section('title', 'Edit Riwayat Pendidikan')

@section('content')
<div class="page-title">
    <h4>Edit Riwayat Pendidikan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.dashboard') }}">Kepegawaian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kepegawaian.pegawai.riwayat.index', $pegawai) }}">{{ $pegawai->nama }}</a></li>
            <li class="breadcrumb-item active">Edit Pendidikan</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-mortarboard me-2"></i>Form Riwayat Pendidikan
            </div>
            <div class="card-body">
                <form action="{{ route('kepegawaian.pegawai.riwayat.pendidikan.update', [$pegawai, $pendidikan]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jenjang" class="form-label">Jenjang <span class="text-danger">*</span></label>
                                <select name="jenjang" id="jenjang" class="form-select @error('jenjang') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenjang --</option>
                                    @foreach(['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3', 'Profesi', 'Spesialis'] as $j)
                                    <option value="{{ $j }}" {{ old('jenjang', $pendidikan->jenjang) == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                                @error('jenjang')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nama_institusi" class="form-label">Nama Institusi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_institusi" id="nama_institusi" class="form-control @error('nama_institusi') is-invalid @enderror" value="{{ old('nama_institusi', $pendidikan->nama_institusi) }}" required>
                                @error('nama_institusi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Jurusan/Program Studi</label>
                        <input type="text" name="jurusan" id="jurusan" class="form-control @error('jurusan') is-invalid @enderror" value="{{ old('jurusan', $pendidikan->jurusan) }}">
                        @error('jurusan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tahun_masuk" class="form-label">Tahun Masuk <span class="text-danger">*</span></label>
                                <input type="number" name="tahun_masuk" id="tahun_masuk" class="form-control @error('tahun_masuk') is-invalid @enderror" value="{{ old('tahun_masuk', $pendidikan->tahun_masuk) }}" min="1950" max="{{ date('Y') }}" required>
                                @error('tahun_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                                <input type="number" name="tahun_lulus" id="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus', $pendidikan->tahun_lulus) }}" min="1950" max="{{ date('Y') }}">
                                @error('tahun_lulus')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="ipk" class="form-label">IPK</label>
                                <input type="number" step="0.01" name="ipk" id="ipk" class="form-control @error('ipk') is-invalid @enderror" value="{{ old('ipk', $pendidikan->ipk) }}" min="0" max="4">
                                @error('ipk')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_ijazah" class="form-label">No. Ijazah</label>
                                <input type="text" name="no_ijazah" id="no_ijazah" class="form-control @error('no_ijazah') is-invalid @enderror" value="{{ old('no_ijazah', $pendidikan->no_ijazah) }}">
                                @error('no_ijazah')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_ijazah" class="form-label">Tanggal Ijazah</label>
                                <input type="date" name="tanggal_ijazah" id="tanggal_ijazah" class="form-control @error('tanggal_ijazah') is-invalid @enderror" value="{{ old('tanggal_ijazah', $pendidikan->tanggal_ijazah) }}">
                                @error('tanggal_ijazah')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_ijazah" class="form-label">File Ijazah</label>
                        @if($pendidikan->file_ijazah)
                        <div class="mb-2">
                            <a href="{{ asset('storage/'.$pendidikan->file_ijazah) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Lihat File Saat Ini
                            </a>
                        </div>
                        @endif
                        <input type="file" name="file_ijazah" id="file_ijazah" class="form-control @error('file_ijazah') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maks: 2MB. Kosongkan jika tidak ingin mengubah file.</small>
                        @error('file_ijazah')
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
