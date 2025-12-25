@extends('layouts.app')

@section('title', 'Buat Jadwal Bimbingan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Buat Jadwal Bimbingan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bimbingan.dosen') }}">Bimbingan Akademik</a></li>
                <li class="breadcrumb-item active">Buat Jadwal</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('bimbingan.dosen') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Data Mahasiswa</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="100">NIM</td>
                        <td>: <strong>{{ $mahasiswa->nim }}</strong></td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td>: {{ $mahasiswa->nama }}</td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>: {{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Semester</td>
                        <td>: {{ $mahasiswa->semester }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>: <span class="badge bg-{{ $mahasiswa->status == 'Aktif' ? 'success' : 'warning' }}">{{ $mahasiswa->status }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Jadwal Bimbingan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bimbingan.buat-jadwal', $mahasiswa) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_bimbingan" class="form-label">Tanggal Bimbingan <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('tanggal_bimbingan') is-invalid @enderror" 
                                   id="tanggal_bimbingan" name="tanggal_bimbingan" 
                                   value="{{ old('tanggal_bimbingan') }}" required>
                            @error('tanggal_bimbingan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jenis" class="form-label">Jenis Bimbingan <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis') is-invalid @enderror" id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="KRS" {{ old('jenis') == 'KRS' ? 'selected' : '' }}>KRS</option>
                                <option value="Akademik" {{ old('jenis') == 'Akademik' ? 'selected' : '' }}>Akademik</option>
                                <option value="Karir" {{ old('jenis') == 'Karir' ? 'selected' : '' }}>Karir</option>
                                <option value="Pribadi" {{ old('jenis') == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                                <option value="Lainnya" {{ old('jenis') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="topik" class="form-label">Topik Bimbingan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('topik') is-invalid @enderror" 
                               id="topik" name="topik" value="{{ old('topik') }}" 
                               placeholder="Masukkan topik bimbingan..." required maxlength="500">
                        @error('topik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="catatan_dosen" class="form-label">Catatan</label>
                        <textarea class="form-control @error('catatan_dosen') is-invalid @enderror" 
                                  id="catatan_dosen" name="catatan_dosen" rows="3" 
                                  placeholder="Tambahkan catatan jika diperlukan...">{{ old('catatan_dosen') }}</textarea>
                        @error('catatan_dosen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-calendar-plus me-1"></i>Buat Jadwal
                        </button>
                        <a href="{{ route('bimbingan.dosen') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
