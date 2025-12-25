@extends('layouts.app')

@section('title', 'Buat Jadwal Bimbingan')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Buat Jadwal Bimbingan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bimbingan.dosen') }}">Bimbingan</a></li>
            <li class="breadcrumb-item active">Buat Jadwal</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Form Bimbingan Baru
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('bimbingan.dosen.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                        <select name="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Mahasiswa --</option>
                            @foreach($mahasiswaPerwalian as $mhs)
                            <option value="{{ $mhs->id }}" {{ old('mahasiswa_id') == $mhs->id ? 'selected' : '' }}>
                                {{ $mhs->nim }} - {{ $mhs->nama }} (Semester {{ $mhs->semester_aktif }})
                            </option>
                            @endforeach
                        </select>
                        @error('mahasiswa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Bimbingan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_bimbingan" class="form-control @error('tanggal_bimbingan') is-invalid @enderror" 
                                    value="{{ old('tanggal_bimbingan', date('Y-m-d')) }}" required>
                                @error('tanggal_bimbingan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Bimbingan <span class="text-danger">*</span></label>
                                <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="KRS" {{ old('jenis') == 'KRS' ? 'selected' : '' }}>KRS</option>
                                    <option value="Akademik" {{ old('jenis') == 'Akademik' ? 'selected' : '' }}>Akademik</option>
                                    <option value="Pribadi" {{ old('jenis') == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                                    <option value="Karir" {{ old('jenis') == 'Karir' ? 'selected' : '' }}>Karir</option>
                                    <option value="Lainnya" {{ old('jenis') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Topik <span class="text-danger">*</span></label>
                        <input type="text" name="topik" class="form-control @error('topik') is-invalid @enderror" 
                            value="{{ old('topik') }}" placeholder="Topik bimbingan..." required>
                        @error('topik')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Dosen</label>
                        <textarea name="catatan_dosen" class="form-control @error('catatan_dosen') is-invalid @enderror" 
                            rows="3" placeholder="Catatan dari dosen...">{{ old('catatan_dosen') }}</textarea>
                        @error('catatan_dosen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rekomendasi</label>
                        <textarea name="rekomendasi" class="form-control @error('rekomendasi') is-invalid @enderror" 
                            rows="2" placeholder="Rekomendasi untuk mahasiswa...">{{ old('rekomendasi') }}</textarea>
                        @error('rekomendasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Dijadwalkan" {{ old('status') == 'Dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                            <option value="Selesai" {{ old('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('bimbingan.dosen') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Pilih mahasiswa dari daftar perwalian Anda.
                </p>
                <p class="small text-muted mb-2">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Tentukan tanggal dan jenis bimbingan.
                </p>
                <p class="small text-muted mb-0">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    Isi catatan dan rekomendasi setelah bimbingan selesai.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
