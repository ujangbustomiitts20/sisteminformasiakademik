@extends('layouts.app')

@section('title', 'Edit Refund')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Refund</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('keuangan.refund.index') }}">Refund</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('keuangan.refund.show', $refund) }}">{{ $refund->nomor_refund }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('keuangan.refund.update', $refund) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <!-- Info Mahasiswa (Read Only) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">NIM</small>
                                    <div>{{ $refund->mahasiswa?->nim }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Nama</small>
                                    <div>{{ $refund->mahasiswa?->nama }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaksi Terkait (Read Only) -->
                @if($refund->transaksiPembayaran)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Transaksi Terkait</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-secondary mb-0">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted">No. Transaksi</small>
                                    <div>{{ $refund->transaksiPembayaran->no_transaksi }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Tanggal</small>
                                    <div>{{ $refund->transaksiPembayaran->tanggal_bayar?->format('d/m/Y') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Jumlah</small>
                                    <div class="fw-bold">Rp {{ number_format($refund->transaksiPembayaran->jumlah, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Detail Pengajuan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Refund <span class="text-danger">*</span></label>
                                    <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                        <option value="">Pilih Jenis</option>
                                        @foreach($jenisList as $key => $label)
                                            <option value="{{ $key }}" {{ old('jenis', $refund->jenis) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Pengajuan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="jumlah_pengajuan" class="form-control @error('jumlah_pengajuan') is-invalid @enderror" 
                                            value="{{ old('jumlah_pengajuan', $refund->jumlah_pengajuan) }}" required min="0" step="0.01">
                                    </div>
                                    @error('jumlah_pengajuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alasan Pengajuan <span class="text-danger">*</span></label>
                            <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="4" required>{{ old('alasan', $refund->alasan) }}</textarea>
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dokumen Pendukung</label>
                            @if($refund->dokumen_pendukung)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($refund->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark me-1"></i>Lihat Dokumen Saat Ini
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" accept="image/*,.pdf">
                            <small class="text-muted">Format: JPG, PNG, PDF. Maks: 2MB. Kosongkan jika tidak ingin mengubah.</small>
                            @error('dokumen_pendukung')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bank me-2"></i>Metode Pengembalian</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Metode Refund <span class="text-danger">*</span></label>
                            <select name="metode_refund" id="metodeRefund" class="form-select @error('metode_refund') is-invalid @enderror" required>
                                <option value="">Pilih Metode</option>
                                @foreach($metodeList as $key => $label)
                                    <option value="{{ $key }}" {{ old('metode_refund', $refund->metode_refund) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('metode_refund')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="bankFields" class="{{ old('metode_refund', $refund->metode_refund) == 'transfer' ? '' : 'd-none' }}">
                            <div class="mb-3">
                                <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" name="nama_bank" class="form-control @error('nama_bank') is-invalid @enderror" 
                                    value="{{ old('nama_bank', $refund->nama_bank) }}" placeholder="Contoh: BCA, BNI, Mandiri">
                                @error('nama_bank')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="nomor_rekening" class="form-control @error('nomor_rekening') is-invalid @enderror" 
                                    value="{{ old('nomor_rekening', $refund->nomor_rekening) }}">
                                @error('nomor_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Pemilik Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pemilik_rekening" class="form-control @error('nama_pemilik_rekening') is-invalid @enderror" 
                                    value="{{ old('nama_pemilik_rekening', $refund->nama_pemilik_rekening) }}">
                                @error('nama_pemilik_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('keuangan.refund.show', $refund) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const metodeRefund = document.getElementById('metodeRefund');
    const bankFields = document.getElementById('bankFields');

    metodeRefund.addEventListener('change', function() {
        if (this.value === 'transfer') {
            bankFields.classList.remove('d-none');
            bankFields.querySelectorAll('input').forEach(input => input.required = true);
        } else {
            bankFields.classList.add('d-none');
            bankFields.querySelectorAll('input').forEach(input => input.required = false);
        }
    });
});
</script>
@endpush
