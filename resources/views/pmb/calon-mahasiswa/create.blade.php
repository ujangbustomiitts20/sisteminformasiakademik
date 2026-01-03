@extends('layouts.app')

@section('title', 'Tambah Calon Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Tambah Calon Mahasiswa</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pmb.calon-mahasiswa.index') }}">Calon Mahasiswa</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('pmb.calon-mahasiswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Pilih Gelombang & Jalur -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-clipboard-check me-2"></i>Pilihan Pendaftaran
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="gelombang_pmb_id" class="form-label">Gelombang PMB <span class="text-danger">*</span></label>
                        <select class="form-select @error('gelombang_pmb_id') is-invalid @enderror" id="gelombang_pmb_id" name="gelombang_pmb_id" required>
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}" {{ old('gelombang_pmb_id') == $gelombang->id ? 'selected' : '' }}>
                                    {{ $gelombang->periodePmb->nama ?? '' }} - {{ $gelombang->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('gelombang_pmb_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="jalur_seleksi_id" class="form-label">Jalur Seleksi <span class="text-danger">*</span></label>
                        <select class="form-select @error('jalur_seleksi_id') is-invalid @enderror" id="jalur_seleksi_id" name="jalur_seleksi_id" required>
                            <option value="">-- Pilih Jalur --</option>
                            @foreach($jalurs as $jalur)
                                <option value="{{ $jalur->id }}" {{ old('jalur_seleksi_id') == $jalur->id ? 'selected' : '' }}>
                                    {{ $jalur->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('jalur_seleksi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="program_studi_id" class="form-label">Program Studi Pilihan 1 <span class="text-danger">*</span></label>
                        <select class="form-select @error('program_studi_id') is-invalid @enderror" id="program_studi_id" name="program_studi_id" required>
                            <option value="">-- Pilih Prodi --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_studi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="program_studi_2_id" class="form-label">Program Studi Pilihan 2</label>
                        <select class="form-select @error('program_studi_2_id') is-invalid @enderror" id="program_studi_2_id" name="program_studi_2_id">
                            <option value="">-- Pilih Prodi --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('program_studi_2_id') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_studi_2_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Pribadi -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person me-2"></i>Data Pribadi
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16">
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nisn" class="form-label">NISN</label>
                        <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" name="nisn" value="{{ old('nisn') }}" maxlength="10">
                        @error('nisn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="agama" class="form-label">Agama</label>
                        <select class="form-select @error('agama') is-invalid @enderror" id="agama" name="agama">
                            <option value="">-- Pilih --</option>
                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                        @error('agama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontak -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-telephone me-2"></i>Kontak
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="no_hp" class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="no_wa" class="form-label">No. WhatsApp</label>
                        <input type="text" class="form-control @error('no_wa') is-invalid @enderror" id="no_wa" name="no_wa" value="{{ old('no_wa') }}">
                        @error('no_wa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Alamat -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-geo-alt me-2"></i>Alamat
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="rt" class="form-label">RT</label>
                        <input type="text" class="form-control" id="rt" name="rt" value="{{ old('rt') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="rw" class="form-label">RW</label>
                        <input type="text" class="form-control" id="rw" name="rw" value="{{ old('rw') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="provinsi_id" class="form-label">Provinsi</label>
                        <select class="form-select" id="provinsi_id" name="provinsi_id">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                        <input type="hidden" id="provinsi" name="provinsi" value="{{ old('provinsi') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kabupaten_id" class="form-label">Kabupaten/Kota</label>
                        <select class="form-select" id="kabupaten_id" name="kabupaten_id" disabled>
                            <option value="">-- Pilih Kabupaten --</option>
                        </select>
                        <input type="hidden" id="kabupaten" name="kabupaten" value="{{ old('kabupaten') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kecamatan_id" class="form-label">Kecamatan</label>
                        <select class="form-select" id="kecamatan_id" name="kecamatan_id" disabled>
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                        <input type="hidden" id="kecamatan" name="kecamatan" value="{{ old('kecamatan') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kelurahan_id" class="form-label">Kelurahan/Desa</label>
                        <select class="form-select" id="kelurahan_id" name="kelurahan_id" disabled>
                            <option value="">-- Pilih Kelurahan --</option>
                        </select>
                        <input type="hidden" id="kelurahan" name="kelurahan" value="{{ old('kelurahan') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="kode_pos" class="form-label">Kode Pos</label>
                        <input type="text" class="form-control" id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Asal Sekolah -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-building me-2"></i>Asal Sekolah
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="sekolah_provinsi_id" class="form-label">Provinsi Sekolah</label>
                        <select class="form-select" id="sekolah_provinsi_id">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="sekolah_kabupaten_id" class="form-label">Kabupaten Sekolah</label>
                        <select class="form-select" id="sekolah_kabupaten_id" disabled>
                            <option value="">-- Pilih Kabupaten --</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sekolah_id" class="form-label">Pilih Sekolah <span class="text-danger">*</span></label>
                        <select class="form-select @error('asal_sekolah') is-invalid @enderror" id="sekolah_id" name="sekolah_id" disabled>
                            <option value="">-- Pilih Sekolah --</option>
                        </select>
                        @error('asal_sekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="asal_sekolah" class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('asal_sekolah') is-invalid @enderror" id="asal_sekolah" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required>
                        <small class="text-muted">Otomatis terisi saat memilih sekolah, atau isi manual jika tidak ada</small>
                        @error('asal_sekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="npsn_sekolah" class="form-label">NPSN</label>
                        <input type="text" class="form-control" id="npsn_sekolah" name="npsn_sekolah" value="{{ old('npsn_sekolah') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="jurusan_sekolah" class="form-label">Jurusan</label>
                        <input type="text" class="form-control" id="jurusan_sekolah" name="jurusan_sekolah" value="{{ old('jurusan_sekolah') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tahun_lulus" class="form-label">Tahun Lulus <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('tahun_lulus') is-invalid @enderror" id="tahun_lulus" name="tahun_lulus" value="{{ old('tahun_lulus', date('Y')) }}" min="2000" max="{{ date('Y') }}" required>
                        @error('tahun_lulus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nilai_rata_rata" class="form-label">Nilai Rata-rata</label>
                        <input type="number" step="0.01" class="form-control" id="nilai_rata_rata" name="nilai_rata_rata" value="{{ old('nilai_rata_rata') }}" min="0" max="100">
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Orang Tua -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-people me-2"></i>Data Orang Tua
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nama_ayah" class="form-label">Nama Ayah</label>
                        <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pekerjaan_ayah" class="form-label">Pekerjaan Ayah</label>
                        <input type="text" class="form-control" id="pekerjaan_ayah" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="no_hp_ayah" class="form-label">No. HP Ayah</label>
                        <input type="text" class="form-control" id="no_hp_ayah" name="no_hp_ayah" value="{{ old('no_hp_ayah') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nama_ibu" class="form-label">Nama Ibu</label>
                        <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pekerjaan_ibu" class="form-label">Pekerjaan Ibu</label>
                        <input type="text" class="form-control" id="pekerjaan_ibu" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="no_hp_ibu" class="form-label">No. HP Ibu</label>
                        <input type="text" class="form-control" id="no_hp_ibu" name="no_hp_ibu" value="{{ old('no_hp_ibu') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="penghasilan_ortu" class="form-label">Penghasilan Orang Tua (per bulan)</label>
                        <input type="number" class="form-control" id="penghasilan_ortu" name="penghasilan_ortu" value="{{ old('penghasilan_ortu') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Foto -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-camera me-2"></i>Foto
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="foto" class="form-label">Pas Foto</label>
                        <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB</small>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Dokumen -->
        @if(isset($settingDokumen) && $settingDokumen->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-file-earmark-arrow-up me-2"></i>Upload Dokumen Persyaratan
            </div>
            <div class="card-body">
                @php
                    $dokumenWajib = $settingDokumen->where('is_wajib', true);
                    $dokumenOpsional = $settingDokumen->where('is_wajib', false);
                @endphp

                {{-- Dokumen Wajib --}}
                @if($dokumenWajib->count() > 0)
                <h6 class="text-danger mb-3"><i class="bi bi-asterisk me-1"></i>Dokumen Wajib</h6>
                <div class="row mb-4">
                    @foreach($dokumenWajib as $setting)
                    <div class="col-md-6 mb-3">
                        <label for="dokumen_{{ $setting->kode }}" class="form-label">
                            {{ $setting->nama_dokumen }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               class="form-control @error('dokumen.'.$setting->kode) is-invalid @enderror" 
                               id="dokumen_{{ $setting->kode }}" 
                               name="dokumen[{{ $setting->kode }}]"
                               accept="{{ $setting->format_file }}">
                        <small class="text-muted">
                            Format: {{ strtoupper(str_replace('.', '', $setting->format_file)) }}. 
                            Maks: {{ $setting->max_size_mb }}MB
                            @if($setting->deskripsi)
                                <br>{{ $setting->deskripsi }}
                            @endif
                        </small>
                        @error('dokumen.'.$setting->kode)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Dokumen Opsional --}}
                @if($dokumenOpsional->count() > 0)
                <h6 class="text-muted mb-3"><i class="bi bi-file-earmark me-1"></i>Dokumen Opsional</h6>
                <div class="row">
                    @foreach($dokumenOpsional as $setting)
                    <div class="col-md-6 mb-3">
                        <label for="dokumen_{{ $setting->kode }}" class="form-label">
                            {{ $setting->nama_dokumen }}
                        </label>
                        <input type="file" 
                               class="form-control @error('dokumen.'.$setting->kode) is-invalid @enderror" 
                               id="dokumen_{{ $setting->kode }}" 
                               name="dokumen[{{ $setting->kode }}]"
                               accept="{{ $setting->format_file }}">
                        <small class="text-muted">
                            Format: {{ strtoupper(str_replace('.', '', $setting->format_file)) }}. 
                            Maks: {{ $setting->max_size_mb }}MB
                            @if($setting->deskripsi)
                                <br>{{ $setting->deskripsi }}
                            @endif
                        </small>
                        @error('dokumen.'.$setting->kode)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy me-1"></i> Simpan
            </button>
            <a href="{{ route('pmb.calon-mahasiswa.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === WILAYAH ALAMAT ===
    const provinsiSelect = document.getElementById('provinsi_id');
    const kabupatenSelect = document.getElementById('kabupaten_id');
    const kecamatanSelect = document.getElementById('kecamatan_id');
    const kelurahanSelect = document.getElementById('kelurahan_id');
    const kodePosInput = document.getElementById('kode_pos');
    
    // Hidden inputs untuk nama
    const provinsiInput = document.getElementById('provinsi');
    const kabupatenInput = document.getElementById('kabupaten');
    const kecamatanInput = document.getElementById('kecamatan');
    const kelurahanInput = document.getElementById('kelurahan');
    
    // Load provinsi
    fetch('{{ route("api.provinsi") }}')
        .then(response => response.json())
        .then(data => {
            data.forEach(prov => {
                const option = new Option(prov.nama, prov.id);
                provinsiSelect.add(option);
            });
        });
    
    // On provinsi change
    provinsiSelect.addEventListener('change', function() {
        const provinsiId = this.value;
        const selectedText = this.options[this.selectedIndex].text;
        provinsiInput.value = selectedText !== '-- Pilih Provinsi --' ? selectedText : '';
        
        // Reset dependent dropdowns
        kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
        kabupatenSelect.disabled = true;
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kecamatanSelect.disabled = true;
        kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        kelurahanSelect.disabled = true;
        
        // Reset hidden inputs
        kabupatenInput.value = '';
        kecamatanInput.value = '';
        kelurahanInput.value = '';
        
        if (provinsiId) {
            fetch(`{{ route("api.kabupaten") }}?provinsi_id=${provinsiId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(kab => {
                        const option = new Option(kab.nama, kab.id);
                        kabupatenSelect.add(option);
                    });
                    kabupatenSelect.disabled = false;
                });
        }
    });
    
    // On kabupaten change
    kabupatenSelect.addEventListener('change', function() {
        const kabupatenId = this.value;
        const selectedText = this.options[this.selectedIndex].text;
        kabupatenInput.value = selectedText !== '-- Pilih Kabupaten --' ? selectedText : '';
        
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kecamatanSelect.disabled = true;
        kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        kelurahanSelect.disabled = true;
        kecamatanInput.value = '';
        kelurahanInput.value = '';
        
        if (kabupatenId) {
            fetch(`{{ route("api.kecamatan") }}?kabupaten_id=${kabupatenId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(kec => {
                        const option = new Option(kec.nama, kec.id);
                        kecamatanSelect.add(option);
                    });
                    kecamatanSelect.disabled = false;
                });
        }
    });
    
    // On kecamatan change
    kecamatanSelect.addEventListener('change', function() {
        const kecamatanId = this.value;
        const selectedText = this.options[this.selectedIndex].text;
        kecamatanInput.value = selectedText !== '-- Pilih Kecamatan --' ? selectedText : '';
        
        kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        kelurahanSelect.disabled = true;
        kelurahanInput.value = '';
        
        if (kecamatanId) {
            fetch(`{{ route("api.kelurahan") }}?kecamatan_id=${kecamatanId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(kel => {
                        const option = new Option(kel.nama, kel.id);
                        option.dataset.kodePos = kel.kode_pos || '';
                        kelurahanSelect.add(option);
                    });
                    kelurahanSelect.disabled = false;
                });
        }
    });
    
    // On kelurahan change
    kelurahanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const selectedText = selectedOption.text;
        kelurahanInput.value = selectedText !== '-- Pilih Kelurahan --' ? selectedText : '';
        
        // Auto fill kode pos
        if (selectedOption.dataset.kodePos) {
            kodePosInput.value = selectedOption.dataset.kodePos;
        }
    });
    
    // === WILAYAH SEKOLAH ===
    const sekolahProvinsiSelect = document.getElementById('sekolah_provinsi_id');
    const sekolahKabupatenSelect = document.getElementById('sekolah_kabupaten_id');
    const sekolahSelect = document.getElementById('sekolah_id');
    const asalSekolahInput = document.getElementById('asal_sekolah');
    const npsnInput = document.getElementById('npsn_sekolah');
    
    // Load provinsi untuk sekolah
    fetch('{{ route("api.provinsi") }}')
        .then(response => response.json())
        .then(data => {
            data.forEach(prov => {
                const option = new Option(prov.nama, prov.id);
                sekolahProvinsiSelect.add(option);
            });
        });
    
    // On sekolah provinsi change
    sekolahProvinsiSelect.addEventListener('change', function() {
        const provinsiId = this.value;
        
        sekolahKabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
        sekolahKabupatenSelect.disabled = true;
        sekolahSelect.innerHTML = '<option value="">-- Pilih Sekolah --</option>';
        sekolahSelect.disabled = true;
        
        if (provinsiId) {
            fetch(`{{ route("api.kabupaten") }}?provinsi_id=${provinsiId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(kab => {
                        const option = new Option(kab.nama, kab.id);
                        sekolahKabupatenSelect.add(option);
                    });
                    sekolahKabupatenSelect.disabled = false;
                });
        }
    });
    
    // On sekolah kabupaten change
    sekolahKabupatenSelect.addEventListener('change', function() {
        const kabupatenId = this.value;
        const provinsiId = sekolahProvinsiSelect.value;
        
        sekolahSelect.innerHTML = '<option value="">-- Pilih Sekolah --</option>';
        sekolahSelect.disabled = true;
        
        if (kabupatenId) {
            fetch(`{{ route("api.sekolah") }}?provinsi_id=${provinsiId}&kabupaten_id=${kabupatenId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        const option = new Option('-- Tidak ada data sekolah --', '');
                        sekolahSelect.add(option);
                    } else {
                        data.forEach(sekolah => {
                            const option = new Option(`${sekolah.nama} (${sekolah.npsn})`, sekolah.id);
                            option.dataset.nama = sekolah.nama;
                            option.dataset.npsn = sekolah.npsn;
                            sekolahSelect.add(option);
                        });
                    }
                    sekolahSelect.disabled = false;
                });
        }
    });
    
    // On sekolah change
    sekolahSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.dataset.nama) {
            asalSekolahInput.value = selectedOption.dataset.nama;
            npsnInput.value = selectedOption.dataset.npsn || '';
        }
    });
});
</script>
@endpush
