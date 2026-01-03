@extends('layouts.app')

@section('title', 'Edit Jadwal Pengganti')

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Edit Jadwal Pengganti</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jadwal-pengganti.index') }}">Jadwal Pengganti</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('jadwal-pengganti.update', $jadwalPengganti) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Jadwal Kuliah</label>
                            <select name="jadwal_kuliah_id" class="form-select @error('jadwal_kuliah_id') is-invalid @enderror" required>
                                <option value="">Pilih Jadwal Kuliah</option>
                                @foreach($jadwalKuliah as $jadwal)
                                    <option value="{{ $jadwal->id }}" {{ old('jadwal_kuliah_id', $jadwalPengganti->jadwal_kuliah_id) == $jadwal->id ? 'selected' : '' }}>
                                        {{ $jadwal->mataKuliah->nama }} - {{ $jadwal->dosen->nama }} ({{ $jadwal->hari }}, {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }})
                                    </option>
                                @endforeach
                            </select>
                            @error('jadwal_kuliah_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Asli</label>
                            <input type="date" name="tanggal_asli" class="form-control @error('tanggal_asli') is-invalid @enderror" 
                                   value="{{ old('tanggal_asli', $jadwalPengganti->tanggal_asli->format('Y-m-d')) }}" required>
                            @error('tanggal_asli')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pengganti</label>
                            <input type="date" name="tanggal_pengganti" class="form-control @error('tanggal_pengganti') is-invalid @enderror" 
                                   value="{{ old('tanggal_pengganti', $jadwalPengganti->tanggal_pengganti->format('Y-m-d')) }}" required>
                            @error('tanggal_pengganti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alasan</label>
                            <select name="alasan" class="form-select @error('alasan') is-invalid @enderror" required>
                                <option value="">Pilih Alasan</option>
                                @foreach(['Libur Nasional', 'Acara Kampus', 'Dosen Berhalangan', 'Force Majeure', 'Lainnya'] as $alasan)
                                    <option value="{{ $alasan }}" {{ old('alasan', $jadwalPengganti->alasan) == $alasan ? 'selected' : '' }}>
                                        {{ $alasan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" 
                                   value="{{ old('jam_mulai', $jadwalPengganti->jam_mulai) }}" required>
                            @error('jam_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" 
                                   value="{{ old('jam_selesai', $jadwalPengganti->jam_selesai) }}" required>
                            @error('jam_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ruangan</label>
                            <select name="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror">
                                <option value="">Ruangan sama dengan jadwal asli</option>
                                @foreach($ruangan as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id', $jadwalPengganti->ruangan_id) == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama }} (Kapasitas: {{ $r->kapasitas }})
                                    </option>
                                @endforeach
                            </select>
                            @error('ruangan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $jadwalPengganti->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('jadwal-pengganti.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Update Jadwal Pengganti</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
