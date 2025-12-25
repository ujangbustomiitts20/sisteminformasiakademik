@extends('layouts.app')

@section('title', ($pertemuan ? 'Edit' : 'Tambah') . ' Pertemuan Ke-' . $pertemuanKe)

@section('content')
<div class="page-title">
    <h4>{{ $pertemuan ? 'Edit' : 'Tambah' }} Pertemuan Ke-{{ $pertemuanKe }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pertemuan.index', $jadwalKuliah) }}">Pertemuan</a></li>
            <li class="breadcrumb-item active">{{ $pertemuan ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-text me-2"></i>{{ $jadwalKuliah->mataKuliah->nama }} - Pertemuan Ke-{{ $pertemuanKe }}
            </div>
            <div class="card-body">
                <form action="{{ route('pertemuan.store', [$jadwalKuliah, $pertemuanKe]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Pertemuan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" 
                            value="{{ old('judul', $pertemuan?->judul) }}" 
                            placeholder="Contoh: Pengenalan Algoritma dan Pemrograman" required>
                        @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis Pertemuan <span class="text-danger">*</span></label>
                                <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                    @foreach($jenisOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('jenis', $pertemuan?->jenis ?? 'Materi') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                                    value="{{ old('tanggal', $pertemuan?->tanggal?->format('Y-m-d')) }}">
                                @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                            rows="4" placeholder="Deskripsi materi atau kegiatan pertemuan...">{{ old('deskripsi', $pertemuan?->deskripsi) }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-paperclip me-2"></i>Lampiran Materi</h6>

                    <div class="mb-3">
                        <label for="file_materi" class="form-label">Upload File Materi</label>
                        <input type="file" name="file_materi" id="file_materi" class="form-control @error('file_materi') is-invalid @enderror">
                        <div class="form-text">Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP. Maksimal 10MB</div>
                        @if($pertemuan?->file_materi)
                        <div class="mt-2">
                            <span class="badge bg-info">
                                <i class="bi bi-file-earmark me-1"></i>File tersimpan: {{ basename($pertemuan->file_materi) }}
                            </span>
                        </div>
                        @endif
                        @error('file_materi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="link_materi" class="form-label">Link Materi (URL)</label>
                        <input type="url" name="link_materi" id="link_materi" class="form-control @error('link_materi') is-invalid @enderror" 
                            value="{{ old('link_materi', $pertemuan?->link_materi) }}"
                            placeholder="https://drive.google.com/... atau https://youtube.com/...">
                        @error('link_materi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1"
                                {{ old('is_published', $pertemuan?->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">
                                <strong>Publikasikan ke Mahasiswa</strong>
                                <br><small class="text-muted">Jika dicentang, mahasiswa dapat melihat pertemuan ini</small>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('pertemuan.index', $jadwalKuliah) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="100">Mata Kuliah</td>
                        <td>: <strong>{{ $jadwalKuliah->mataKuliah->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td>Kode</td>
                        <td>: {{ $jadwalKuliah->mataKuliah->kode }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: {{ $jadwalKuliah->kelas }}</td>
                    </tr>
                    <tr>
                        <td>Pertemuan</td>
                        <td>: Ke-{{ $pertemuanKe }} dari {{ $jadwalKuliah->mataKuliah->jumlah_pertemuan ?? 16 }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-lightbulb me-2"></i>Tips
            </div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li class="mb-2">Pilih <strong>jenis pertemuan</strong> yang sesuai (Materi, Tugas, Quiz, dll)</li>
                    <li class="mb-2">Upload file materi atau berikan link untuk referensi mahasiswa</li>
                    <li class="mb-2"><strong>Publikasikan</strong> agar mahasiswa dapat melihat pertemuan ini</li>
                    <li>Pertemuan yang tidak dipublikasikan hanya terlihat oleh dosen</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
