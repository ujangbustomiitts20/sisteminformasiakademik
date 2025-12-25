@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Edit Tagihan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Tagihan: {{ $tagihan->no_tagihan }}</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info mb-4">
                        <strong>Info Mahasiswa:</strong> {{ $tagihan->mahasiswa->nim }} - {{ $tagihan->mahasiswa->nama }}
                    </div>

                    <form action="{{ route('tagihan.update', $tagihan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nominal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal', $tagihan->nominal) }}" min="0" required>
                                </div>
                                @error('nominal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Diskon/Potongan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="diskon" class="form-control @error('diskon') is-invalid @enderror" value="{{ old('diskon', $tagihan->diskon) }}" min="0">
                                </div>
                                @error('diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_jatuh_tempo" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" value="{{ old('tanggal_jatuh_tempo', $tagihan->tanggal_jatuh_tempo) }}" required>
                                @error('tanggal_jatuh_tempo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="Belum Bayar" {{ old('status', $tagihan->status) == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                    <option value="Cicilan" {{ old('status', $tagihan->status) == 'Cicilan' ? 'selected' : '' }}>Cicilan</option>
                                    <option value="Lunas" {{ old('status', $tagihan->status) == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                                    <option value="Batal" {{ old('status', $tagihan->status) == 'Batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan_tagihan" class="form-control @error('keterangan_tagihan') is-invalid @enderror" rows="3">{{ old('keterangan_tagihan', $tagihan->keterangan_tagihan) }}</textarea>
                            @error('keterangan_tagihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-secondary">
                            <strong>Info Pembayaran:</strong><br>
                            Jumlah Dibayar: Rp {{ number_format($tagihan->jumlah_dibayar, 0, ',', '.') }}<br>
                            Sisa Tagihan: Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('tagihan.show', $tagihan) }}" class="btn btn-secondary">
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
