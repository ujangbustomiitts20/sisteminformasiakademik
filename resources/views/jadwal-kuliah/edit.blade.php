@extends('layouts.app')

@section('title', 'Edit Jadwal Kuliah')

@section('content')
<div class="page-title">
    <h4>Edit Jadwal Kuliah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jadwal-kuliah.index') }}">Jadwal Kuliah</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil-square me-2"></i>Form Edit Jadwal Kuliah
    </div>
    <div class="card-body">
        <form action="{{ route('jadwal-kuliah.update', $jadwalKuliah) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tahun_akademik_id" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                        <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Akademik --</option>
                            @foreach($tahunAkademik as $ta)
                            <option value="{{ $ta->id }}" {{ old('tahun_akademik_id', $jadwalKuliah->tahun_akademik_id) == $ta->id ? 'selected' : '' }}>{{ $ta->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        @error('tahun_akademik_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="mata_kuliah_id" class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
                        <select name="mata_kuliah_id" id="mata_kuliah_id" class="form-select @error('mata_kuliah_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach($mataKuliah as $mk)
                            <option value="{{ $mk->id }}" {{ old('mata_kuliah_id', $jadwalKuliah->mata_kuliah_id) == $mk->id ? 'selected' : '' }}>{{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->sks }} SKS)</option>
                            @endforeach
                        </select>
                        @error('mata_kuliah_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="dosen_id" class="form-label">Dosen Pengampu <span class="text-danger">*</span></label>
                        <select name="dosen_id" id="dosen_id" class="form-select @error('dosen_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosen as $d)
                            <option value="{{ $d->id }}" {{ old('dosen_id', $jadwalKuliah->dosen_id) == $d->id ? 'selected' : '' }}>{{ $d->nidn }} - {{ $d->nama }}</option>
                            @endforeach
                        </select>
                        @error('dosen_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                                <input type="text" name="kelas" id="kelas" class="form-control @error('kelas') is-invalid @enderror" 
                                    value="{{ old('kelas', $jadwalKuliah->kelas) }}" required>
                                @error('kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kuota" class="form-label">Kuota</label>
                                <input type="number" name="kuota" id="kuota" class="form-control @error('kuota') is-invalid @enderror" 
                                    value="{{ old('kuota', $jadwalKuliah->kuota) }}" min="1">
                                @error('kuota')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="hari" class="form-label">Hari <span class="text-danger">*</span></label>
                        <select name="hari" id="hari" class="form-select @error('hari') is-invalid @enderror" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                            <option value="{{ $h }}" {{ old('hari', $jadwalKuliah->hari) == $h ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </select>
                        @error('hari')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" 
                                    value="{{ old('jam_mulai', \Carbon\Carbon::parse($jadwalKuliah->jam_mulai)->format('H:i')) }}" required>
                                @error('jam_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" 
                                    value="{{ old('jam_selesai', \Carbon\Carbon::parse($jadwalKuliah->jam_selesai)->format('H:i')) }}" required>
                                @error('jam_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ruangan_id" class="form-label">Ruangan <span class="text-danger">*</span></label>
                        <select name="ruangan_id" id="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($ruangan as $r)
                            <option value="{{ $r->id }}" {{ old('ruangan_id', $jadwalKuliah->ruangan_id) == $r->id ? 'selected' : '' }}>{{ $r->kode }} - {{ $r->nama }} (Kap: {{ $r->kapasitas }})</option>
                            @endforeach
                        </select>
                        @error('ruangan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('jadwal-kuliah.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
