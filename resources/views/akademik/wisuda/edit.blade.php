@extends('layouts.app')

@section('title', 'Edit Periode Wisuda')

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Edit Periode Wisuda</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('wisuda.index') }}">Wisuda</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('wisuda.update', $wisuda) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                            <select name="tahun_akademik_id" class="form-select @error('tahun_akademik_id') is-invalid @enderror" required>
                                <option value="">Pilih Tahun Akademik</option>
                                @foreach($tahunAkademik as $ta)
                                    <option value="{{ $ta->id }}" {{ old('tahun_akademik_id', $wisuda->tahun_akademik_id) == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun }}/{{ $ta->tahun + 1 }} {{ $ta->semester }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun_akademik_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Periode <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                   value="{{ old('nama', $wisuda->nama) }}" placeholder="Contoh: Wisuda Periode I 2024" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Wisuda <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_wisuda" class="form-control @error('tanggal_wisuda') is-invalid @enderror" 
                                   value="{{ old('tanggal_wisuda', $wisuda->tanggal_wisuda->format('Y-m-d')) }}" required>
                            @error('tanggal_wisuda')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Lokasi Wisuda</label>
                            <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" 
                                   value="{{ old('lokasi', $wisuda->lokasi) }}" placeholder="Lokasi pelaksanaan wisuda">
                            @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Buka Pendaftaran <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_buka_pendaftaran" class="form-control @error('tanggal_buka_pendaftaran') is-invalid @enderror" 
                                   value="{{ old('tanggal_buka_pendaftaran', $wisuda->tanggal_buka_pendaftaran->format('Y-m-d')) }}" required>
                            @error('tanggal_buka_pendaftaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Tutup Pendaftaran <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_tutup_pendaftaran" class="form-control @error('tanggal_tutup_pendaftaran') is-invalid @enderror" 
                                   value="{{ old('tanggal_tutup_pendaftaran', $wisuda->tanggal_tutup_pendaftaran->format('Y-m-d')) }}" required>
                            @error('tanggal_tutup_pendaftaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kuota Peserta</label>
                            <input type="number" name="kuota" class="form-control @error('kuota') is-invalid @enderror" 
                                   value="{{ old('kuota', $wisuda->kuota) }}" placeholder="Kosongkan jika tidak ada batasan">
                            @error('kuota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Biaya Wisuda</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="biaya_wisuda" class="form-control @error('biaya_wisuda') is-invalid @enderror" 
                                       value="{{ old('biaya_wisuda', $wisuda->biaya_wisuda) }}" placeholder="0">
                            </div>
                            @error('biaya_wisuda')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Keterangan tambahan">{{ old('keterangan', $wisuda->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('wisuda.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Update Periode Wisuda</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
