@extends('layouts.app')

@section('title', 'Ajukan Cuti Akademik')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Ajukan Cuti Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cuti.mahasiswa') }}">Cuti Akademik</a></li>
            <li class="breadcrumb-item active">Ajukan</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Form Pengajuan Cuti
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('cuti.mahasiswa.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Alasan Cuti <span class="text-danger">*</span></label>
                                <select name="alasan" class="form-select @error('alasan') is-invalid @enderror" required>
                                    <option value="">-- Pilih Alasan --</option>
                                    <option value="Keuangan" {{ old('alasan') == 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                                    <option value="Kesehatan" {{ old('alasan') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                    <option value="Keluarga" {{ old('alasan') == 'Keluarga' ? 'selected' : '' }}>Keluarga</option>
                                    <option value="Pekerjaan" {{ old('alasan') == 'Pekerjaan' ? 'selected' : '' }}>Pekerjaan</option>
                                    <option value="Lainnya" {{ old('alasan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Semester Cuti <span class="text-danger">*</span></label>
                                <select name="jumlah_semester" class="form-select @error('jumlah_semester') is-invalid @enderror" required>
                                    <option value="1" {{ old('jumlah_semester') == '1' ? 'selected' : '' }}>1 Semester</option>
                                    <option value="2" {{ old('jumlah_semester') == '2' ? 'selected' : '' }}>2 Semester</option>
                                </select>
                                @error('jumlah_semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan/Alasan Detail <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                            rows="4" placeholder="Jelaskan alasan pengajuan cuti secara detail..." required>{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dokumen Pendukung</label>
                        <input type="file" name="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maksimal 2MB.</small>
                        @error('dokumen_pendukung')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Periode Cuti:</strong> {{ $tahunAkademik->nama_lengkap ?? 'Periode aktif' }}
                        <br>Cuti akan dimulai dari awal periode akademik yang sedang berjalan.
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cuti.mahasiswa') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Ajukan Cuti
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark-text me-2"></i>Dokumen Pendukung
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">Dokumen yang diperlukan berdasarkan alasan:</p>
                <ul class="small text-muted mb-0">
                    <li><strong>Keuangan:</strong> Surat keterangan tidak mampu</li>
                    <li><strong>Kesehatan:</strong> Surat keterangan dokter</li>
                    <li><strong>Keluarga:</strong> Surat keterangan dari RT/RW atau kelurahan</li>
                    <li><strong>Pekerjaan:</strong> Surat keterangan bekerja</li>
                    <li><strong>Lainnya:</strong> Dokumen pendukung yang relevan</li>
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-diagram-3 me-2"></i>Alur Persetujuan
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-warning me-2">1</span>
                    <span class="small">Pengajuan oleh Mahasiswa</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-info me-2">2</span>
                    <span class="small">Verifikasi oleh Kaprodi</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success me-2">3</span>
                    <span class="small">Persetujuan oleh Dekan</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-secondary me-2">4</span>
                    <span class="small">Surat Cuti Terbit</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
