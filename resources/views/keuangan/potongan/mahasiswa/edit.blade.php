@extends('layouts.app')

@section('title', 'Edit Potongan Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Potongan Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-info-circle me-2"></i>
                                Kode: <strong>{{ $potonganMahasiswa->kode }}</strong>
                            </div>
                            <div class="col-md-6">
                                Mahasiswa: <strong>{{ $potonganMahasiswa->mahasiswa->nama ?? '-' }}</strong> ({{ $potonganMahasiswa->mahasiswa->nim ?? '' }})
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('potongan-mahasiswa.update', $potonganMahasiswa) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Potongan <span class="text-danger">*</span></label>
                                <select name="jenis_potongan_id" class="form-select @error('jenis_potongan_id') is-invalid @enderror" required>
                                    <option value="">Pilih Jenis Potongan</option>
                                    @foreach($jenisPotonganList as $jenis)
                                        <option value="{{ $jenis->id }}" {{ old('jenis_potongan_id', $potonganMahasiswa->jenis_potongan_id) == $jenis->id ? 'selected' : '' }}>
                                            {{ $jenis->nama }} ({{ $jenis->kategori_label }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_potongan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Periode Diskon (Opsional)</label>
                                <select name="periode_diskon_id" class="form-select @error('periode_diskon_id') is-invalid @enderror">
                                    <option value="">Tanpa Periode</option>
                                    @foreach($periodeDiskonList as $periode)
                                        <option value="{{ $periode->id }}" {{ old('periode_diskon_id', $potonganMahasiswa->periode_diskon_id) == $periode->id ? 'selected' : '' }}>
                                            {{ $periode->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('periode_diskon_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tipe Nilai <span class="text-danger">*</span></label>
                                <select name="tipe_nilai" id="tipe_nilai" class="form-select @error('tipe_nilai') is-invalid @enderror" required>
                                    <option value="persen" {{ old('tipe_nilai', $potonganMahasiswa->tipe_nilai) == 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                                    <option value="nominal" {{ old('tipe_nilai', $potonganMahasiswa->tipe_nilai) == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                </select>
                                @error('tipe_nilai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nilai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="nilai-prefix">Rp</span>
                                    <input type="number" name="nilai" class="form-control @error('nilai') is-invalid @enderror" value="{{ old('nilai', $potonganMahasiswa->nilai) }}" min="0" step="0.01" required>
                                    @error('nilai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nilai Maksimal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nilai_max" class="form-control @error('nilai_max') is-invalid @enderror" value="{{ old('nilai_max', $potonganMahasiswa->nilai_max) }}" min="0" step="0.01">
                                    @error('nilai_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', $potonganMahasiswa->tanggal_mulai?->format('Y-m-d')) }}">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai', $potonganMahasiswa->tanggal_selesai?->format('Y-m-d')) }}">
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alasan</label>
                            <input type="text" name="alasan" class="form-control @error('alasan') is-invalid @enderror" value="{{ old('alasan', $potonganMahasiswa->alasan) }}">
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan', $potonganMahasiswa->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('potongan-mahasiswa.index') }}" class="btn btn-secondary">
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
    prefix.textContent = this.value === 'persen' ? '%' : 'Rp';
});

document.getElementById('tipe_nilai').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
