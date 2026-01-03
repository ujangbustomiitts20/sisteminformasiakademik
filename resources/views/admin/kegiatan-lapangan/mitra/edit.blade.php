@extends('layouts.app')

@section('title', 'Edit Mitra')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Mitra Kegiatan</h1>
        <a href="{{ route('admin.kegiatan-lapangan.mitra.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.kegiatan-lapangan.mitra.update', $mitra->hashid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Mitra</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Mitra <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $mitra->nama) }}" required>
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bidang Usaha</label>
                            <input type="text" name="bidang_usaha" class="form-control @error('bidang_usaha') is-invalid @enderror" value="{{ old('bidang_usaha', $mitra->bidang_usaha) }}">
                            @error('bidang_usaha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2" required>{{ old('alamat', $mitra->alamat) }}</textarea>
                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota <span class="text-danger">*</span></label>
                                <input type="text" name="kota" class="form-control @error('kota') is-invalid @enderror" value="{{ old('kota', $mitra->kota) }}" required>
                                @error('kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror" value="{{ old('provinsi', $mitra->provinsi) }}" required>
                                @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon', $mitra->telepon) }}">
                                @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $mitra->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Kontak</h5>

                        <div class="mb-3">
                            <label class="form-label">Nama Kontak</label>
                            <input type="text" name="nama_kontak" class="form-control @error('nama_kontak') is-invalid @enderror" value="{{ old('nama_kontak', $mitra->nama_kontak) }}">
                            @error('nama_kontak')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan Kontak</label>
                            <input type="text" name="jabatan_kontak" class="form-control @error('jabatan_kontak') is-invalid @enderror" value="{{ old('jabatan_kontak', $mitra->jabatan_kontak) }}">
                            @error('jabatan_kontak')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Kuota Mahasiswa</label>
                            <input type="number" name="kuota_mahasiswa" class="form-control @error('kuota_mahasiswa') is-invalid @enderror" value="{{ old('kuota_mahasiswa', $mitra->kuota_mahasiswa) }}" min="1">
                            @error('kuota_mahasiswa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', $mitra->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">Mitra Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.kegiatan-lapangan.mitra.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
