@extends('layouts.app')

@section('title', 'Detail Pengajuan Surat')

@section('content')
<div class="page-title">
    <h4>Detail Pengajuan Surat</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.pengajuan-surat.index') }}">Pengajuan Surat</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Informasi Pengajuan</h5>
                <span class="badge bg-{{ $pengajuanSurat->status_badge }} fs-6">
                    {{ $pengajuanSurat->status_text }}
                </span>
            </div>
            <div class="card-body">
                <h6 class="text-primary mb-3"><i class="bi bi-person me-2"></i>Data Mahasiswa</h6>
                
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">NIM:</div>
                    <div class="col-md-8"><code>{{ $pengajuanSurat->mahasiswa->nim }}</code></div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">Nama:</div>
                    <div class="col-md-8"><strong>{{ $pengajuanSurat->mahasiswa->nama }}</strong></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Program Studi:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->mahasiswa->programStudi->nama ?? '-' }}</div>
                </div>
                
                <hr class="my-4">
                
                <h6 class="text-primary mb-3"><i class="bi bi-file-text me-2"></i>Detail Surat</h6>
                
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">Jenis Surat:</div>
                    <div class="col-md-8"><strong>{{ $pengajuanSurat->jenis_surat }}</strong></div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">Tanggal Pengajuan:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->created_at->format('d F Y, H:i') }} WIB</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">Keperluan:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->keperluan }}</div>
                </div>
                
                @if($pengajuanSurat->ditujukan_kepada)
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">Ditujukan Kepada:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->ditujukan_kepada }}</div>
                </div>
                @endif
                
                @if($pengajuanSurat->keterangan_tambahan)
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Keterangan Tambahan:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->keterangan_tambahan }}</div>
                </div>
                @endif
                
                @if($pengajuanSurat->nomor_surat)
                <hr class="my-4">
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">Nomor Surat:</div>
                    <div class="col-md-8"><code>{{ $pengajuanSurat->nomor_surat }}</code></div>
                </div>
                @endif
                
                @if($pengajuanSurat->tanggal_diproses)
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">Tanggal Diproses:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->tanggal_diproses->format('d F Y, H:i') }} WIB</div>
                </div>
                @endif
                
                @if($pengajuanSurat->diproses_oleh_user)
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">Diproses Oleh:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->diproses_oleh_user->name }}</div>
                </div>
                @endif
                
                @if($pengajuanSurat->catatan_admin)
                <div class="row mb-2">
                    <div class="col-md-4 text-muted">Catatan:</div>
                    <div class="col-md-8">{{ $pengajuanSurat->catatan_admin }}</div>
                </div>
                @endif
                
                <div class="mt-4">
                    <a href="{{ route('admin.pengajuan-surat.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Proses Pengajuan</h6>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                <form action="{{ route('admin.pengajuan-surat.update-status', $pengajuanSurat) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="diproses" {{ $pengajuanSurat->status == 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                            <option value="disetujui" {{ $pengajuanSurat->status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ $pengajuanSurat->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" class="form-control @error('nomor_surat') is-invalid @enderror" value="{{ old('nomor_surat', $pengajuanSurat->nomor_surat) }}" placeholder="Contoh: 123/SK/AKD/2025">
                        @error('nomor_surat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Isi nomor surat jika disetujui</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan_admin" class="form-control @error('catatan_admin') is-invalid @enderror" rows="3" placeholder="Catatan untuk mahasiswa (opsional)">{{ old('catatan_admin', $pengajuanSurat->catatan_admin) }}</textarea>
                        @error('catatan_admin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-2"></i>Update Status
                    </button>
                </form>
                
                @if($pengajuanSurat->status == 'disetujui' && $pengajuanSurat->file_surat)
                <hr>
                <a href="{{ route('pengajuan-surat.download', $pengajuanSurat) }}" class="btn btn-success w-100" target="_blank">
                    <i class="bi bi-file-pdf me-2"></i>Lihat Surat (PDF)
                </a>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Panduan</h6>
            </div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li class="mb-2">Ubah status menjadi <strong>Diproses</strong> jika sedang mengurus surat</li>
                    <li class="mb-2">Ubah status menjadi <strong>Disetujui</strong> dan isi nomor surat untuk generate PDF</li>
                    <li class="mb-2">Ubah status menjadi <strong>Ditolak</strong> jika tidak memenuhi syarat</li>
                    <li>Catatan akan dikirim ke mahasiswa sebagai notifikasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
