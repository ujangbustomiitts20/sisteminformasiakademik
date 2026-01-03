@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="page-title">
    <h4>Edit Profil</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.profil') }}">Profil</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<form action="{{ route('dosen.profil.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-4">
            <!-- Card Foto -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-image me-2"></i>Foto Profil
                </div>
                <div class="card-body text-center">
                    @if($dosen->foto)
                    <img src="{{ Storage::url($dosen->foto) }}" alt="{{ $dosen->nama }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;" id="preview-foto">
                    @else
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px; font-size: 4rem;" id="preview-placeholder">
                        {{ strtoupper(substr($dosen->nama, 0, 1)) }}
                    </div>
                    <img src="" alt="" class="rounded-circle mb-3 d-none" style="width: 150px; height: 150px; object-fit: cover;" id="preview-foto">
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">Ganti Foto</label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg" onchange="previewImage(this)">
                        @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                    </div>
                </div>
            </div>

            <!-- Info Tidak Bisa Diedit -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Informasi
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Data berikut tidak dapat diubah sendiri. Hubungi admin jika ada perubahan:</p>
                    <ul class="small text-muted mb-0">
                        <li>NIDN</li>
                        <li>Nama Lengkap</li>
                        <li>Gelar</li>
                        <li>Tempat/Tanggal Lahir</li>
                        <li>Program Studi</li>
                        <li>Status Kepegawaian</li>
                        <li>Jabatan Fungsional</li>
                        <li>Golongan</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Card Kontak -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-telephone me-2"></i>Informasi Kontak
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $dosen->email) }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $dosen->no_hp) }}">
                            @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon', $dosen->telepon) }}">
                            @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $dosen->alamat) }}</textarea>
                            @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Akademik -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-book me-2"></i>Informasi Akademik & Peneliti
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Bidang Keahlian</label>
                            <input type="text" name="bidang_keahlian" class="form-control @error('bidang_keahlian') is-invalid @enderror" value="{{ old('bidang_keahlian', $dosen->bidang_keahlian) }}" placeholder="Contoh: Machine Learning, Data Science">
                            @error('bidang_keahlian')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SINTA ID</label>
                            <input type="text" name="sinta_id" class="form-control @error('sinta_id') is-invalid @enderror" value="{{ old('sinta_id', $dosen->sinta_id) }}">
                            @error('sinta_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Scopus ID</label>
                            <input type="text" name="scopus_id" class="form-control @error('scopus_id') is-invalid @enderror" value="{{ old('scopus_id', $dosen->scopus_id) }}">
                            @error('scopus_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Google Scholar ID</label>
                            <input type="text" name="google_scholar_id" class="form-control @error('google_scholar_id') is-invalid @enderror" value="{{ old('google_scholar_id', $dosen->google_scholar_id) }}">
                            @error('google_scholar_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ORCID</label>
                            <input type="text" name="orcid" class="form-control @error('orcid') is-invalid @enderror" value="{{ old('orcid', $dosen->orcid) }}" placeholder="0000-0000-0000-0000">
                            @error('orcid')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Bank -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bank me-2"></i>Informasi Rekening
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nama Bank</label>
                            <input type="text" name="nama_bank" class="form-control @error('nama_bank') is-invalid @enderror" value="{{ old('nama_bank', $dosen->nama_bank) }}">
                            @error('nama_bank')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. Rekening</label>
                            <input type="text" name="no_rekening" class="form-control @error('no_rekening') is-invalid @enderror" value="{{ old('no_rekening', $dosen->no_rekening) }}">
                            @error('no_rekening')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Atas Nama</label>
                            <input type="text" name="atas_nama_rekening" class="form-control @error('atas_nama_rekening') is-invalid @enderror" value="{{ old('atas_nama_rekening', $dosen->atas_nama_rekening) }}">
                            @error('atas_nama_rekening')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('dosen.profil') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-foto').src = e.target.result;
            document.getElementById('preview-foto').classList.remove('d-none');
            var placeholder = document.getElementById('preview-placeholder');
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
