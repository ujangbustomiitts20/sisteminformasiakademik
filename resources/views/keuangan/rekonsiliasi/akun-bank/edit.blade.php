@extends('layouts.app')

@section('title', 'Edit Akun Bank')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Edit Akun Bank</h1>
                    <p class="text-muted mb-0">{{ $akunBank->nama_bank }} - {{ $akunBank->nomor_rekening }}</p>
                </div>
                <a href="{{ route('akun-bank.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('akun-bank.update', $akunBank) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" name="nama_bank" class="form-control @error('nama_bank') is-invalid @enderror" 
                                       value="{{ old('nama_bank', $akunBank->nama_bank) }}" required>
                                @error('nama_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Bank</label>
                                <input type="text" name="kode_bank" class="form-control @error('kode_bank') is-invalid @enderror" 
                                       value="{{ old('kode_bank', $akunBank->kode_bank) }}">
                                @error('kode_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="nomor_rekening" class="form-control @error('nomor_rekening') is-invalid @enderror" 
                                       value="{{ old('nomor_rekening', $akunBank->nomor_rekening) }}" required>
                                @error('nomor_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="nama_rekening" class="form-control @error('nama_rekening') is-invalid @enderror" 
                                       value="{{ old('nama_rekening', $akunBank->nama_rekening) }}" required>
                                @error('nama_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Cabang</label>
                                <input type="text" name="cabang" class="form-control @error('cabang') is-invalid @enderror" 
                                       value="{{ old('cabang', $akunBank->cabang) }}">
                                @error('cabang')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipe Rekening <span class="text-danger">*</span></label>
                                <select name="tipe" class="form-select @error('tipe') is-invalid @enderror" required>
                                    <option value="penampungan" {{ old('tipe', $akunBank->tipe) == 'penampungan' ? 'selected' : '' }}>Penampungan (VA)</option>
                                    <option value="operasional" {{ old('tipe', $akunBank->tipe) == 'operasional' ? 'selected' : '' }}>Operasional</option>
                                    <option value="beasiswa" {{ old('tipe', $akunBank->tipe) == 'beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                                </select>
                                @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Saldo Awal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="saldo_awal" class="form-control @error('saldo_awal') is-invalid @enderror" 
                                           value="{{ old('saldo_awal', $akunBank->saldo_awal) }}" min="0" step="0.01">
                                </div>
                                @error('saldo_awal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', $akunBank->is_active) ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !old('is_active', $akunBank->is_active) ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Saldo Sistem:</strong> Rp {{ number_format($akunBank->saldo_sistem, 0, ',', '.') }}
                            <br>
                            <small>Saldo sistem dihitung otomatis berdasarkan mutasi yang tercatat.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update
                            </button>
                            <a href="{{ route('akun-bank.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
