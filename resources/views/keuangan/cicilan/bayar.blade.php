@extends('layouts.app')

@section('title', 'Bayar Cicilan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Bayar Cicilan ke-{{ $detailCicilan->cicilan_ke }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cicilan.index') }}">Cicilan</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cicilan.show', $detailCicilan->cicilan) }}">Detail</a></li>
                    <li class="breadcrumb-item active">Bayar</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Pembayaran Cicilan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cicilan.proses-bayar', $detailCicilan) }}" method="POST">
                        @csrf
                        
                        @php
                            $totalBayar = $detailCicilan->nominal + $denda;
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Cicilan ke-{{ $detailCicilan->cicilan_ke }}</h6>
                                    <hr>
                                    <p class="mb-1">Jatuh Tempo: <strong>{{ $detailCicilan->jatuh_tempo->format('d M Y') }}</strong></p>
                                    @if($detailCicilan->isTerlambat())
                                    <p class="mb-1 text-danger">Terlambat: <strong>{{ $detailCicilan->getHariTerlambat() }} hari</strong></p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td>Nominal Cicilan</td>
                                        <td class="text-end">Rp {{ number_format($detailCicilan->nominal, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($denda > 0)
                                    <tr class="text-danger">
                                        <td>Denda Keterlambatan</td>
                                        <td class="text-end">Rp {{ number_format($denda, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="table-success">
                                        <td><strong>Total Bayar</strong></td>
                                        <td class="text-end"><strong>Rp {{ number_format($totalBayar, 0, ',', '.') }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                                        value="{{ old('jumlah', $totalBayar) }}" min="{{ $totalBayar }}" required>
                                </div>
                                @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select name="metode_pembayaran" class="form-select @error('metode_pembayaran') is-invalid @enderror" required>
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="Tunai" {{ old('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                    <option value="Transfer Bank" {{ old('metode_pembayaran') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="Virtual Account" {{ old('metode_pembayaran') == 'Virtual Account' ? 'selected' : '' }}>Virtual Account</option>
                                    <option value="QRIS" {{ old('metode_pembayaran') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                                </select>
                                @error('metode_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_bayar" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                                value="{{ old('tanggal_bayar', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            @error('tanggal_bayar')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                rows="3" placeholder="Catatan pembayaran (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Konfirmasi pembayaran cicilan?')">
                                <i class="bi bi-check-lg me-1"></i> Proses Pembayaran
                            </button>
                            <a href="{{ route('cicilan.show', $detailCicilan->cicilan) }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Info Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Nama</td>
                            <td>{{ $detailCicilan->cicilan->tagihan->mahasiswa->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>NIM</td>
                            <td>{{ $detailCicilan->cicilan->tagihan->mahasiswa->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Program Studi</td>
                            <td>{{ $detailCicilan->cicilan->tagihan->mahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tagihan</td>
                            <td>{{ $detailCicilan->cicilan->tagihan->jenis_tagihan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Skema</td>
                            <td>{{ $detailCicilan->cicilan->skemaCicilan->nama ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
