@extends('layouts.app')

@section('title', 'Edit Kurikulum')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Edit Kurikulum</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kurikulum.index') }}">Kurikulum</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil me-2"></i>Edit Kurikulum: {{ $kurikulum->nama }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('kurikulum.update', $kurikulum) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('program_studi_id', $kurikulum->program_studi_id) == $prodi->id ? 'selected' : '' }}>
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
                            value="{{ old('kode', $kurikulum->kode) }}" required>
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Kurikulum <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                    value="{{ old('nama', $kurikulum->nama) }}" required>
                @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tahun Mulai <span class="text-danger">*</span></label>
                        <input type="number" name="tahun_mulai" class="form-control @error('tahun_mulai') is-invalid @enderror" 
                            value="{{ old('tahun_mulai', $kurikulum->tahun_mulai) }}" min="2000" max="2100" required>
                        @error('tahun_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tahun Selesai</label>
                        <input type="number" name="tahun_selesai" class="form-control @error('tahun_selesai') is-invalid @enderror" 
                            value="{{ old('tahun_selesai', $kurikulum->tahun_selesai) }}" min="2000" max="2100">
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
                            value="{{ old('total_sks_wajib', $kurikulum->total_sks_wajib) }}" min="0" required>
                        @error('total_sks_wajib')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">SKS Pilihan <span class="text-danger">*</span></label>
                        <input type="number" name="total_sks_pilihan" class="form-control @error('total_sks_pilihan') is-invalid @enderror" 
                            value="{{ old('total_sks_pilihan', $kurikulum->total_sks_pilihan) }}" min="0" required>
                        @error('total_sks_pilihan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Total SKS Lulus <span class="text-danger">*</span></label>
                        <input type="number" name="total_sks_lulus" class="form-control @error('total_sks_lulus') is-invalid @enderror" 
                            value="{{ old('total_sks_lulus', $kurikulum->total_sks_lulus) }}" min="1" required>
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
                            value="{{ old('minimal_semester', $kurikulum->minimal_semester) }}" min="1" required>
                        @error('minimal_semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Maksimal Semester <span class="text-danger">*</span></label>
                        <input type="number" name="maksimal_semester" class="form-control @error('maksimal_semester') is-invalid @enderror" 
                            value="{{ old('maksimal_semester', $kurikulum->maksimal_semester) }}" min="1" required>
                        @error('maksimal_semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi', $kurikulum->deskripsi) }}</textarea>
                @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_aktif" class="form-check-input" id="is_aktif" value="1" 
                        {{ old('is_aktif', $kurikulum->is_aktif) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_aktif">Kurikulum aktif</label>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                <a href="{{ route('kurikulum.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
