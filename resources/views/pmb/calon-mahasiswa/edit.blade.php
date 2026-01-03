@extends('layouts.app')

@section('title', 'Edit Calon Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0">Edit Calon Mahasiswa</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pmb.calon-mahasiswa.index') }}">Calon Mahasiswa</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('pmb.calon-mahasiswa.update', $calonMahasiswa->hashid) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Pilih Gelombang & Jalur -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-clipboard-check me-2"></i>Pilihan Pendaftaran
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">No. Pendaftaran</label>
                        <input type="text" class="form-control" value="{{ $calonMahasiswa->no_pendaftaran }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="gelombang_pmb_id" class="form-label">Gelombang PMB <span class="text-danger">*</span></label>
                        <select class="form-select @error('gelombang_pmb_id') is-invalid @enderror" id="gelombang_pmb_id" name="gelombang_pmb_id" required>
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}" {{ old('gelombang_pmb_id', $calonMahasiswa->gelombang_pmb_id) == $gelombang->id ? 'selected' : '' }}>
                                    {{ $gelombang->periodePmb->nama ?? '' }} - {{ $gelombang->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('gelombang_pmb_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="jalur_seleksi_id" class="form-label">Jalur Seleksi <span class="text-danger">*</span></label>
                        <select class="form-select @error('jalur_seleksi_id') is-invalid @enderror" id="jalur_seleksi_id" name="jalur_seleksi_id" required>
                            <option value="">-- Pilih Jalur --</option>
                            @foreach($jalurs as $jalur)
                                <option value="{{ $jalur->id }}" {{ old('jalur_seleksi_id', $calonMahasiswa->jalur_seleksi_id) == $jalur->id ? 'selected' : '' }}>
                                    {{ $jalur->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('jalur_seleksi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="draft" {{ old('status', $calonMahasiswa->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="menunggu_bayar" {{ old('status', $calonMahasiswa->status) == 'menunggu_bayar' ? 'selected' : '' }}>Menunggu Bayar</option>
                            <option value="terdaftar" {{ old('status', $calonMahasiswa->status) == 'terdaftar' ? 'selected' : '' }}>Terdaftar</option>
                            <option value="mengikuti_ujian" {{ old('status', $calonMahasiswa->status) == 'mengikuti_ujian' ? 'selected' : '' }}>Mengikuti Ujian</option>
                            <option value="lulus" {{ old('status', $calonMahasiswa->status) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="tidak_lulus" {{ old('status', $calonMahasiswa->status) == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                            <option value="daftar_ulang" {{ old('status', $calonMahasiswa->status) == 'daftar_ulang' ? 'selected' : '' }}>Daftar Ulang</option>
                            <option value="menjadi_mahasiswa" {{ old('status', $calonMahasiswa->status) == 'menjadi_mahasiswa' ? 'selected' : '' }}>Menjadi Mahasiswa</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="program_studi_id" class="form-label">Program Studi Pilihan 1 <span class="text-danger">*</span></label>
                        <select class="form-select @error('program_studi_id') is-invalid @enderror" id="program_studi_id" name="program_studi_id" required>
                            <option value="">-- Pilih Prodi --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('program_studi_id', $calonMahasiswa->program_studi_id) == $prodi->id ? 'selected' : '' }}>
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
                                <option value="{{ $prodi->id }}" {{ old('program_studi_2_id', $calonMahasiswa->program_studi_2_id) == $prodi->id ? 'selected' : '' }}>
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
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $calonMahasiswa->nama_lengkap) }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik', $calonMahasiswa->nik) }}" maxlength="16">
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nisn" class="form-label">NISN</label>
                        <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" name="nisn" value="{{ old('nisn', $calonMahasiswa->nisn) }}" maxlength="10">
                        @error('nisn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin', $calonMahasiswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $calonMahasiswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $calonMahasiswa->tempat_lahir) }}" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $calonMahasiswa->tanggal_lahir->format('Y-m-d')) }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="agama" class="form-label">Agama</label>
                        <select class="form-select @error('agama') is-invalid @enderror" id="agama" name="agama">
                            <option value="">-- Pilih --</option>
                            <option value="Islam" {{ old('agama', $calonMahasiswa->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama', $calonMahasiswa->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama', $calonMahasiswa->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama', $calonMahasiswa->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama', $calonMahasiswa->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama', $calonMahasiswa->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
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
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $calonMahasiswa->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="no_hp" class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $calonMahasiswa->no_hp) }}" required>
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="no_wa" class="form-label">No. WhatsApp</label>
                        <input type="text" class="form-control @error('no_wa') is-invalid @enderror" id="no_wa" name="no_wa" value="{{ old('no_wa', $calonMahasiswa->no_wa) }}">
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
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2" required>{{ old('alamat', $calonMahasiswa->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="rt" class="form-label">RT</label>
                        <input type="text" class="form-control" id="rt" name="rt" value="{{ old('rt', $calonMahasiswa->rt) }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="rw" class="form-label">RW</label>
                        <input type="text" class="form-control" id="rw" name="rw" value="{{ old('rw', $calonMahasiswa->rw) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kelurahan" class="form-label">Kelurahan/Desa</label>
                        <input type="text" class="form-control" id="kelurahan" name="kelurahan" value="{{ old('kelurahan', $calonMahasiswa->kelurahan) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kecamatan" class="form-label">Kecamatan</label>
                        <input type="text" class="form-control" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $calonMahasiswa->kecamatan) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kabupaten" class="form-label">Kabupaten/Kota</label>
                        <input type="text" class="form-control" id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $calonMahasiswa->kabupaten) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="provinsi" class="form-label">Provinsi</label>
                        <input type="text" class="form-control" id="provinsi" name="provinsi" value="{{ old('provinsi', $calonMahasiswa->provinsi) }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="kode_pos" class="form-label">Kode Pos</label>
                        <input type="text" class="form-control" id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $calonMahasiswa->kode_pos) }}">
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
                    <div class="col-md-6 mb-3">
                        <label for="asal_sekolah" class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('asal_sekolah') is-invalid @enderror" id="asal_sekolah" name="asal_sekolah" value="{{ old('asal_sekolah', $calonMahasiswa->asal_sekolah) }}" required>
                        @error('asal_sekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="npsn_sekolah" class="form-label">NPSN</label>
                        <input type="text" class="form-control" id="npsn_sekolah" name="npsn_sekolah" value="{{ old('npsn_sekolah', $calonMahasiswa->npsn_sekolah) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="jurusan_sekolah" class="form-label">Jurusan</label>
                        <input type="text" class="form-control" id="jurusan_sekolah" name="jurusan_sekolah" value="{{ old('jurusan_sekolah', $calonMahasiswa->jurusan_sekolah) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tahun_lulus" class="form-label">Tahun Lulus <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('tahun_lulus') is-invalid @enderror" id="tahun_lulus" name="tahun_lulus" value="{{ old('tahun_lulus', $calonMahasiswa->tahun_lulus) }}" min="2000" max="{{ date('Y') }}" required>
                        @error('tahun_lulus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nilai_rata_rata" class="form-label">Nilai Rata-rata</label>
                        <input type="number" step="0.01" class="form-control" id="nilai_rata_rata" name="nilai_rata_rata" value="{{ old('nilai_rata_rata', $calonMahasiswa->nilai_rata_rata) }}" min="0" max="100">
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
                        <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah', $calonMahasiswa->nama_ayah) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pekerjaan_ayah" class="form-label">Pekerjaan Ayah</label>
                        <input type="text" class="form-control" id="pekerjaan_ayah" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $calonMahasiswa->pekerjaan_ayah) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="no_hp_ayah" class="form-label">No. HP Ayah</label>
                        <input type="text" class="form-control" id="no_hp_ayah" name="no_hp_ayah" value="{{ old('no_hp_ayah', $calonMahasiswa->no_hp_ayah) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nama_ibu" class="form-label">Nama Ibu</label>
                        <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu', $calonMahasiswa->nama_ibu) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pekerjaan_ibu" class="form-label">Pekerjaan Ibu</label>
                        <input type="text" class="form-control" id="pekerjaan_ibu" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $calonMahasiswa->pekerjaan_ibu) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="no_hp_ibu" class="form-label">No. HP Ibu</label>
                        <input type="text" class="form-control" id="no_hp_ibu" name="no_hp_ibu" value="{{ old('no_hp_ibu', $calonMahasiswa->no_hp_ibu) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="penghasilan_ortu" class="form-label">Penghasilan Orang Tua (per bulan)</label>
                        <input type="number" class="form-control" id="penghasilan_ortu" name="penghasilan_ortu" value="{{ old('penghasilan_ortu', $calonMahasiswa->penghasilan_ortu) }}">
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
                        @if($calonMahasiswa->foto)
                        <div class="mb-3">
                            <label class="form-label">Foto Saat Ini</label><br>
                            <img src="{{ Storage::url($calonMahasiswa->foto) }}" class="rounded" style="max-height: 200px;">
                        </div>
                        @endif
                        <label for="foto" class="form-label">{{ $calonMahasiswa->foto ? 'Ganti Foto' : 'Upload Foto' }}</label>
                        <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengganti.</small>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy me-1"></i> Simpan Perubahan
            </button>
            <a href="{{ route('pmb.calon-mahasiswa.show', $calonMahasiswa->hashid) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
