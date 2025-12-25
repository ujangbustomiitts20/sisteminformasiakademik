@extends('layouts.app')

@section('title', 'Tambah Periode Diskon')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Tambah Periode Diskon</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('periode-diskon.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Nama Periode <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Contoh: Diskon Early Bird Semester Ganjil 2025" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jenis Potongan <span class="text-danger">*</span></label>
                                <select name="jenis_potongan_id" class="form-select @error('jenis_potongan_id') is-invalid @enderror" required>
                                    <option value="">Pilih Jenis</option>
                                    @foreach($jenisPotonganList as $jenis)
                                        <option value="{{ $jenis->id }}" {{ old('jenis_potongan_id') == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_potongan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Tipe Nilai <span class="text-danger">*</span></label>
                                <select name="tipe_nilai" id="tipe_nilai" class="form-select @error('tipe_nilai') is-invalid @enderror" required>
                                    <option value="persen" {{ old('tipe_nilai') == 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                                    <option value="nominal" {{ old('tipe_nilai', 'nominal') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                </select>
                                @error('tipe_nilai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nilai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="nilai-prefix">Rp</span>
                                    <input type="number" name="nilai" class="form-control @error('nilai') is-invalid @enderror" value="{{ old('nilai') }}" min="0" step="0.01" required>
                                    @error('nilai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nilai Maksimal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nilai_max" class="form-control @error('nilai_max') is-invalid @enderror" value="{{ old('nilai_max') }}" min="0" step="0.01">
                                    @error('nilai_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Untuk tipe persen</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Min. Transaksi</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="min_transaksi" class="form-control @error('min_transaksi') is-invalid @enderror" value="{{ old('min_transaksi') }}" min="0" step="0.01">
                                    @error('min_transaksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" required>
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kuota</label>
                                <input type="number" name="kuota" class="form-control @error('kuota') is-invalid @enderror" value="{{ old('kuota') }}" min="1">
                                <small class="text-muted">Kosongkan untuk unlimited</small>
                                @error('kuota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">Berlaku Untuk (Opsional)</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Program Studi</label>
                                <select name="program_studi_id[]" class="form-select" multiple size="4">
                                    @foreach($programStudiList as $prodi)
                                        <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih lebih dari satu dengan Ctrl+Klik</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Angkatan</label>
                                <select name="angkatan[]" class="form-select" multiple size="4">
                                    @for($i = date('Y'); $i >= date('Y') - 6; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jenis Tagihan</label>
                                <select name="jenis_tagihan[]" class="form-select" multiple size="4">
                                    <option value="SPP">SPP</option>
                                    <option value="UKT">UKT</option>
                                    <option value="Daftar Ulang">Daftar Ulang</option>
                                    <option value="Praktikum">Praktikum</option>
                                    <option value="Wisuda">Wisuda</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Syarat & Ketentuan</label>
                            <textarea name="syarat_ketentuan" class="form-control @error('syarat_ketentuan') is-invalid @enderror" rows="4" placeholder="Tuliskan syarat dan ketentuan diskon...">{{ old('syarat_ketentuan') }}</textarea>
                            @error('syarat_ketentuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('periode-diskon.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tipe_nilai').addEventListener('change', function() {
    const prefix = document.getElementById('nilai-prefix');
    if (this.value === 'persen') {
        prefix.textContent = '%';
    } else {
        prefix.textContent = 'Rp';
    }
});

document.getElementById('tipe_nilai').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
