@extends('layouts.app')

@section('title', 'Edit Mata Kuliah')

@section('content')
<div class="page-title">
    <h4>Edit Mata Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil-square me-2"></i>Form Edit Mata Kuliah
    </div>
    <div class="card-body">
        <form action="{{ route('mata-kuliah.update', $mataKuliah) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control @error('kode') is-invalid @enderror" 
                            value="{{ old('kode', $mataKuliah->kode) }}" required>
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" 
                            value="{{ old('nama', $mataKuliah->nama) }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4">{{ old('deskripsi', $mataKuliah->deskripsi) }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="program_studi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="program_studi_id" id="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($programStudi as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('program_studi_id', $mataKuliah->program_studi_id) == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                        @error('program_studi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sks" class="form-label">SKS <span class="text-danger">*</span></label>
                                <input type="number" name="sks" id="sks" class="form-control @error('sks') is-invalid @enderror" 
                                    value="{{ old('sks', $mataKuliah->sks) }}" min="1" max="6" required>
                                @error('sks')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jumlah_pertemuan" class="form-label">Jml Pertemuan <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah_pertemuan" id="jumlah_pertemuan" class="form-control @error('jumlah_pertemuan') is-invalid @enderror" 
                                    value="{{ old('jumlah_pertemuan', $mataKuliah->jumlah_pertemuan ?? 16) }}" min="1" max="32" required>
                                @error('jumlah_pertemuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select name="semester" id="semester" class="form-select @error('semester') is-invalid @enderror" required>
                                    @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ old('semester', $mataKuliah->semester) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Mata Kuliah</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis" id="jenis_wajib" value="Wajib" {{ old('jenis', $mataKuliah->jenis ?? 'Wajib') == 'Wajib' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jenis_wajib">Wajib</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis" id="jenis_pilihan" value="Pilihan" {{ old('jenis', $mataKuliah->jenis) == 'Pilihan' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jenis_pilihan">Pilihan</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
