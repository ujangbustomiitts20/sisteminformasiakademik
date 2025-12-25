@extends('layouts.app')

@section('title', 'Buat Rekonsiliasi Baru')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Buat Rekonsiliasi Baru</h1>
                    <p class="text-muted mb-0">Buat sesi rekonsiliasi bank baru</p>
                </div>
                <a href="{{ route('rekonsiliasi.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('rekonsiliasi.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Akun Bank <span class="text-danger">*</span></label>
                            <select name="akun_bank_id" class="form-select @error('akun_bank_id') is-invalid @enderror" required id="akunBankSelect">
                                <option value="">Pilih Akun Bank</option>
                                @foreach($akunBank as $akun)
                                <option value="{{ $akun->id }}" 
                                        data-saldo="{{ $akun->saldo_sistem }}"
                                        {{ old('akun_bank_id') == $akun->id ? 'selected' : '' }}>
                                    {{ $akun->nama_bank }} - {{ $akun->nomor_rekening }} ({{ $akun->nama_rekening }})
                                </option>
                                @endforeach
                            </select>
                            @error('akun_bank_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Periode Awal <span class="text-danger">*</span></label>
                                <input type="date" name="periode_awal" class="form-control @error('periode_awal') is-invalid @enderror" 
                                       value="{{ old('periode_awal', now()->startOfMonth()->format('Y-m-d')) }}" required>
                                @error('periode_awal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Periode Akhir <span class="text-danger">*</span></label>
                                <input type="date" name="periode_akhir" class="form-control @error('periode_akhir') is-invalid @enderror" 
                                       value="{{ old('periode_akhir', now()->endOfMonth()->format('Y-m-d')) }}" required>
                                @error('periode_akhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Saldo Awal Bank <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="saldo_awal_bank" class="form-control @error('saldo_awal_bank') is-invalid @enderror" 
                                           value="{{ old('saldo_awal_bank', 0) }}" min="0" step="0.01" required>
                                </div>
                                @error('saldo_awal_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Saldo per tanggal awal periode (dari statement bank)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Saldo Akhir Bank <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="saldo_akhir_bank" class="form-control @error('saldo_akhir_bank') is-invalid @enderror" 
                                           value="{{ old('saldo_akhir_bank', 0) }}" min="0" step="0.01" required>
                                </div>
                                @error('saldo_akhir_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Saldo per tanggal akhir periode (dari statement bank)</small>
                            </div>
                        </div>

                        <div class="alert alert-info" id="saldoInfo" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Saldo Sistem:</strong>
                                    <span id="saldoSistem">Rp 0</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Mutasi Pending:</strong>
                                    <span id="mutasiPending">0 transaksi</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" 
                                      rows="2" placeholder="Catatan rekonsiliasi (opsional)">{{ old('catatan') }}</textarea>
                            @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="include_unmatched" id="includeUnmatched" value="1"
                                       {{ old('include_unmatched', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="includeUnmatched">
                                    Sertakan mutasi yang belum di-match dari periode sebelumnya
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Buat Rekonsiliasi
                            </button>
                            <a href="{{ route('rekonsiliasi.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Panduan Rekonsiliasi</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">
                            <strong>Siapkan Statement Bank</strong> - Download statement bank untuk periode yang akan direkonsiliasi
                        </li>
                        <li class="mb-2">
                            <strong>Import Mutasi</strong> - Import data mutasi dari statement bank ke sistem
                        </li>
                        <li class="mb-2">
                            <strong>Buat Rekonsiliasi</strong> - Buat sesi rekonsiliasi dengan memasukkan saldo dari statement
                        </li>
                        <li class="mb-2">
                            <strong>Proses Matching</strong> - Cocokkan mutasi bank dengan transaksi pembayaran di sistem
                        </li>
                        <li class="mb-2">
                            <strong>Review & Approve</strong> - Review hasil rekonsiliasi dan approve jika sudah sesuai
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('akunBankSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const saldo = selected.dataset.saldo;
    
    if (saldo) {
        document.getElementById('saldoInfo').style.display = 'block';
        document.getElementById('saldoSistem').textContent = 'Rp ' + parseInt(saldo).toLocaleString('id-ID');
        
        // Fetch pending mutasi count
        fetch(`/api/akun-bank/${this.value}/pending-count`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('mutasiPending').textContent = data.count + ' transaksi';
            });
    } else {
        document.getElementById('saldoInfo').style.display = 'none';
    }
});
</script>
@endpush
@endsection
