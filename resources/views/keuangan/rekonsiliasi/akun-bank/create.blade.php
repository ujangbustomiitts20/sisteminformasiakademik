@extends('layouts.app')

@section('title', 'Tambah Akun Bank')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Tambah Akun Bank</h1>
                    <p class="text-muted mb-0">Tambah rekening bank baru</p>
                </div>
                <a href="{{ route('akun-bank.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('akun-bank.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" name="nama_bank" class="form-control @error('nama_bank') is-invalid @enderror" 
                                       value="{{ old('nama_bank') }}" placeholder="Contoh: Bank Mandiri" required>
                                @error('nama_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Bank</label>
                                <input type="text" name="kode_bank" class="form-control @error('kode_bank') is-invalid @enderror" 
                                       value="{{ old('kode_bank') }}" placeholder="Contoh: 008">
                                @error('kode_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="nomor_rekening" class="form-control @error('nomor_rekening') is-invalid @enderror" 
                                       value="{{ old('nomor_rekening') }}" placeholder="Contoh: 1234567890" required>
                                @error('nomor_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="nama_rekening" class="form-control @error('nama_rekening') is-invalid @enderror" 
                                       value="{{ old('nama_rekening') }}" placeholder="Contoh: Universitas XYZ" required>
                                @error('nama_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Cabang</label>
                                <input type="text" name="cabang" class="form-control @error('cabang') is-invalid @enderror" 
                                       value="{{ old('cabang') }}" placeholder="Contoh: KCP Kampus">
                                @error('cabang')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipe Rekening <span class="text-danger">*</span></label>
                                <select name="tipe" class="form-select @error('tipe') is-invalid @enderror" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="penampungan" {{ old('tipe') == 'penampungan' ? 'selected' : '' }}>Penampungan (VA)</option>
                                    <option value="operasional" {{ old('tipe') == 'operasional' ? 'selected' : '' }}>Operasional</option>
                                    <option value="beasiswa" {{ old('tipe') == 'beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                                </select>
                                @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Saldo Awal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="saldo_awal" class="form-control @error('saldo_awal') is-invalid @enderror" 
                                       value="{{ old('saldo_awal', 0) }}" min="0" step="0.01" required>
                            </div>
                            @error('saldo_awal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Saldo awal per tanggal mulai digunakan</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
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
