@extends('layouts.app')

@section('title', 'Tambah Dosen')

@section('content')
<div class="page-title">
    <h4>Tambah Dosen</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Dosen</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<form action="{{ route('dosen.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Data Utama -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>Data Utama
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nidn" class="form-label">NIDN <span class="text-danger">*</span></label>
                                <input type="text" name="nidn" id="nidn" class="form-control @error('nidn') is-invalid @enderror" value="{{ old('nidn') }}" required maxlength="20">
                                @error('nidn')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Password default = NIDN</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap (Tanpa Gelar) <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="gelar_depan" class="form-label">Gelar Depan</label>
                                <input type="text" name="gelar_depan" id="gelar_depan" class="form-control @error('gelar_depan') is-invalid @enderror" value="{{ old('gelar_depan') }}" placeholder="Prof. Dr. Ir.">
                                @error('gelar_depan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="gelar_belakang" class="form-label">Gelar Belakang</label>
                                <input type="text" name="gelar_belakang" id="gelar_belakang" class="form-control @error('gelar_belakang') is-invalid @enderror" value="{{ old('gelar_belakang') }}" placeholder="M.Kom., Ph.D.">
                                @error('gelar_belakang')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select @error('pendidikan_terakhir') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="S1" {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('pendidikan_terakhir') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                                @error('pendidikan_terakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program_studi_id" class="form-label">Program Studi <span class="text-danger">*</span></label>
                                <select name="program_studi_id" id="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    @foreach($programStudi as $ps)
                                    <option value="{{ $ps->id }}" {{ old('program_studi_id') == $ps->id ? 'selected' : '' }}>
                                        {{ $ps->nama }} ({{ $ps->fakultas->nama ?? '-' }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('program_studi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}">
                                @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}">
                                @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telepon" class="form-label">No. Telepon</label>
                                <input type="text" name="telepon" id="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon') }}">
                                @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No. HP</label>
                                <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
                                @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Kepegawaian -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-briefcase me-2"></i>Data Kepegawaian
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
                                <select name="jabatan_fungsional" id="jabatan_fungsional" class="form-select @error('jabatan_fungsional') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="Tenaga Pengajar" {{ old('jabatan_fungsional') == 'Tenaga Pengajar' ? 'selected' : '' }}>Tenaga Pengajar</option>
                                    <option value="Asisten Ahli" {{ old('jabatan_fungsional') == 'Asisten Ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                                    <option value="Lektor" {{ old('jabatan_fungsional') == 'Lektor' ? 'selected' : '' }}>Lektor</option>
                                    <option value="Lektor Kepala" {{ old('jabatan_fungsional') == 'Lektor Kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                                    <option value="Guru Besar" {{ old('jabatan_fungsional') == 'Guru Besar' ? 'selected' : '' }}>Guru Besar</option>
                                </select>
                                @error('jabatan_fungsional')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="golongan" class="form-label">Golongan</label>
                                <select name="golongan" id="golongan" class="form-select @error('golongan') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="III/a" {{ old('golongan') == 'III/a' ? 'selected' : '' }}>III/a - Penata Muda</option>
                                    <option value="III/b" {{ old('golongan') == 'III/b' ? 'selected' : '' }}>III/b - Penata Muda Tk. I</option>
                                    <option value="III/c" {{ old('golongan') == 'III/c' ? 'selected' : '' }}>III/c - Penata</option>
                                    <option value="III/d" {{ old('golongan') == 'III/d' ? 'selected' : '' }}>III/d - Penata Tk. I</option>
                                    <option value="IV/a" {{ old('golongan') == 'IV/a' ? 'selected' : '' }}>IV/a - Pembina</option>
                                    <option value="IV/b" {{ old('golongan') == 'IV/b' ? 'selected' : '' }}>IV/b - Pembina Tk. I</option>
                                    <option value="IV/c" {{ old('golongan') == 'IV/c' ? 'selected' : '' }}>IV/c - Pembina Utama Muda</option>
                                    <option value="IV/d" {{ old('golongan') == 'IV/d' ? 'selected' : '' }}>IV/d - Pembina Utama Madya</option>
                                    <option value="IV/e" {{ old('golongan') == 'IV/e' ? 'selected' : '' }}>IV/e - Pembina Utama</option>
                                </select>
                                @error('golongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bidang_keahlian" class="form-label">Bidang Keahlian</label>
                                <input type="text" name="bidang_keahlian" id="bidang_keahlian" class="form-control @error('bidang_keahlian') is-invalid @enderror" value="{{ old('bidang_keahlian') }}" placeholder="Sistem Informasi, Jaringan Komputer, dll">
                                @error('bidang_keahlian')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rumpun_ilmu" class="form-label">Rumpun Ilmu</label>
                                <input type="text" name="rumpun_ilmu" id="rumpun_ilmu" class="form-control @error('rumpun_ilmu') is-invalid @enderror" value="{{ old('rumpun_ilmu') }}" placeholder="Ilmu Komputer">
                                @error('rumpun_ilmu')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Sertifikasi -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-award me-2"></i>Data Sertifikasi
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="no_sertifikasi_dosen" class="form-label">No. Sertifikasi Dosen</label>
                                <input type="text" name="no_sertifikasi_dosen" id="no_sertifikasi_dosen" class="form-control @error('no_sertifikasi_dosen') is-invalid @enderror" value="{{ old('no_sertifikasi_dosen') }}">
                                @error('no_sertifikasi_dosen')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tahun_sertifikasi" class="form-label">Tahun Sertifikasi</label>
                                <select name="tahun_sertifikasi" id="tahun_sertifikasi" class="form-select @error('tahun_sertifikasi') is-invalid @enderror">
                                    <option value="">-- Pilih Tahun --</option>
                                    @for($year = date('Y'); $year >= 2000; $year--)
                                    <option value="{{ $year }}" {{ old('tahun_sertifikasi') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                                @error('tahun_sertifikasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="no_registrasi_dikti" class="form-label">No. Registrasi DIKTI</label>
                                <input type="text" name="no_registrasi_dikti" id="no_registrasi_dikti" class="form-control @error('no_registrasi_dikti') is-invalid @enderror" value="{{ old('no_registrasi_dikti') }}">
                                @error('no_registrasi_dikti')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Publikasi -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-journal-text me-2"></i>Data Publikasi & Akademik
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sinta_id" class="form-label">SINTA ID</label>
                                <input type="text" name="sinta_id" id="sinta_id" class="form-control @error('sinta_id') is-invalid @enderror" value="{{ old('sinta_id') }}">
                                @error('sinta_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scopus_id" class="form-label">Scopus ID</label>
                                <input type="text" name="scopus_id" id="scopus_id" class="form-control @error('scopus_id') is-invalid @enderror" value="{{ old('scopus_id') }}">
                                @error('scopus_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="google_scholar_id" class="form-label">Google Scholar ID</label>
                                <input type="text" name="google_scholar_id" id="google_scholar_id" class="form-control @error('google_scholar_id') is-invalid @enderror" value="{{ old('google_scholar_id') }}">
                                @error('google_scholar_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="orcid" class="form-label">ORCID iD</label>
                                <input type="text" name="orcid" id="orcid" class="form-control @error('orcid') is-invalid @enderror" value="{{ old('orcid') }}" placeholder="0000-0000-0000-0000">
                                @error('orcid')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alamat Lengkap -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-geo-alt me-2"></i>Alamat Lengkap
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror" placeholder="Nama jalan, nomor rumah, dll">{{ old('alamat') }}</textarea>
                        @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="provinsi_id" class="form-label">Provinsi</label>
                                <select name="provinsi_id" id="provinsi_id" class="form-select @error('provinsi_id') is-invalid @enderror">
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach($provinsi ?? [] as $prov)
                                    <option value="{{ $prov->id }}" {{ old('provinsi_id') == $prov->id ? 'selected' : '' }}>{{ $prov->nama }}</option>
                                    @endforeach
                                </select>
                                @error('provinsi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kabupaten_id" class="form-label">Kabupaten/Kota</label>
                                <select name="kabupaten_id" id="kabupaten_id" class="form-select @error('kabupaten_id') is-invalid @enderror">
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                                @error('kabupaten_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kecamatan_id" class="form-label">Kecamatan</label>
                                <select name="kecamatan_id" id="kecamatan_id" class="form-select @error('kecamatan_id') is-invalid @enderror">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                @error('kecamatan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kelurahan_id" class="form-label">Kelurahan/Desa</label>
                                <select name="kelurahan_id" id="kelurahan_id" class="form-select @error('kelurahan_id') is-invalid @enderror">
                                    <option value="">-- Pilih Kelurahan/Desa --</option>
                                </select>
                                @error('kelurahan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="rt" class="form-label">RT</label>
                                <input type="text" name="rt" id="rt" class="form-control @error('rt') is-invalid @enderror" value="{{ old('rt') }}" maxlength="5" placeholder="001">
                                @error('rt')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="rw" class="form-label">RW</label>
                                <input type="text" name="rw" id="rw" class="form-control @error('rw') is-invalid @enderror" value="{{ old('rw') }}" maxlength="5" placeholder="001">
                                @error('rw')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="kode_pos" class="form-label">Kode Pos</label>
                                <input type="text" name="kode_pos" id="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror" value="{{ old('kode_pos') }}" maxlength="10" placeholder="12345">
                                @error('kode_pos')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Finansial -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bank me-2"></i>Data Finansial
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_npwp" class="form-label">NPWP</label>
                                <input type="text" name="no_npwp" id="no_npwp" class="form-control @error('no_npwp') is-invalid @enderror" value="{{ old('no_npwp') }}" placeholder="00.000.000.0-000.000">
                                @error('no_npwp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_bank" class="form-label">Nama Bank</label>
                                <select name="nama_bank" id="nama_bank" class="form-select @error('nama_bank') is-invalid @enderror">
                                    <option value="">-- Pilih Bank --</option>
                                    <option value="BCA" {{ old('nama_bank') == 'BCA' ? 'selected' : '' }}>BCA</option>
                                    <option value="BNI" {{ old('nama_bank') == 'BNI' ? 'selected' : '' }}>BNI</option>
                                    <option value="BRI" {{ old('nama_bank') == 'BRI' ? 'selected' : '' }}>BRI</option>
                                    <option value="Mandiri" {{ old('nama_bank') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                    <option value="BSI" {{ old('nama_bank') == 'BSI' ? 'selected' : '' }}>BSI</option>
                                    <option value="CIMB Niaga" {{ old('nama_bank') == 'CIMB Niaga' ? 'selected' : '' }}>CIMB Niaga</option>
                                    <option value="BTN" {{ old('nama_bank') == 'BTN' ? 'selected' : '' }}>BTN</option>
                                    <option value="Lainnya" {{ old('nama_bank') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('nama_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_rekening" class="form-label">No. Rekening</label>
                                <input type="text" name="no_rekening" id="no_rekening" class="form-control @error('no_rekening') is-invalid @enderror" value="{{ old('no_rekening') }}">
                                @error('no_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="atas_nama_rekening" class="form-label">Atas Nama</label>
                                <input type="text" name="atas_nama_rekening" id="atas_nama_rekening" class="form-control @error('atas_nama_rekening') is-invalid @enderror" value="{{ old('atas_nama_rekening') }}">
                                @error('atas_nama_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_bpjs_kesehatan" class="form-label">No. BPJS Kesehatan</label>
                                <input type="text" name="no_bpjs_kesehatan" id="no_bpjs_kesehatan" class="form-control @error('no_bpjs_kesehatan') is-invalid @enderror" value="{{ old('no_bpjs_kesehatan') }}">
                                @error('no_bpjs_kesehatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_bpjs_ketenagakerjaan" class="form-label">No. BPJS Ketenagakerjaan</label>
                                <input type="text" name="no_bpjs_ketenagakerjaan" id="no_bpjs_ketenagakerjaan" class="form-control @error('no_bpjs_ketenagakerjaan') is-invalid @enderror" value="{{ old('no_bpjs_ketenagakerjaan') }}">
                                @error('no_bpjs_ketenagakerjaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Submit -->
            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <a href="{{ route('dosen.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Info Card -->
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h6><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                    <ul class="small text-muted mb-0">
                        <li>NIDN akan digunakan sebagai password default untuk login</li>
                        <li>Email harus unik dan akan digunakan untuk login</li>
                        <li>Field bertanda <span class="text-danger">*</span> wajib diisi</li>
                    </ul>
                </div>
            </div>
            
            <!-- Foto -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-image me-2"></i>Foto Dosen
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="foto" class="form-label">Upload Foto</label>
                        <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                        @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                    </div>
                    <div id="previewFoto" class="text-center" style="display: none;">
                        <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-toggle-on me-2"></i>Status
                </div>
                <div class="card-body">
                    <div class="mb-0">
                        <label for="status" class="form-label">Status Dosen</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Cuti" {{ old('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Preview foto
    document.getElementById('foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFoto').style.display = 'block';
                document.getElementById('previewFoto').querySelector('img').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Cascading dropdown untuk wilayah
    document.getElementById('provinsi_id').addEventListener('change', async function() {
        const kabupatenSelect = document.getElementById('kabupaten_id');
        const kecamatanSelect = document.getElementById('kecamatan_id');
        const kelurahanSelect = document.getElementById('kelurahan_id');
        
        kabupatenSelect.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
        
        if (this.value) {
            const response = await fetch(`/api/kabupaten?provinsi_id=${this.value}`);
            const data = await response.json();
            data.forEach(item => {
                kabupatenSelect.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
            });
        }
    });
    
    document.getElementById('kabupaten_id').addEventListener('change', async function() {
        const kecamatanSelect = document.getElementById('kecamatan_id');
        const kelurahanSelect = document.getElementById('kelurahan_id');
        
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
        
        if (this.value) {
            const response = await fetch(`/api/kecamatan?kabupaten_id=${this.value}`);
            const data = await response.json();
            data.forEach(item => {
                kecamatanSelect.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
            });
        }
    });
    
    document.getElementById('kecamatan_id').addEventListener('change', async function() {
        const kelurahanSelect = document.getElementById('kelurahan_id');
        
        kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
        
        if (this.value) {
            const response = await fetch(`/api/kelurahan?kecamatan_id=${this.value}`);
            const data = await response.json();
            data.forEach(item => {
                kelurahanSelect.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
            });
        }
    });
</script>
@endpush
