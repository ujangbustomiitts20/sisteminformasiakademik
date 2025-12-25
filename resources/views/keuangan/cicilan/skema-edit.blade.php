@extends('layouts.app')

@section('title', 'Edit Skema Cicilan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Skema Cicilan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('skema-cicilan.index') }}">Skema Cicilan</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit: {{ $skemaCicilan->nama }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('skema-cicilan.update', $skemaCicilan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Skema <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                    value="{{ old('nama', $skemaCicilan->nama) }}" required>
                                @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Cicilan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="jumlah_cicilan" class="form-control @error('jumlah_cicilan') is-invalid @enderror" 
                                        value="{{ old('jumlah_cicilan', $skemaCicilan->jumlah_cicilan) }}" min="2" max="24" required>
                                    <span class="input-group-text">kali</span>
                                </div>
                                @error('jumlah_cicilan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Biaya Admin</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="biaya_admin" class="form-control @error('biaya_admin') is-invalid @enderror" 
                                        value="{{ old('biaya_admin', $skemaCicilan->biaya_admin) }}" min="0">
                                </div>
                                @error('biaya_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Persentase Bunga</label>
                                <div class="input-group">
                                    <input type="number" name="persentase_bunga" class="form-control @error('persentase_bunga') is-invalid @enderror" 
                                        value="{{ old('persentase_bunga', $skemaCicilan->persentase_bunga) }}" min="0" max="100" step="0.1">
                                    <span class="input-group-text">% / cicilan</span>
                                </div>
                                @error('persentase_bunga')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimal Tagihan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="minimal_tagihan" class="form-control @error('minimal_tagihan') is-invalid @enderror" 
                                        value="{{ old('minimal_tagihan', $skemaCicilan->minimal_tagihan) }}" min="0">
                                </div>
                                @error('minimal_tagihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Interval Pembayaran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="interval_hari" class="form-control @error('interval_hari') is-invalid @enderror" 
                                        value="{{ old('interval_hari', $skemaCicilan->interval_hari) }}" min="7" max="90" required>
                                    <span class="input-group-text">hari</span>
                                </div>
                                @error('interval_hari')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                rows="3">{{ old('keterangan', $skemaCicilan->keterangan) }}</textarea>
                            @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" 
                                    {{ old('is_active', $skemaCicilan->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktifkan skema ini</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update
                            </button>
                            <a href="{{ route('skema-cicilan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Info Penggunaan</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Cicilan Aktif:</strong></p>
                    <h4>{{ $skemaCicilan->cicilan()->where('status', 'Aktif')->count() }}</h4>
                    <hr>
                    <p class="mb-2"><strong>Total Digunakan:</strong></p>
                    <h4>{{ $skemaCicilan->cicilan()->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
