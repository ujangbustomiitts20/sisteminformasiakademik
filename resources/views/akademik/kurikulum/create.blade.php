@extends('layouts.app')

@section('title', 'Tambah Kurikulum')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Tambah Kurikulum Baru</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kurikulum.index') }}">Kurikulum</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-plus-circle me-2"></i>Form Tambah Kurikulum
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('kurikulum.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama }} ({{ $prodi->jenjang }})
                            </option>
                            @endforeach
                        </select>
                        @error('program_studi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kode Kurikulum <span class="text-danger">*</span></label>
                        <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror" 
                            value="{{ old('kode') }}" placeholder="e.g., KUR-TI-2024" required>
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Kurikulum <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                    value="{{ old('nama') }}" placeholder="e.g., Kurikulum 2024 Berbasis KKNI" required>
                @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tahun Mulai <span class="text-danger">*</span></label>
                        <input type="number" name="tahun_mulai" class="form-control @error('tahun_mulai') is-invalid @enderror" 
                            value="{{ old('tahun_mulai', date('Y')) }}" min="2000" max="2100" required>
                        @error('tahun_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tahun Selesai</label>
                        <input type="number" name="tahun_selesai" class="form-control @error('tahun_selesai') is-invalid @enderror" 
                            value="{{ old('tahun_selesai') }}" min="2000" max="2100" placeholder="Kosongkan jika masih berlaku">
                        @error('tahun_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">SKS Wajib <span class="text-danger">*</span></label>
                        <input type="number" name="total_sks_wajib" class="form-control @error('total_sks_wajib') is-invalid @enderror" 
                            value="{{ old('total_sks_wajib', 120) }}" min="0" required>
                        @error('total_sks_wajib')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">SKS Pilihan <span class="text-danger">*</span></label>
                        <input type="number" name="total_sks_pilihan" class="form-control @error('total_sks_pilihan') is-invalid @enderror" 
                            value="{{ old('total_sks_pilihan', 24) }}" min="0" required>
                        @error('total_sks_pilihan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Total SKS Lulus <span class="text-danger">*</span></label>
                        <input type="number" name="total_sks_lulus" class="form-control @error('total_sks_lulus') is-invalid @enderror" 
                            value="{{ old('total_sks_lulus', 144) }}" min="1" required>
                        @error('total_sks_lulus')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Minimal Semester <span class="text-danger">*</span></label>
                        <input type="number" name="minimal_semester" class="form-control @error('minimal_semester') is-invalid @enderror" 
                            value="{{ old('minimal_semester', 8) }}" min="1" required>
                        @error('minimal_semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Maksimal Semester <span class="text-danger">*</span></label>
                        <input type="number" name="maksimal_semester" class="form-control @error('maksimal_semester') is-invalid @enderror" 
                            value="{{ old('maksimal_semester', 14) }}" min="1" required>
                        @error('maksimal_semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3" 
                    placeholder="Deskripsi singkat tentang kurikulum...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_aktif" class="form-check-input" id="is_aktif" value="1" {{ old('is_aktif') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_aktif">Set sebagai kurikulum aktif</label>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                <a href="{{ route('kurikulum.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Kurikulum
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
