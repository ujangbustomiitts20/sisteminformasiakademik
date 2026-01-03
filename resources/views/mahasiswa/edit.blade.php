@extends('layouts.app')

@section('title', 'Edit Mahasiswa')

@section('content')
<div class="page-title">
    <h4>Edit Mahasiswa</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mahasiswa.index') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<form method="POST" action="{{ route('mahasiswa.update', $mahasiswa) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Kolom Kiri -->
        <div class="col-lg-8">
            <!-- Data Pribadi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Data Pribadi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NIM <span class="text-danger">*</span></label>
                            <input type="text" name="nim" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim', $mahasiswa->nim) }}" required>
                            @error('nim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $mahasiswa->nama) }}" required>
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $mahasiswa->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="L" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $mahasiswa->tanggal_lahir?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Agama</label>
                            <select name="agama" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                <option value="{{ $agama }}" {{ old('agama', $mahasiswa->agama) == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" value="{{ old('nik', $mahasiswa->nik) }}" maxlength="16">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">No. KK</label>
                            <input type="text" name="no_kk" class="form-control" value="{{ old('no_kk', $mahasiswa->no_kk) }}" maxlength="16">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Kewarganegaraan</label>
                            <select name="kewarganegaraan" class="form-select">
                                <option value="WNI" {{ old('kewarganegaraan', $mahasiswa->kewarganegaraan) == 'WNI' ? 'selected' : '' }}>WNI</option>
                                <option value="WNA" {{ old('kewarganegaraan', $mahasiswa->kewarganegaraan) == 'WNA' ? 'selected' : '' }}>WNA</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="form-label">Gol.</label>
                            <select name="golongan_darah" class="form-select">
                                <option value="">-</option>
                                @foreach(['A', 'B', 'AB', 'O'] as $gol)
                                <option value="{{ $gol }}" {{ old('golongan_darah', $mahasiswa->golongan_darah) == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $mahasiswa->telepon) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $mahasiswa->no_hp) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat Domisili -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Alamat Domisili</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Provinsi</label>
                            <select name="provinsi_id" id="provinsi_id" class="form-select" data-current="{{ old('provinsi_id', $mahasiswa->provinsi_id) }}">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kabupaten/Kota</label>
                            <select name="kabupaten_id" id="kabupaten_id" class="form-select" data-current="{{ old('kabupaten_id', $mahasiswa->kabupaten_id) }}" disabled>
                                <option value="">-- Pilih Kabupaten --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kecamatan</label>
                            <select name="kecamatan_id" id="kecamatan_id" class="form-select" data-current="{{ old('kecamatan_id', $mahasiswa->kecamatan_id) }}" disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelurahan/Desa</label>
                            <select name="kelurahan_id" id="kelurahan_id" class="form-select" data-current="{{ old('kelurahan_id', $mahasiswa->kelurahan_id) }}" disabled>
                                <option value="">-- Pilih Kelurahan --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="form-label">RT</label>
                            <input type="text" name="rt" class="form-control" value="{{ old('rt', $mahasiswa->rt) }}" maxlength="5" placeholder="001">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">RW</label>
                            <input type="text" name="rw" class="form-control" value="{{ old('rw', $mahasiswa->rw) }}" maxlength="5" placeholder="001">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="kode_pos" id="kode_pos" class="form-control" value="{{ old('kode_pos', $mahasiswa->kode_pos) }}" maxlength="10">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat (Jalan/Dusun/dll)</label>
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $mahasiswa->alamat) }}" placeholder="Jl. Contoh No. 123">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Akademik -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Data Akademik</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                            <select name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                                @foreach($programStudi as $ps)
                                <option value="{{ $ps->id }}" {{ old('program_studi_id', $mahasiswa->program_studi_id) == $ps->id ? 'selected' : '' }}>
                                    {{ $ps->nama }} ({{ $ps->fakultas->nama ?? '-' }})
                                </option>
                                @endforeach
                            </select>
                            @error('program_studi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Angkatan <span class="text-danger">*</span></label>
                            <input type="number" name="angkatan" class="form-control @error('angkatan') is-invalid @enderror" value="{{ old('angkatan', $mahasiswa->angkatan) }}" required>
                            @error('angkatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jalur Masuk</label>
                            <select name="jalur_masuk" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach(['SNMPTN', 'SBMPTN', 'Mandiri', 'Pindahan', 'Beasiswa'] as $jalur)
                                <option value="{{ $jalur }}" {{ old('jalur_masuk', $mahasiswa->jalur_masuk) == $jalur ? 'selected' : '' }}>{{ $jalur }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Semester Aktif <span class="text-danger">*</span></label>
                            <input type="number" name="semester_aktif" class="form-control" value="{{ old('semester_aktif', $mahasiswa->semester_aktif) }}" min="1" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="Aktif" {{ old('status', $mahasiswa->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Cuti" {{ old('status', $mahasiswa->status) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                <option value="Lulus" {{ old('status', $mahasiswa->status) == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                <option value="DO" {{ old('status', $mahasiswa->status) == 'DO' ? 'selected' : '' }}>DO</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dosen Wali</label>
                            <select name="dosen_wali_id" class="form-select">
                                <option value="">-- Pilih Dosen Wali --</option>
                                @foreach($dosen as $d)
                                <option value="{{ $d->id }}" {{ old('dosen_wali_id', $mahasiswa->dosen_wali_id) == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }} ({{ $d->nidn }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3"><i class="bi bi-building me-2"></i>Asal Sekolah</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Provinsi Sekolah</label>
                            <select id="provinsi_sekolah" class="form-select">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kabupaten Sekolah</label>
                            <select id="kabupaten_sekolah" class="form-select" disabled>
                                <option value="">-- Pilih Kabupaten --</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nama Sekolah</label>
                            <select name="sekolah_id" id="sekolah_id" class="form-select" data-current="{{ old('sekolah_id', $mahasiswa->sekolah_id) }}">
                                <option value="">-- Pilih Sekolah --</option>
                                @if($mahasiswa->sekolah)
                                <option value="{{ $mahasiswa->sekolah_id }}" selected>{{ $mahasiswa->sekolah->nama }} - {{ $mahasiswa->sekolah->kabupaten->nama ?? '' }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Atau Ketik Manual</label>
                            <input type="text" name="asal_sekolah" id="asal_sekolah_manual" class="form-control" value="{{ old('asal_sekolah', $mahasiswa->asal_sekolah) }}" placeholder="Jika tidak ada di daftar">
                            <small class="text-muted">Isi jika sekolah tidak ada di daftar</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jurusan Asal</label>
                            <input type="text" name="jurusan_asal" class="form-control" value="{{ old('jurusan_asal', $mahasiswa->jurusan_asal) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun Lulus</label>
                            <input type="number" name="tahun_lulus_sekolah" class="form-control" value="{{ old('tahun_lulus_sekolah', $mahasiswa->tahun_lulus_sekolah) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Ijazah SMA</label>
                            <input type="text" name="no_ijazah_sma" class="form-control" value="{{ old('no_ijazah_sma', $mahasiswa->no_ijazah_sma) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nilai UN</label>
                            <input type="number" name="nilai_un" class="form-control" value="{{ old('nilai_un', $mahasiswa->nilai_un) }}" step="0.01" min="0" max="100">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>Data Orang Tua</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Data Ayah</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah', $mahasiswa->nama_ayah) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NIK Ayah</label>
                                <input type="text" name="nik_ayah" class="form-control" value="{{ old('nik_ayah', $mahasiswa->nik_ayah) }}" maxlength="16">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah', $mahasiswa->pekerjaan_ayah) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pendidikan Ayah</label>
                                <select name="pendidikan_ayah" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'] as $pend)
                                    <option value="{{ $pend }}" {{ old('pendidikan_ayah', $mahasiswa->pendidikan_ayah) == $pend ? 'selected' : '' }}>{{ $pend }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Data Ibu</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu', $mahasiswa->nama_ibu) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NIK Ibu</label>
                                <input type="text" name="nik_ibu" class="form-control" value="{{ old('nik_ibu', $mahasiswa->nik_ibu) }}" maxlength="16">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu', $mahasiswa->pekerjaan_ibu) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pendidikan Ibu</label>
                                <select name="pendidikan_ibu" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'] as $pend)
                                    <option value="{{ $pend }}" {{ old('pendidikan_ibu', $mahasiswa->pendidikan_ibu) == $pend ? 'selected' : '' }}>{{ $pend }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. HP Orang Tua</label>
                            <input type="text" name="no_hp_ortu" class="form-control" value="{{ old('no_hp_ortu', $mahasiswa->no_hp_ortu) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email Orang Tua</label>
                            <input type="email" name="email_ortu" class="form-control" value="{{ old('email_ortu', $mahasiswa->email_ortu) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Penghasilan Orang Tua</label>
                            <select name="penghasilan_ortu" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach(['< 1 Juta', '1 - 3 Juta', '3 - 5 Juta', '5 - 10 Juta', '> 10 Juta'] as $penghasilan)
                                <option value="{{ $penghasilan }}" {{ old('penghasilan_ortu', $mahasiswa->penghasilan_ortu) == $penghasilan ? 'selected' : '' }}>{{ $penghasilan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Alamat Orang Tua -->
                    <h6 class="text-muted mt-3 mb-3">Alamat Orang Tua</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Provinsi</label>
                            <select name="provinsi_ortu_id" id="provinsi_ortu_id" class="form-select" data-current="{{ old('provinsi_ortu_id', $mahasiswa->provinsi_ortu_id) }}">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kabupaten/Kota</label>
                            <select name="kabupaten_ortu_id" id="kabupaten_ortu_id" class="form-select" data-current="{{ old('kabupaten_ortu_id', $mahasiswa->kabupaten_ortu_id) }}" disabled>
                                <option value="">-- Pilih Kabupaten --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kecamatan</label>
                            <select name="kecamatan_ortu_id" id="kecamatan_ortu_id" class="form-select" data-current="{{ old('kecamatan_ortu_id', $mahasiswa->kecamatan_ortu_id) }}" disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelurahan/Desa</label>
                            <select name="kelurahan_ortu_id" id="kelurahan_ortu_id" class="form-select" data-current="{{ old('kelurahan_ortu_id', $mahasiswa->kelurahan_ortu_id) }}" disabled>
                                <option value="">-- Pilih Kelurahan --</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap (Jalan/RT/RW)</label>
                        <textarea name="alamat_ortu" class="form-control" rows="2">{{ old('alamat_ortu', $mahasiswa->alamat_ortu) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Data Wali -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Data Wali (Jika berbeda dari Orang Tua)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Wali</label>
                            <input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali', $mahasiswa->nama_wali) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hubungan dengan Wali</label>
                            <select name="hubungan_wali" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach(['Kakak', 'Paman', 'Bibi', 'Kakek', 'Nenek', 'Saudara Lainnya', 'Lainnya'] as $hub)
                                <option value="{{ $hub }}" {{ old('hubungan_wali', $mahasiswa->hubungan_wali) == $hub ? 'selected' : '' }}>{{ $hub }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pekerjaan Wali</label>
                            <input type="text" name="pekerjaan_wali" class="form-control" value="{{ old('pekerjaan_wali', $mahasiswa->pekerjaan_wali) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP Wali</label>
                            <input type="text" name="no_hp_wali" class="form-control" value="{{ old('no_hp_wali', $mahasiswa->no_hp_wali) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Wali</label>
                        <textarea name="alamat_wali" class="form-control" rows="2">{{ old('alamat_wali', $mahasiswa->alamat_wali) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Data Finansial -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Data Finansial & Beasiswa</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. Rekening</label>
                            <input type="text" name="no_rekening" class="form-control" value="{{ old('no_rekening', $mahasiswa->no_rekening) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nama Bank</label>
                            <select name="nama_bank" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach(['BRI', 'BNI', 'Mandiri', 'BCA', 'BSI', 'BTN', 'CIMB Niaga', 'Bank Jatim', 'Bank Jateng', 'Lainnya'] as $bank)
                                <option value="{{ $bank }}" {{ old('nama_bank', $mahasiswa->nama_bank) == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Atas Nama Rekening</label>
                            <input type="text" name="atas_nama_rekening" class="form-control" value="{{ old('atas_nama_rekening', $mahasiswa->atas_nama_rekening) }}">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Penerima KIP?</label>
                            <select name="penerima_kip" id="penerima_kip" class="form-select">
                                <option value="0" {{ old('penerima_kip', $mahasiswa->penerima_kip) == '0' || !$mahasiswa->penerima_kip ? 'selected' : '' }}>Tidak</option>
                                <option value="1" {{ old('penerima_kip', $mahasiswa->penerima_kip) == '1' || $mahasiswa->penerima_kip ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3" id="kip_container" style="{{ old('penerima_kip', $mahasiswa->penerima_kip) ? '' : 'display: none;' }}">
                            <label class="form-label">No. KIP</label>
                            <input type="text" name="no_kip" class="form-control" value="{{ old('no_kip', $mahasiswa->no_kip) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan - Foto -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-camera me-2"></i>Foto Mahasiswa</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($mahasiswa->foto)
                        <img id="preview_foto" src="{{ asset('storage/' . $mahasiswa->foto) }}" 
                             alt="Foto {{ $mahasiswa->nama }}" class="img-thumbnail" 
                             style="width: 200px; height: 250px; object-fit: cover;">
                        @else
                        <img id="preview_foto" src="{{ asset('images/default-avatar.svg') }}" 
                             alt="Default Foto" class="img-thumbnail" 
                             style="width: 200px; height: 250px; object-fit: cover;">
                        @endif
                    </div>
                    <div class="mb-3">
                        <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/jpg">
                        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Format: JPG, PNG. Maks: 2MB. Kosongkan jika tidak ingin mengubah foto.</small>
                    </div>
                    @if($mahasiswa->foto)
                    <div class="form-check">
                        <input type="checkbox" name="hapus_foto" id="hapus_foto" class="form-check-input" value="1">
                        <label for="hapus_foto" class="form-check-label text-danger">Hapus foto</label>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update
                        </button>
                        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x me-1"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // FOTO PREVIEW
    // ==========================================
    const fotoInput = document.getElementById('foto');
    const previewFoto = document.getElementById('preview_foto');
    const hapusFoto = document.getElementById('hapus_foto');

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 2MB.');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                previewFoto.src = e.target.result;
            }
            reader.readAsDataURL(file);
            
            if (hapusFoto) {
                hapusFoto.checked = false;
            }
        }
    });

    // ==========================================
    // TOGGLE KIP
    // ==========================================
    const penerimaKip = document.getElementById('penerima_kip');
    const kipContainer = document.getElementById('kip_container');

    penerimaKip.addEventListener('change', function() {
        kipContainer.style.display = this.value == '1' ? '' : 'none';
    });

    // ==========================================
    // WILAYAH FUNCTIONS
    // ==========================================
    async function loadProvinsi(selectElement, currentValue = null) {
        try {
            const response = await fetch('{{ route("api.provinsi") }}');
            const data = await response.json();
            selectElement.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
            data.forEach(item => {
                const selected = currentValue == item.id ? 'selected' : '';
                selectElement.innerHTML += `<option value="${item.id}" ${selected}>${item.nama}</option>`;
            });
            if (currentValue) selectElement.dispatchEvent(new Event('change'));
        } catch (error) {
            console.error('Error loading provinsi:', error);
        }
    }

    async function loadKabupaten(provinsiId, selectElement, currentValue = null) {
        if (!provinsiId) {
            selectElement.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
            selectElement.disabled = true;
            return;
        }
        try {
            const response = await fetch(`{{ route("api.kabupaten") }}?provinsi_id=${provinsiId}`);
            const data = await response.json();
            selectElement.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
            data.forEach(item => {
                const selected = currentValue == item.id ? 'selected' : '';
                selectElement.innerHTML += `<option value="${item.id}" ${selected}>${item.nama}</option>`;
            });
            selectElement.disabled = false;
            if (currentValue) selectElement.dispatchEvent(new Event('change'));
        } catch (error) {
            console.error('Error loading kabupaten:', error);
        }
    }

    async function loadKecamatan(kabupatenId, selectElement, currentValue = null) {
        if (!kabupatenId) {
            selectElement.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            selectElement.disabled = true;
            return;
        }
        try {
            const response = await fetch(`{{ route("api.kecamatan") }}?kabupaten_id=${kabupatenId}`);
            const data = await response.json();
            selectElement.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            data.forEach(item => {
                const selected = currentValue == item.id ? 'selected' : '';
                selectElement.innerHTML += `<option value="${item.id}" ${selected}>${item.nama}</option>`;
            });
            selectElement.disabled = false;
            if (currentValue) selectElement.dispatchEvent(new Event('change'));
        } catch (error) {
            console.error('Error loading kecamatan:', error);
        }
    }

    async function loadKelurahan(kecamatanId, selectElement, currentValue = null, kodePosEl = null) {
        if (!kecamatanId) {
            selectElement.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
            selectElement.disabled = true;
            return;
        }
        try {
            const response = await fetch(`{{ route("api.kelurahan") }}?kecamatan_id=${kecamatanId}`);
            const data = await response.json();
            selectElement.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
            data.forEach(item => {
                const selected = currentValue == item.id ? 'selected' : '';
                selectElement.innerHTML += `<option value="${item.id}" data-kodepos="${item.kode_pos || ''}" ${selected}>${item.nama}</option>`;
            });
            selectElement.disabled = false;
            
            if (kodePosEl) {
                selectElement.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    const kp = opt.getAttribute('data-kodepos');
                    if (kp && !kodePosEl.value) kodePosEl.value = kp;
                });
            }
        } catch (error) {
            console.error('Error loading kelurahan:', error);
        }
    }

    // ==========================================
    // ALAMAT MAHASISWA
    // ==========================================
    const provinsiSelect = document.getElementById('provinsi_id');
    const kabupatenSelect = document.getElementById('kabupaten_id');
    const kecamatanSelect = document.getElementById('kecamatan_id');
    const kelurahanSelect = document.getElementById('kelurahan_id');
    const kodePosInput = document.getElementById('kode_pos');

    loadProvinsi(provinsiSelect, provinsiSelect.dataset.current);
    
    provinsiSelect.addEventListener('change', function() {
        loadKabupaten(this.value, kabupatenSelect, kabupatenSelect.dataset.current);
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kecamatanSelect.disabled = true;
        kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        kelurahanSelect.disabled = true;
    });
    
    kabupatenSelect.addEventListener('change', function() {
        loadKecamatan(this.value, kecamatanSelect, kecamatanSelect.dataset.current);
        kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        kelurahanSelect.disabled = true;
    });
    
    kecamatanSelect.addEventListener('change', function() {
        loadKelurahan(this.value, kelurahanSelect, kelurahanSelect.dataset.current, kodePosInput);
    });

    // ==========================================
    // ALAMAT ORANG TUA
    // ==========================================
    const provinsiOrtuSelect = document.getElementById('provinsi_ortu_id');
    const kabupatenOrtuSelect = document.getElementById('kabupaten_ortu_id');
    const kecamatanOrtuSelect = document.getElementById('kecamatan_ortu_id');
    const kelurahanOrtuSelect = document.getElementById('kelurahan_ortu_id');

    loadProvinsi(provinsiOrtuSelect, provinsiOrtuSelect.dataset.current);
    
    provinsiOrtuSelect.addEventListener('change', function() {
        loadKabupaten(this.value, kabupatenOrtuSelect, kabupatenOrtuSelect.dataset.current);
        kecamatanOrtuSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kecamatanOrtuSelect.disabled = true;
        kelurahanOrtuSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        kelurahanOrtuSelect.disabled = true;
    });
    
    kabupatenOrtuSelect.addEventListener('change', function() {
        loadKecamatan(this.value, kecamatanOrtuSelect, kecamatanOrtuSelect.dataset.current);
        kelurahanOrtuSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        kelurahanOrtuSelect.disabled = true;
    });
    
    kecamatanOrtuSelect.addEventListener('change', function() {
        loadKelurahan(this.value, kelurahanOrtuSelect, kelurahanOrtuSelect.dataset.current);
    });

    // ==========================================
    // SEKOLAH DROPDOWN
    // ==========================================
    const provinsiSekolahSelect = document.getElementById('provinsi_sekolah');
    const kabupatenSekolahSelect = document.getElementById('kabupaten_sekolah');
    const sekolahSelect = document.getElementById('sekolah_id');
    const asalSekolahManual = document.getElementById('asal_sekolah_manual');

    loadProvinsi(provinsiSekolahSelect);
    
    provinsiSekolahSelect.addEventListener('change', function() {
        loadKabupaten(this.value, kabupatenSekolahSelect);
        // Keep existing selection if any
        const currentSekolah = sekolahSelect.value;
        if (!currentSekolah) {
            sekolahSelect.innerHTML = '<option value="">-- Pilih Sekolah --</option>';
        }
    });
    
    kabupatenSekolahSelect.addEventListener('change', async function() {
        const kabupatenId = this.value;
        if (!kabupatenId) {
            // Keep existing selection if any
            const currentSekolah = sekolahSelect.value;
            if (!currentSekolah) {
                sekolahSelect.innerHTML = '<option value="">-- Pilih Sekolah --</option>';
            }
            return;
        }
        try {
            const response = await fetch(`{{ route("api.sekolah") }}?kabupaten_id=${kabupatenId}`);
            const data = await response.json();
            const currentSekolah = sekolahSelect.dataset.current;
            sekolahSelect.innerHTML = '<option value="">-- Pilih Sekolah --</option>';
            data.forEach(item => {
                const selected = currentSekolah == item.id ? 'selected' : '';
                const kab = item.kabupaten ? item.kabupaten.nama : '';
                sekolahSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.nama} (${item.jenjang}) - ${kab}</option>`;
            });
        } catch (error) {
            console.error('Error loading sekolah:', error);
        }
    });

    // Mutual exclusion between sekolah dropdown and manual input
    sekolahSelect.addEventListener('change', function() {
        if (this.value) asalSekolahManual.value = '';
    });

    asalSekolahManual.addEventListener('input', function() {
        if (this.value) sekolahSelect.value = '';
    });
});
</script>
@endpush
