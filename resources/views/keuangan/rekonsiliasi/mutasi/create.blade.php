@extends('layouts.app')

@section('title', 'Tambah Mutasi Bank')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Tambah Mutasi Bank</h1>
                    <p class="text-muted mb-0">Input mutasi bank secara manual</p>
                </div>
                <a href="{{ route('mutasi-bank.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('mutasi-bank.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Akun Bank <span class="text-danger">*</span></label>
                                <select name="akun_bank_id" class="form-select @error('akun_bank_id') is-invalid @enderror" required>
                                    <option value="">Pilih Akun Bank</option>
                                    @foreach($akunBank as $akun)
                                    <option value="{{ $akun->id }}" {{ old('akun_bank_id') == $akun->id ? 'selected' : '' }}>
                                        {{ $akun->nama_bank }} - {{ $akun->nomor_rekening }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('akun_bank_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                                       value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipe Transaksi <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="tipe" value="kredit" id="tipeKredit" 
                                           {{ old('tipe', 'kredit') == 'kredit' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="tipeKredit">
                                        <i class="bi bi-arrow-down-circle"></i> Kredit (Masuk)
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="tipe" value="debit" id="tipeDebit"
                                           {{ old('tipe') == 'debit' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="tipeDebit">
                                        <i class="bi bi-arrow-up-circle"></i> Debit (Keluar)
                                    </label>
                                </div>
                                @error('tipe')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nominal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" 
                                           value="{{ old('nominal') }}" min="1" step="0.01" required>
                                </div>
                                @error('nominal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                      rows="2" placeholder="Keterangan dari statement bank">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Referensi</label>
                                <input type="text" name="referensi" class="form-control @error('referensi') is-invalid @enderror" 
                                       value="{{ old('referensi') }}" placeholder="Nomor referensi bank">
                                @error('referensi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Saldo Akhir</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="saldo" class="form-control @error('saldo') is-invalid @enderror" 
                                           value="{{ old('saldo') }}" min="0" step="0.01" placeholder="Opsional">
                                </div>
                                @error('saldo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="auto_match" id="autoMatch" value="1"
                                       {{ old('auto_match') ? 'checked' : '' }}>
                                <label class="form-check-label" for="autoMatch">
                                    Coba auto-match dengan transaksi pembayaran
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <button type="submit" name="save_add" value="1" class="btn btn-outline-primary">
                                <i class="bi bi-save"></i> Simpan & Tambah Lagi
                            </button>
                            <a href="{{ route('mutasi-bank.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
