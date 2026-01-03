@extends('layouts.app')

@section('title', 'Edit Konversi Nilai')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Konversi Nilai</h1>
        <a href="{{ route('admin.konversi-nilai.show', $pengajuanKonversi->hashid) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Data Konversi</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.konversi-nilai.update', $pengajuanKonversi->hashid) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Info Mahasiswa -->
                    <div class="col-md-12 mb-4">
                        <h6 class="text-primary border-bottom pb-2">Mahasiswa Tujuan</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mahasiswa</label>
                        <input type="text" class="form-control" 
                               value="{{ $pengajuanKonversi->mahasiswa->nim ?? '-' }} - {{ $pengajuanKonversi->mahasiswa->nama ?? '-' }}" 
                               readonly disabled>
                        <small class="text-muted">Mahasiswa tidak dapat diubah</small>
                    </div>

                    <!-- Data Kampus Asal -->
                    <div class="col-md-12 mb-4 mt-3">
                        <h6 class="text-primary border-bottom pb-2">Data Kampus Asal</h6>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Universitas/Kampus Asal <span class="text-danger">*</span></label>
                        <input type="text" name="universitas_asal" class="form-control @error('universitas_asal') is-invalid @enderror" 
                               value="{{ old('universitas_asal', $pengajuanKonversi->universitas_asal) }}" required>
                        @error('universitas_asal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program Studi Asal <span class="text-danger">*</span></label>
                        <input type="text" name="program_studi_asal" class="form-control @error('program_studi_asal') is-invalid @enderror" 
                               value="{{ old('program_studi_asal', $pengajuanKonversi->program_studi_asal) }}" required>
                        @error('program_studi_asal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIM Asal <span class="text-danger">*</span></label>
                        <input type="text" name="nim_asal" class="form-control @error('nim_asal') is-invalid @enderror" 
                               value="{{ old('nim_asal', $pengajuanKonversi->nim_asal) }}" required>
                        @error('nim_asal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Masuk Asal <span class="text-danger">*</span></label>
                        <input type="number" name="tahun_masuk_asal" class="form-control @error('tahun_masuk_asal') is-invalid @enderror" 
                               value="{{ old('tahun_masuk_asal', $pengajuanKonversi->tahun_masuk_asal) }}" required min="1990" max="{{ date('Y') }}">
                        @error('tahun_masuk_asal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dokumen -->
                    <div class="col-md-12 mb-4 mt-3">
                        <h6 class="text-primary border-bottom pb-2">Dokumen Pendukung</h6>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Transkrip Nilai</label>
                        @if($pengajuanKonversi->dokumen_transkrip)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $pengajuanKonversi->dokumen_transkrip) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-text"></i> Lihat Dokumen
                                </a>
                            </div>
                        @endif
                        <input type="file" name="dokumen_transkrip" class="form-control @error('dokumen_transkrip') is-invalid @enderror" 
                               accept=".pdf,.jpg,.png">
                        @error('dokumen_transkrip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Silabus</label>
                        @if($pengajuanKonversi->dokumen_silabus)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $pengajuanKonversi->dokumen_silabus) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-text"></i> Lihat Dokumen
                                </a>
                            </div>
                        @endif
                        <input type="file" name="dokumen_silabus" class="form-control @error('dokumen_silabus') is-invalid @enderror" 
                               accept=".pdf">
                        @error('dokumen_silabus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Dokumen Pendukung</label>
                        @if($pengajuanKonversi->dokumen_pendukung)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $pengajuanKonversi->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-text"></i> Lihat Dokumen
                                </a>
                            </div>
                        @endif
                        <input type="file" name="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" 
                               accept=".pdf,.zip">
                        @error('dokumen_pendukung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                    </div>

                    <!-- Catatan -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" 
                                  rows="3">{{ old('catatan', $pengajuanKonversi->catatan) }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.konversi-nilai.show', $pengajuanKonversi->hashid) }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
