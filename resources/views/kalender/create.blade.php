@extends('layouts.app')

@section('title', 'Tambah Event Kalender')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tambah Event Kalender</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kalender.index') }}">Kalender Akademik</a></li>
                    <li class="breadcrumb-item active">Tambah Event</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-calendar-plus me-2"></i>Form Event Baru
                </div>
                <div class="card-body">
                    <form action="{{ route('kalender.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Event <span class="text-danger">*</span></label>
                            <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" 
                                value="{{ old('judul') }}" required>
                            @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                    value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                    value="{{ old('tanggal_selesai') }}">
                                @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jenis" class="form-label">Jenis Event <span class="text-danger">*</span></label>
                                <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                    <option value="akademik" {{ old('jenis') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                                    <option value="libur" {{ old('jenis') == 'libur' ? 'selected' : '' }}>Libur</option>
                                    <option value="ujian" {{ old('jenis') == 'ujian' ? 'selected' : '' }}>Ujian</option>
                                    <option value="pendaftaran" {{ old('jenis') == 'pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                                    <option value="lainnya" {{ old('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="warna" class="form-label">Warna</label>
                                <input type="color" name="warna" id="warna" class="form-control form-control-color w-100" 
                                    value="{{ old('warna', '#007bff') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tahun_akademik_id" class="form-label">Tahun Akademik</label>
                                <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach($tahunAkademiks as $ta)
                                    <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun }} - Semester {{ $ta->semester }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                            <a href="{{ route('kalender.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x me-1"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-palette me-2"></i>Panduan Warna
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Gunakan warna yang sesuai dengan jenis event:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><span class="badge bg-primary me-2">&nbsp;&nbsp;&nbsp;</span> Akademik (#007bff)</li>
                        <li class="mb-2"><span class="badge bg-danger me-2">&nbsp;&nbsp;&nbsp;</span> Libur (#dc3545)</li>
                        <li class="mb-2"><span class="badge bg-warning me-2">&nbsp;&nbsp;&nbsp;</span> Ujian (#ffc107)</li>
                        <li class="mb-2"><span class="badge bg-success me-2">&nbsp;&nbsp;&nbsp;</span> Pendaftaran (#28a745)</li>
                        <li class="mb-2"><span class="badge bg-secondary me-2">&nbsp;&nbsp;&nbsp;</span> Lainnya (#6c757d)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('jenis').addEventListener('change', function() {
    var colorInput = document.getElementById('warna');
    var colors = {
        'akademik': '#007bff',
        'libur': '#dc3545',
        'ujian': '#ffc107',
        'pendaftaran': '#28a745',
        'lainnya': '#6c757d'
    };
    colorInput.value = colors[this.value] || '#007bff';
});
</script>
@endsection
