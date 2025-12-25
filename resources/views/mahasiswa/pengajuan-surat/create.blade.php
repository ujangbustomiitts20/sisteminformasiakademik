@extends('layouts.app')

@section('title', 'Ajukan Surat Baru')

@section('content')
<div class="page-title">
    <h4>Ajukan Surat Baru</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pengajuan-surat.index') }}">Pengajuan Surat</a></li>
            <li class="breadcrumb-item active">Ajukan Baru</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Form Pengajuan Surat</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pengajuan-surat.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Surat <span class="text-danger">*</span></label>
                        <select name="jenis_surat" class="form-select @error('jenis_surat') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Surat --</option>
                            @foreach($jenisSurat as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_surat') == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                            @endforeach
                        </select>
                        @error('jenis_surat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pilih jenis surat yang akan Anda ajukan</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                        <textarea name="keperluan" class="form-control @error('keperluan') is-invalid @enderror" rows="3" required placeholder="Jelaskan keperluan surat ini">{{ old('keperluan') }}</textarea>
                        @error('keperluan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Contoh: Untuk persyaratan magang di PT. ABC</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ditujukan Kepada</label>
                        <input type="text" name="ditujukan_kepada" class="form-control @error('ditujukan_kepada') is-invalid @enderror" value="{{ old('ditujukan_kepada') }}" placeholder="Nama instansi/perusahaan (opsional)">
                        @error('ditujukan_kepada')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Contoh: HRD PT. Maju Jaya, Kepala Dinas Pendidikan</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Keterangan Tambahan</label>
                        <textarea name="keterangan_tambahan" class="form-control @error('keterangan_tambahan') is-invalid @enderror" rows="3" placeholder="Keterangan tambahan jika ada (opsional)">{{ old('keterangan_tambahan') }}</textarea>
                        @error('keterangan_tambahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Perhatian</h6>
                        <ul class="mb-0 small">
                            <li>Pastikan data yang Anda masukkan sudah benar</li>
                            <li>Pengajuan akan diproses dalam 1-3 hari kerja</li>
                            <li>Anda akan mendapat notifikasi jika pengajuan disetujui atau ditolak</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pengajuan-surat.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
