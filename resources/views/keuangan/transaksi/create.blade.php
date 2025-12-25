@extends('layouts.app')

@section('title', 'Input Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Input Pembayaran</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('transaksi-pembayaran.index') }}">Transaksi</a></li>
                <li class="breadcrumb-item active">Input</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-cash me-2"></i>Form Pembayaran</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('transaksi-pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tagihan <span class="text-danger">*</span></label>
                            <select name="tagihan_id" class="form-select @error('tagihan_id') is-invalid @enderror" id="tagihanSelect" required>
                                <option value="">Pilih Tagihan</option>
                                @foreach($tagihanList as $t)
                                    <option value="{{ $t->id }}" 
                                            data-sisa="{{ $t->sisa_tagihan }}"
                                            data-mhs="{{ $t->mahasiswa->nama ?? '-' }}"
                                            {{ (old('tagihan_id') == $t->id || ($tagihan && $tagihan->id == $t->id)) ? 'selected' : '' }}>
                                        {{ $t->no_tagihan }} - {{ $t->mahasiswa->nim ?? '-' }} {{ $t->mahasiswa->nama ?? '-' }} (Sisa: Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tagihan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info" id="infoTagihan" style="display: none;">
                            <strong>Mahasiswa:</strong> <span id="infoMhs">-</span><br>
                            <strong>Sisa Tagihan:</strong> Rp <span id="infoSisa">0</span>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah_bayar" class="form-control @error('jumlah_bayar') is-invalid @enderror" id="jumlahBayar" value="{{ old('jumlah_bayar', $tagihan->sisa_tagihan ?? '') }}" min="1000" required>
                                </div>
                                @error('jumlah_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_bayar" class="form-control @error('tanggal_bayar') is-invalid @enderror" value="{{ old('tanggal_bayar', now()->format('Y-m-d')) }}" required>
                                @error('tanggal_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select name="metode_pembayaran" class="form-select @error('metode_pembayaran') is-invalid @enderror" required>
                                    @foreach(\App\Models\TransaksiPembayaran::METODE as $key => $label)
                                        <option value="{{ $key }}" {{ old('metode_pembayaran') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('metode_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bukti Pembayaran</label>
                                <input type="file" name="bukti_pembayaran" class="form-control @error('bukti_pembayaran') is-invalid @enderror" accept="image/*">
                                @error('bukti_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: JPG, PNG. Maks: 2MB</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="2">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i>Simpan Pembayaran
                            </button>
                            <a href="{{ route('transaksi-pembayaran.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tagihanSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const info = document.getElementById('infoTagihan');
    
    if (selected.value) {
        info.style.display = 'block';
        document.getElementById('infoMhs').textContent = selected.dataset.mhs;
        document.getElementById('infoSisa').textContent = parseInt(selected.dataset.sisa).toLocaleString('id-ID');
        document.getElementById('jumlahBayar').max = selected.dataset.sisa;
        document.getElementById('jumlahBayar').value = selected.dataset.sisa;
    } else {
        info.style.display = 'none';
    }
});

// Trigger on page load if tagihan is pre-selected
if (document.getElementById('tagihanSelect').value) {
    document.getElementById('tagihanSelect').dispatchEvent(new Event('change'));
}
</script>
@endpush
@endsection
