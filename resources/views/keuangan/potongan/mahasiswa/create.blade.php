@extends('layouts.app')

@section('title', 'Tambah Potongan Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Tambah Potongan Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('potongan-mahasiswa.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                                <select name="mahasiswa_id" id="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror" required>
                                    <option value="">Cari mahasiswa...</option>
                                </select>
                                @error('mahasiswa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Potongan <span class="text-danger">*</span></label>
                                <select name="jenis_potongan_id" id="jenis_potongan_id" class="form-select @error('jenis_potongan_id') is-invalid @enderror" required>
                                    <option value="">Pilih Jenis Potongan</option>
                                    @foreach($jenisPotonganList as $jenis)
                                        <option value="{{ $jenis->id }}" 
                                            data-tipe="{{ $jenis->tipe_nilai }}" 
                                            data-nilai="{{ $jenis->nilai_default }}"
                                            {{ old('jenis_potongan_id') == $jenis->id ? 'selected' : '' }}>
                                            {{ $jenis->nama }} ({{ $jenis->kategori_label }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_potongan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Periode Diskon (Opsional)</label>
                                <select name="periode_diskon_id" class="form-select @error('periode_diskon_id') is-invalid @enderror">
                                    <option value="">Tanpa Periode</option>
                                    @foreach($periodeDiskonList as $periode)
                                        <option value="{{ $periode->id }}" {{ old('periode_diskon_id') == $periode->id ? 'selected' : '' }}>
                                            {{ $periode->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('periode_diskon_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipe Nilai <span class="text-danger">*</span></label>
                                <select name="tipe_nilai" id="tipe_nilai" class="form-select @error('tipe_nilai') is-invalid @enderror" required>
                                    <option value="persen" {{ old('tipe_nilai') == 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                                    <option value="nominal" {{ old('tipe_nilai', 'nominal') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                </select>
                                @error('tipe_nilai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nilai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="nilai-prefix">Rp</span>
                                    <input type="number" name="nilai" id="nilai" class="form-control @error('nilai') is-invalid @enderror" value="{{ old('nilai') }}" min="0" step="0.01" required>
                                    @error('nilai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}">
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alasan</label>
                            <input type="text" name="alasan" class="form-control @error('alasan') is-invalid @enderror" value="{{ old('alasan') }}" placeholder="Contoh: Anak pegawai, Prestasi akademik, dll">
                            @error('alasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="auto_approve" class="form-check-input" id="auto_approve" value="1" {{ old('auto_approve') ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto_approve">
                                    <strong>Langsung Setujui</strong> (Skip proses approval)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('potongan-mahasiswa.index') }}" class="btn btn-secondary">
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

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Mahasiswa Select2
    $('#mahasiswa_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Cari NIM atau nama mahasiswa...',
        allowClear: true,
        ajax: {
            url: '{{ route("potongan-mahasiswa.search-mahasiswa") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return { results: data.results };
            }
        },
        minimumInputLength: 2
    });

    // Tipe nilai change
    function updateNilaiPrefix() {
        const prefix = document.getElementById('nilai-prefix');
        const tipeNilai = document.getElementById('tipe_nilai').value;
        prefix.textContent = tipeNilai === 'persen' ? '%' : 'Rp';
    }
    
    document.getElementById('tipe_nilai').addEventListener('change', updateNilaiPrefix);
    updateNilaiPrefix();

    // Auto-fill from jenis potongan
    document.getElementById('jenis_potongan_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const tipe = selected.dataset.tipe;
            const nilai = selected.dataset.nilai;
            
            document.getElementById('tipe_nilai').value = tipe;
            document.getElementById('nilai').value = nilai;
            updateNilaiPrefix();
        }
    });
});
</script>
@endpush
@endsection
