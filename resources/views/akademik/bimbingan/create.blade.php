@extends('layouts.app')

@section('title', 'Ajukan Bimbingan')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Ajukan Bimbingan Akademik</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bimbingan.mahasiswa') }}">Bimbingan</a></li>
            <li class="breadcrumb-item active">Ajukan</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Form Pengajuan Bimbingan
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('bimbingan.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Bimbingan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_bimbingan" class="form-control @error('tanggal_bimbingan') is-invalid @enderror" 
                                    value="{{ old('tanggal_bimbingan') }}" min="{{ date('Y-m-d') }}" required>
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
                                    <option value="KRS" {{ old('jenis') == 'KRS' ? 'selected' : '' }}>KRS (Kartu Rencana Studi)</option>
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
                        <label class="form-label">Topik/Hal yang Ingin Dibahas <span class="text-danger">*</span></label>
                        <input type="text" name="topik" class="form-control @error('topik') is-invalid @enderror" 
                            value="{{ old('topik') }}" placeholder="e.g., Konsultasi pengambilan mata kuliah semester ini" required>
                        @error('topik')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan/Keterangan Tambahan</label>
                        <textarea name="catatan_mahasiswa" class="form-control @error('catatan_mahasiswa') is-invalid @enderror" 
                            rows="4" placeholder="Jelaskan lebih detail mengenai hal yang ingin dikonsultasikan...">{{ old('catatan_mahasiswa') }}</textarea>
                        @error('catatan_mahasiswa')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('bimbingan.mahasiswa') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Ajukan Bimbingan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Dosen Wali Anda
            </div>
            <div class="card-body text-center">
                <img src="{{ $mahasiswa->dosenWali->foto ? asset('storage/' . $mahasiswa->dosenWali->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($mahasiswa->dosenWali->nama) }}" 
                    class="rounded-circle mb-2" width="80" height="80" style="object-fit: cover;">
                <h6 class="mb-0">{{ $mahasiswa->dosenWali->nama }}</h6>
                <small class="text-muted">{{ $mahasiswa->dosenWali->nidn }}</small>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-lightbulb me-2"></i>Tips
            </div>
            <div class="card-body">
                <ul class="small text-muted mb-0 ps-3">
                    <li class="mb-2">Pilih tanggal yang masih tersedia dan sesuai dengan jadwal dosen.</li>
                    <li class="mb-2">Jelaskan topik dengan singkat dan jelas.</li>
                    <li class="mb-2">Siapkan dokumen atau pertanyaan sebelum bimbingan.</li>
                    <li>Datang tepat waktu pada jadwal yang telah ditentukan.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
