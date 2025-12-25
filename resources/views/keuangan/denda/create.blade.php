@extends('layouts.app')

@section('title', 'Tambah Pengaturan Denda')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Tambah Pengaturan Denda</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pengaturan-denda.index') }}">Pengaturan Denda</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Form Pengaturan Denda</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('pengaturan-denda.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama Denda <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Contoh: Denda Keterlambatan SPP" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipe Denda <span class="text-danger">*</span></label>
                                <select name="tipe" class="form-select @error('tipe') is-invalid @enderror" required>
                                    @foreach(\App\Models\PengaturanDenda::TIPE as $key => $label)
                                        <option value="{{ $key }}" {{ old('tipe') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('tipe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nilai Denda <span class="text-danger">*</span></label>
                                <input type="number" name="nilai" class="form-control @error('nilai') is-invalid @enderror" value="{{ old('nilai') }}" min="0" step="0.01" required>
                                @error('nilai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Jika persen, masukkan angka 0-100. Jika nominal, masukkan angka dalam Rupiah.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Periode Denda <span class="text-danger">*</span></label>
                                <select name="periode" class="form-select @error('periode') is-invalid @enderror" required>
                                    @foreach(\App\Models\PengaturanDenda::PERIODE as $key => $label)
                                        <option value="{{ $key }}" {{ old('periode') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('periode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Grace Period (hari)</label>
                                <input type="number" name="grace_period" class="form-control @error('grace_period') is-invalid @enderror" value="{{ old('grace_period', 0) }}" min="0">
                                @error('grace_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Toleransi hari sebelum denda berlaku</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Maksimal Denda</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="maksimal_denda" class="form-control @error('maksimal_denda') is-invalid @enderror" value="{{ old('maksimal_denda') }}" min="0">
                                </div>
                                @error('maksimal_denda')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan jika tidak ada batas</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                            <a href="{{ route('pengaturan-denda.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
