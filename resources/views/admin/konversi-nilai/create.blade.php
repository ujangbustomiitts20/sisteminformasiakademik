@extends('layouts.app')

@section('title', 'Tambah Konversi Nilai')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Konversi Nilai</h1>
        <a href="{{ route('admin.konversi-nilai.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Catatan:</strong> Form ini digunakan untuk mendaftarkan data konversi nilai calon mahasiswa pindahan dari kampus lain. 
        Calon mahasiswa belum terdaftar di sistem, sehingga data diinput manual.
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Data Calon Mahasiswa Pindahan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.konversi-nilai.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Data Calon Mahasiswa -->
                    <div class="col-md-12 mb-4">
                        <h6 class="text-primary border-bottom pb-2"><i class="bi bi-person me-2"></i>Data Calon Mahasiswa</h6>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_calon_mahasiswa" class="form-control @error('nama_calon_mahasiswa') is-invalid @enderror" 
                               value="{{ old('nama_calon_mahasiswa') }}" required placeholder="Nama lengkap calon mahasiswa">
                        @error('nama_calon_mahasiswa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program Studi Tujuan <span class="text-danger">*</span></label>
                        <select name="program_studi_tujuan_id" class="form-select @error('program_studi_tujuan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Program Studi Tujuan --</option>
                            @foreach($programStudis as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('program_studi_tujuan_id') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }} ({{ $prodi->fakultas->nama ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('program_studi_tujuan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email_calon" class="form-control @error('email_calon') is-invalid @enderror" 
                               value="{{ old('email_calon') }}" placeholder="Email calon mahasiswa">
                        @error('email_calon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp_calon" class="form-control @error('no_hp_calon') is-invalid @enderror" 
                               value="{{ old('no_hp_calon') }}" placeholder="Nomor HP calon mahasiswa">
                        @error('no_hp_calon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data Kampus Asal -->
                    <div class="col-md-12 mb-4 mt-3">
                        <h6 class="text-primary border-bottom pb-2"><i class="bi bi-building me-2"></i>Data Kampus Asal</h6>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Universitas/Kampus Asal <span class="text-danger">*</span></label>
                        <input type="text" name="universitas_asal" class="form-control @error('universitas_asal') is-invalid @enderror" 
                               value="{{ old('universitas_asal') }}" required placeholder="Contoh: Universitas ABC">
                        @error('universitas_asal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program Studi Asal <span class="text-danger">*</span></label>
                        <input type="text" name="program_studi_asal" class="form-control @error('program_studi_asal') is-invalid @enderror" 
                               value="{{ old('program_studi_asal') }}" required placeholder="Contoh: Teknik Informatika">
                        @error('program_studi_asal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIM Asal <span class="text-danger">*</span></label>
                        <input type="text" name="nim_asal" class="form-control @error('nim_asal') is-invalid @enderror" 
                               value="{{ old('nim_asal') }}" required placeholder="NIM di kampus asal">
                        @error('nim_asal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Masuk Asal <span class="text-danger">*</span></label>
                        <input type="number" name="tahun_masuk_asal" class="form-control @error('tahun_masuk_asal') is-invalid @enderror" 
                               value="{{ old('tahun_masuk_asal') }}" required min="1990" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                        @error('tahun_masuk_asal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dokumen -->
                    <div class="col-md-12 mb-4 mt-3">
                        <h6 class="text-primary border-bottom pb-2"><i class="bi bi-file-earmark me-2"></i>Dokumen Pendukung</h6>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Transkrip Nilai</label>
                        <input type="file" name="dokumen_transkrip" class="form-control @error('dokumen_transkrip') is-invalid @enderror" 
                               accept=".pdf,.jpg,.png">
                        @error('dokumen_transkrip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF, JPG, PNG (Maks. 5MB)</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Silabus</label>
                        <input type="file" name="dokumen_silabus" class="form-control @error('dokumen_silabus') is-invalid @enderror" 
                               accept=".pdf">
                        @error('dokumen_silabus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF (Maks. 10MB)</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Dokumen Pendukung Lainnya</label>
                        <input type="file" name="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" 
                               accept=".pdf,.zip">
                        @error('dokumen_pendukung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: PDF, ZIP (Maks. 10MB)</small>
                    </div>

                    <!-- Catatan -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" 
                                  rows="3" placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Alur Proses:</strong>
                    <ol class="mb-0 mt-2">
                        <li>Admin membuat data konversi dan menambahkan detail mata kuliah dari kampus asal</li>
                        <li>Admin mengajukan ke Kaprodi untuk diproses</li>
                        <li>Kaprodi memetakan mata kuliah asal ke mata kuliah yang setara dan menentukan nilai konversi</li>
                        <li>Admin finalisasi konversi setelah calon menjadi mahasiswa terdaftar</li>
                    </ol>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.konversi-nilai.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan & Lanjutkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
