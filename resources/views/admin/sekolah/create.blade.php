@extends('layouts.app')

@section('title', 'Tambah Sekolah')

@section('content')
<div class="page-title">
    <h4>Tambah Sekolah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sekolah.index') }}">Sekolah</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-building me-2"></i>Form Tambah Sekolah</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sekolah.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NPSN</label>
                            <input type="text" name="npsn" class="form-control @error('npsn') is-invalid @enderror" 
                                   value="{{ old('npsn') }}" maxlength="20" placeholder="Nomor Pokok Sekolah Nasional">
                            @error('npsn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Opsional - 8 digit</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenjang <span class="text-danger">*</span></label>
                            <select name="jenjang" class="form-select @error('jenjang') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenjang --</option>
                                @foreach(['SMA', 'SMK', 'MA', 'MAK'] as $j)
                                <option value="{{ $j }}" {{ old('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                @endforeach
                            </select>
                            @error('jenjang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Negeri" {{ old('status') == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                                <option value="Swasta" {{ old('status') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                               value="{{ old('nama') }}" required placeholder="Contoh: SMA Negeri 1 Jakarta">
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3"><i class="bi bi-geo-alt me-2"></i>Lokasi Sekolah</h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                            <select name="provinsi_id" id="provinsi_id" class="form-select @error('provinsi_id') is-invalid @enderror" 
                                    required data-current="{{ old('provinsi_id') }}">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinsi as $prov)
                                <option value="{{ $prov->id }}" {{ old('provinsi_id') == $prov->id ? 'selected' : '' }}>
                                    {{ $prov->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('provinsi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                            <select name="kabupaten_id" id="kabupaten_id" class="form-select @error('kabupaten_id') is-invalid @enderror" 
                                    required disabled data-current="{{ old('kabupaten_id') }}">
                                <option value="">-- Pilih Kabupaten --</option>
                            </select>
                            @error('kabupaten_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kecamatan</label>
                            <select name="kecamatan_id" id="kecamatan_id" class="form-select @error('kecamatan_id') is-invalid @enderror" 
                                    disabled data-current="{{ old('kecamatan_id') }}">
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                            @error('kecamatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                                  rows="2" placeholder="Jalan, nomor, RT/RW">{{ old('alamat') }}</textarea>
                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                   value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Sekolah Aktif</label>
                        </div>
                        <small class="text-muted">Sekolah aktif akan tampil di pilihan saat input data mahasiswa</small>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('sekolah.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">
                    Data sekolah digunakan untuk referensi asal sekolah mahasiswa saat pendaftaran atau input data.
                </p>
                <h6 class="small fw-bold">Jenjang:</h6>
                <ul class="small text-muted mb-3">
                    <li><strong>SMA</strong> - Sekolah Menengah Atas</li>
                    <li><strong>SMK</strong> - Sekolah Menengah Kejuruan</li>
                    <li><strong>MA</strong> - Madrasah Aliyah</li>
                    <li><strong>MAK</strong> - Madrasah Aliyah Kejuruan</li>
                </ul>
                <h6 class="small fw-bold">Tips:</h6>
                <ul class="small text-muted mb-0">
                    <li>NPSN dapat dicari di <a href="https://referensi.data.kemdikbud.go.id" target="_blank">Data Referensi Kemdikbud</a></li>
                    <li>Pastikan nama sekolah ditulis lengkap dan benar</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinsiSelect = document.getElementById('provinsi_id');
    const kabupatenSelect = document.getElementById('kabupaten_id');
    const kecamatanSelect = document.getElementById('kecamatan_id');

    // Load kabupaten saat provinsi berubah
    provinsiSelect.addEventListener('change', function() {
        const provinsiId = this.value;
        
        kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
        kabupatenSelect.disabled = true;
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kecamatanSelect.disabled = true;

        if (!provinsiId) return;

        fetch(`{{ url('api/kabupaten') }}?provinsi_id=${provinsiId}`)
            .then(response => response.json())
            .then(data => {
                const currentVal = kabupatenSelect.dataset.current;
                data.forEach(item => {
                    const selected = currentVal == item.id ? 'selected' : '';
                    kabupatenSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.nama}</option>`;
                });
                kabupatenSelect.disabled = false;
                if (currentVal) kabupatenSelect.dispatchEvent(new Event('change'));
            })
            .catch(error => console.error('Error:', error));
    });

    // Load kecamatan saat kabupaten berubah
    kabupatenSelect.addEventListener('change', function() {
        const kabupatenId = this.value;
        
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kecamatanSelect.disabled = true;

        if (!kabupatenId) return;

        fetch(`{{ url('api/kecamatan') }}?kabupaten_id=${kabupatenId}`)
            .then(response => response.json())
            .then(data => {
                const currentVal = kecamatanSelect.dataset.current;
                data.forEach(item => {
                    const selected = currentVal == item.id ? 'selected' : '';
                    kecamatanSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.nama}</option>`;
                });
                kecamatanSelect.disabled = false;
            })
            .catch(error => console.error('Error:', error));
    });

    // Trigger load jika ada nilai old
    if (provinsiSelect.value) {
        provinsiSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
