@extends('layouts.app')

@section('title', 'Edit Sekolah')

@section('content')
<div class="page-title">
    <h4>Edit Sekolah</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sekolah.index') }}">Sekolah</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-pencil me-2"></i>Form Edit Sekolah</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sekolah.update', $sekolah) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NPSN</label>
                            <input type="text" name="npsn" class="form-control @error('npsn') is-invalid @enderror" 
                                   value="{{ old('npsn', $sekolah->npsn) }}" maxlength="20" placeholder="Nomor Pokok Sekolah Nasional">
                            @error('npsn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Opsional - 8 digit</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenjang <span class="text-danger">*</span></label>
                            <select name="jenjang" class="form-select @error('jenjang') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenjang --</option>
                                @foreach(['SMA', 'SMK', 'MA', 'MAK'] as $j)
                                <option value="{{ $j }}" {{ old('jenjang', $sekolah->jenjang) == $j ? 'selected' : '' }}>{{ $j }}</option>
                                @endforeach
                            </select>
                            @error('jenjang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Negeri" {{ old('status', $sekolah->status) == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                                <option value="Swasta" {{ old('status', $sekolah->status) == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                               value="{{ old('nama', $sekolah->nama) }}" required placeholder="Contoh: SMA Negeri 1 Jakarta">
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3"><i class="bi bi-geo-alt me-2"></i>Lokasi Sekolah</h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                            <select name="provinsi_id" id="provinsi_id" class="form-select @error('provinsi_id') is-invalid @enderror" 
                                    required data-current="{{ old('provinsi_id', $sekolah->provinsi_id) }}">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinsi as $prov)
                                <option value="{{ $prov->id }}" {{ old('provinsi_id', $sekolah->provinsi_id) == $prov->id ? 'selected' : '' }}>
                                    {{ $prov->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('provinsi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                            <select name="kabupaten_id" id="kabupaten_id" class="form-select @error('kabupaten_id') is-invalid @enderror" 
                                    required data-current="{{ old('kabupaten_id', $sekolah->kabupaten_id) }}">
                                <option value="">-- Pilih Kabupaten --</option>
                                @if($sekolah->kabupaten)
                                <option value="{{ $sekolah->kabupaten_id }}" selected>{{ $sekolah->kabupaten->nama }}</option>
                                @endif
                            </select>
                            @error('kabupaten_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kecamatan</label>
                            <select name="kecamatan_id" id="kecamatan_id" class="form-select @error('kecamatan_id') is-invalid @enderror" 
                                    data-current="{{ old('kecamatan_id', $sekolah->kecamatan_id) }}">
                                <option value="">-- Pilih Kecamatan --</option>
                                @if($sekolah->kecamatan)
                                <option value="{{ $sekolah->kecamatan_id }}" selected>{{ $sekolah->kecamatan->nama }}</option>
                                @endif
                            </select>
                            @error('kecamatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                                  rows="2" placeholder="Jalan, nomor, RT/RW">{{ old('alamat', $sekolah->alamat) }}</textarea>
                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                   value="1" {{ old('is_active', $sekolah->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Sekolah Aktif</label>
                        </div>
                        <small class="text-muted">Sekolah aktif akan tampil di pilihan saat input data mahasiswa</small>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update
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
        <!-- Info Sekolah -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-building me-2"></i>Info Sekolah</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">NPSN</small>
                    <span class="font-monospace">{{ $sekolah->npsn ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Dibuat</small>
                    {{ $sekolah->created_at->format('d M Y H:i') }}
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Terakhir Diupdate</small>
                    {{ $sekolah->updated_at->format('d M Y H:i') }}
                </div>
            </div>
        </div>

        <!-- Statistik Mahasiswa -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i>Mahasiswa dari Sekolah Ini</h6>
            </div>
            <div class="card-body">
                @php
                    $mahasiswaCount = $sekolah->mahasiswa()->count();
                @endphp
                <div class="text-center py-3">
                    <h2 class="mb-0 text-primary">{{ $mahasiswaCount }}</h2>
                    <small class="text-muted">Mahasiswa</small>
                </div>
                @if($mahasiswaCount > 0)
                <hr>
                <small class="text-muted d-block mb-2">Mahasiswa Terbaru:</small>
                @foreach($sekolah->mahasiswa()->latest()->take(5)->get() as $mhs)
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">{{ $mhs->nama }}</span>
                    <span class="badge bg-secondary">{{ $mhs->angkatan }}</span>
                </div>
                @endforeach
                @endif
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
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kecamatanSelect.disabled = true;

        if (!provinsiId) {
            kabupatenSelect.disabled = true;
            return;
        }

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

        if (!kabupatenId) {
            kecamatanSelect.disabled = true;
            return;
        }

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
});
</script>
@endpush
