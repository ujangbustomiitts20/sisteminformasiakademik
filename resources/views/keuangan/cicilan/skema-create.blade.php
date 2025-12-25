@extends('layouts.app')

@section('title', 'Tambah Skema Cicilan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tambah Skema Cicilan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('skema-cicilan.index') }}">Skema Cicilan</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Skema Cicilan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('skema-cicilan.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Skema <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                    value="{{ old('nama') }}" placeholder="Contoh: Cicilan 3 Bulan" required>
                                @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Cicilan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="jumlah_cicilan" class="form-control @error('jumlah_cicilan') is-invalid @enderror" 
                                        value="{{ old('jumlah_cicilan', 3) }}" min="2" max="24" required>
                                    <span class="input-group-text">kali</span>
                                </div>
                                @error('jumlah_cicilan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Biaya Admin</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="biaya_admin" class="form-control @error('biaya_admin') is-invalid @enderror" 
                                        value="{{ old('biaya_admin', 0) }}" min="0">
                                </div>
                                <small class="text-muted">Biaya admin flat (sekali bayar)</small>
                                @error('biaya_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Persentase Bunga</label>
                                <div class="input-group">
                                    <input type="number" name="persentase_bunga" class="form-control @error('persentase_bunga') is-invalid @enderror" 
                                        value="{{ old('persentase_bunga', 0) }}" min="0" max="100" step="0.1">
                                    <span class="input-group-text">% / cicilan</span>
                                </div>
                                <small class="text-muted">Bunga per cicilan dari total tagihan</small>
                                @error('persentase_bunga')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimal Tagihan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="minimal_tagihan" class="form-control @error('minimal_tagihan') is-invalid @enderror" 
                                        value="{{ old('minimal_tagihan', 0) }}" min="0">
                                </div>
                                <small class="text-muted">Minimal tagihan untuk bisa menggunakan skema ini</small>
                                @error('minimal_tagihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Interval Pembayaran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="interval_hari" class="form-control @error('interval_hari') is-invalid @enderror" 
                                        value="{{ old('interval_hari', 30) }}" min="7" max="90" required>
                                    <span class="input-group-text">hari</span>
                                </div>
                                <small class="text-muted">Jarak antar jatuh tempo cicilan</small>
                                @error('interval_hari')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                rows="3" placeholder="Deskripsi atau syarat ketentuan...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" 
                                    {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktifkan skema ini</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                            <a href="{{ route('skema-cicilan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>Simulasi Cicilan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Contoh Tagihan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="simulasiTagihan" class="form-control" value="5000000">
                        </div>
                    </div>
                    <hr>
                    <div id="hasilSimulasi">
                        <p class="text-muted small">Masukkan data skema untuk melihat simulasi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function hitungSimulasi() {
    const tagihan = parseFloat(document.getElementById('simulasiTagihan').value) || 0;
    const jumlahCicilan = parseInt(document.querySelector('[name="jumlah_cicilan"]').value) || 0;
    const biayaAdmin = parseFloat(document.querySelector('[name="biaya_admin"]').value) || 0;
    const persentaseBunga = parseFloat(document.querySelector('[name="persentase_bunga"]').value) || 0;

    if (tagihan <= 0 || jumlahCicilan <= 0) {
        document.getElementById('hasilSimulasi').innerHTML = '<p class="text-muted small">Data tidak valid</p>';
        return;
    }

    const totalBunga = tagihan * (persentaseBunga / 100) * jumlahCicilan;
    const totalBayar = tagihan + biayaAdmin + totalBunga;
    const perCicilan = Math.ceil(totalBayar / jumlahCicilan);

    const formatRp = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val);

    document.getElementById('hasilSimulasi').innerHTML = `
        <table class="table table-sm">
            <tr>
                <td>Tagihan</td>
                <td class="text-end">${formatRp(tagihan)}</td>
            </tr>
            <tr>
                <td>Biaya Admin</td>
                <td class="text-end">${formatRp(biayaAdmin)}</td>
            </tr>
            <tr>
                <td>Total Bunga (${persentaseBunga}% x ${jumlahCicilan})</td>
                <td class="text-end">${formatRp(totalBunga)}</td>
            </tr>
            <tr class="table-primary">
                <td><strong>Total Bayar</strong></td>
                <td class="text-end"><strong>${formatRp(totalBayar)}</strong></td>
            </tr>
            <tr class="table-success">
                <td><strong>Per Cicilan (${jumlahCicilan}x)</strong></td>
                <td class="text-end"><strong>${formatRp(perCicilan)}</strong></td>
            </tr>
        </table>
    `;
}

// Event listeners
document.getElementById('simulasiTagihan').addEventListener('input', hitungSimulasi);
document.querySelector('[name="jumlah_cicilan"]').addEventListener('input', hitungSimulasi);
document.querySelector('[name="biaya_admin"]').addEventListener('input', hitungSimulasi);
document.querySelector('[name="persentase_bunga"]').addEventListener('input', hitungSimulasi);

// Initial calculation
hitungSimulasi();
</script>
@endpush
@endsection
