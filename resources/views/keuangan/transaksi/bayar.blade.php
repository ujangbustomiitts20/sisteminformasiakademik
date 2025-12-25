@extends('layouts.app')

@section('title', 'Bayar Tagihan')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Pembayaran</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan</a></li>
                <li class="breadcrumb-item active">Bayar</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Detail Tagihan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Detail Tagihan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">No. Tagihan</label>
                            <p class="mb-0 fw-bold">{{ $tagihan->no_tagihan }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Jenis Tagihan</label>
                            <p class="mb-0"><span class="badge bg-info">{{ $tagihan->jenis_tagihan }}</span></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Nominal</label>
                            <p class="mb-0">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Diskon/Potongan</label>
                            <p class="mb-0 text-success">- Rp {{ number_format($tagihan->diskon, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">Total yang Harus Dibayar</label>
                            <p class="mb-0 fw-bold">Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Sisa Tagihan</label>
                            <p class="mb-0 fw-bold fs-4 text-danger">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Bukti -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Upload Bukti Pembayaran</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('pembayaran.upload-bukti', $tagihan) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Jumlah yang Dibayar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah_bayar" class="form-control @error('jumlah_bayar') is-invalid @enderror" value="{{ old('jumlah_bayar', $tagihan->sisa_tagihan) }}" min="10000" max="{{ $tagihan->sisa_tagihan }}" required>
                            </div>
                            @error('jumlah_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal Rp 10.000, maksimal Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                            <input type="file" name="bukti_pembayaran" class="form-control @error('bukti_pembayaran') is-invalid @enderror" accept="image/*" required>
                            @error('bukti_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: JPG, PNG. Maks: 2MB</small>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload me-1"></i>Upload Bukti
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Info Virtual Account -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Virtual Account</h5>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted mb-2">Transfer ke Virtual Account:</p>
                    <h4 class="text-primary mb-2">{{ $va->bank_name }}</h4>
                    <div class="bg-light p-3 rounded mb-3">
                        <h3 class="mb-0 font-monospace">{{ $va->formatted_va }}</h3>
                    </div>
                    <p class="text-muted small mb-0">a.n. {{ $mahasiswa->nama }}</p>
                </div>
            </div>

            <!-- Cara Pembayaran -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Cara Pembayaran</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Transfer ke nomor Virtual Account di atas sesuai jumlah tagihan</li>
                        <li class="mb-2">Simpan bukti transfer</li>
                        <li class="mb-2">Upload bukti transfer di form sebelah kiri</li>
                        <li class="mb-2">Tunggu verifikasi dari admin (1x24 jam)</li>
                        <li>Status pembayaran akan diupdate setelah diverifikasi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
