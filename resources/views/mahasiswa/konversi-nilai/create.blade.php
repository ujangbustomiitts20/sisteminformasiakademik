@extends('layouts.app')

@section('title', 'Ajukan Konversi Nilai')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Ajukan Konversi Nilai</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mahasiswa.konversi-nilai.index') }}">Konversi Nilai</a></li>
                    <li class="breadcrumb-item active">Ajukan</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('mahasiswa.konversi-nilai.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('mahasiswa.konversi-nilai.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Perguruan Tinggi Asal</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nama Universitas/Perguruan Tinggi Asal <span class="text-danger">*</span></label>
                                <input type="text" name="universitas_asal" class="form-control" 
                                    placeholder="Contoh: Universitas Indonesia" value="{{ old('universitas_asal') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Program Studi Asal <span class="text-danger">*</span></label>
                                <input type="text" name="program_studi_asal" class="form-control" 
                                    placeholder="Contoh: Teknik Informatika" value="{{ old('program_studi_asal') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIM Asal <span class="text-danger">*</span></label>
                                <input type="text" name="nim_asal" class="form-control" 
                                    placeholder="NIM di PT asal" value="{{ old('nim_asal') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tahun Masuk di PT Asal <span class="text-danger">*</span></label>
                                <input type="number" name="tahun_masuk_asal" class="form-control" 
                                    placeholder="2020" value="{{ old('tahun_masuk_asal') }}" min="2000" max="{{ date('Y') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Alasan Pindah/Konversi</label>
                                <textarea name="alasan" class="form-control" rows="3" placeholder="Jelaskan alasan pindah atau melakukan konversi nilai...">{{ old('alasan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Dokumen Pendukung</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Transkrip Nilai Asal <span class="text-danger">*</span></label>
                                <input type="file" name="transkrip_asal" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="text-muted">Format: PDF, JPG, PNG. Maks 2MB</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Silabus/Deskripsi Mata Kuliah</label>
                                <input type="file" name="silabus" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG. Maks 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <h6><i class="bi bi-info-circle me-1"></i>Ketentuan</h6>
                            <ul class="small mb-0">
                                <li>Konversi nilai hanya dapat dilakukan untuk mata kuliah dengan kesetaraan minimal 80%</li>
                                <li>Nilai minimal yang dapat dikonversi adalah C atau setara</li>
                                <li>Sertakan dokumen pendukung yang lengkap</li>
                                <li>Setelah pengajuan dibuat, Anda dapat menambahkan detail mata kuliah yang akan dikonversi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-1"></i>Buat Pengajuan
                            </button>
                        </div>
                        <p class="text-muted small text-center mt-2 mb-0">
                            Setelah membuat pengajuan, Anda dapat menambahkan detail mata kuliah yang akan dikonversi
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
