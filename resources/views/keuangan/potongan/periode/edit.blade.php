@extends('layouts.app')

@section('title', 'Edit Periode Diskon')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Periode Diskon</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Kode: <strong>{{ $periodeDiskon->kode }}</strong>
                    </div>

                    <form action="{{ route('periode-diskon.update', $periodeDiskon) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Nama Periode <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $periodeDiskon->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jenis Potongan <span class="text-danger">*</span></label>
                                <select name="jenis_potongan_id" class="form-select @error('jenis_potongan_id') is-invalid @enderror" required>
                                    <option value="">Pilih Jenis</option>
                                    @foreach($jenisPotonganList as $jenis)
                                        <option value="{{ $jenis->id }}" {{ old('jenis_potongan_id', $periodeDiskon->jenis_potongan_id) == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama }}</option>
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
                                    <option value="persen" {{ old('tipe_nilai', $periodeDiskon->tipe_nilai) == 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                                    <option value="nominal" {{ old('tipe_nilai', $periodeDiskon->tipe_nilai) == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                </select>
                                @error('tipe_nilai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nilai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="nilai-prefix">Rp</span>
                                    <input type="number" name="nilai" class="form-control @error('nilai') is-invalid @enderror" value="{{ old('nilai', $periodeDiskon->nilai) }}" min="0" step="0.01" required>
                                    @error('nilai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nilai Maksimal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nilai_max" class="form-control @error('nilai_max') is-invalid @enderror" value="{{ old('nilai_max', $periodeDiskon->nilai_max) }}" min="0" step="0.01">
                                    @error('nilai_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Min. Transaksi</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="min_transaksi" class="form-control @error('min_transaksi') is-invalid @enderror" value="{{ old('min_transaksi', $periodeDiskon->min_transaksi) }}" min="0" step="0.01">
                                    @error('min_transaksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', $periodeDiskon->tanggal_mulai->format('Y-m-d')) }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai', $periodeDiskon->tanggal_selesai->format('Y-m-d')) }}" required>
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kuota</label>
                                <input type="number" name="kuota" class="form-control @error('kuota') is-invalid @enderror" value="{{ old('kuota', $periodeDiskon->kuota) }}" min="1">
                                <small class="text-muted">Terpakai: {{ $periodeDiskon->kuota_terpakai }}</small>
                                @error('kuota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">Berlaku Untuk (Opsional)</h6>

                        @php
                            $berlakuUntuk = $periodeDiskon->berlaku_untuk ?? [];
                        @endphp

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Program Studi</label>
                                <select name="program_studi_id[]" class="form-select" multiple size="4">
                                    @foreach($programStudiList as $prodi)
                                        <option value="{{ $prodi->id }}" {{ in_array($prodi->id, $berlakuUntuk['program_studi_id'] ?? []) ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Angkatan</label>
                                <select name="angkatan[]" class="form-select" multiple size="4">
                                    @for($i = date('Y'); $i >= date('Y') - 6; $i--)
                                        <option value="{{ $i }}" {{ in_array($i, $berlakuUntuk['angkatan'] ?? []) ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jenis Tagihan</label>
                                <select name="jenis_tagihan[]" class="form-select" multiple size="4">
                                    @foreach(['SPP', 'UKT', 'Daftar Ulang', 'Praktikum', 'Wisuda', 'Lainnya'] as $jt)
                                        <option value="{{ $jt }}" {{ in_array($jt, $berlakuUntuk['jenis_tagihan'] ?? []) ? 'selected' : '' }}>{{ $jt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Syarat & Ketentuan</label>
                            <textarea name="syarat_ketentuan" class="form-control @error('syarat_ketentuan') is-invalid @enderror" rows="4">{{ old('syarat_ketentuan', $periodeDiskon->syarat_ketentuan) }}</textarea>
                            @error('syarat_ketentuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('periode-diskon.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
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
