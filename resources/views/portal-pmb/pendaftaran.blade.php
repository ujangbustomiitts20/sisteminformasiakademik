@extends('portal-pmb.layouts.main')

@section('title', 'Pendaftaran Online')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Pendaftaran</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Pendaftaran Online</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Daftar sekarang dan wujudkan impianmu menjadi mahasiswa di kampus kami</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if($gelombangAktif)
        <!-- Info Gelombang Aktif -->
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" data-aos="fade-up">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                    <i class="bi bi-calendar-check text-success fs-3"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">{{ $gelombangAktif->nama }} Sedang Dibuka!</h5>
                    <p class="mb-0">Periode Pendaftaran: {{ $gelombangAktif->tanggal_mulai_daftar->format('d M Y') }} - {{ $gelombangAktif->tanggal_selesai_daftar->format('d F Y') }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4" data-aos="fade-up">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                    <i class="bi bi-info-circle text-warning fs-3"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Pendaftaran Belum Dibuka</h5>
                    <p class="mb-0">Saat ini tidak ada gelombang pendaftaran yang aktif. Silakan cek kembali nanti.</p>
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4">
            <!-- Form Pendaftaran -->
            <div class="col-lg-8">
                <div class="card card-pmb" data-aos="fade-up">
                    <div class="card-body p-4 p-lg-5">
                        <h4 class="fw-bold mb-4">Formulir Pendaftaran</h4>
                        
                        <form action="{{ route('portal-pmb.pendaftaran.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Data Pribadi -->
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="bi bi-person me-2"></i>Data Pribadi
                            </h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" maxlength="16" required>
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}" required>
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}" required>
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Agama <span class="text-danger">*</span></label>
                                    <select name="agama" class="form-select @error('agama') is-invalid @enderror" required>
                                        <option value="">Pilih Agama</option>
                                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ old('agama') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                        @endforeach
                                    </select>
                                    @error('agama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Kontak -->
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="bi bi-telephone me-2"></i>Informasi Kontak
                            </h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. HP/WhatsApp <span class="text-danger">*</span></label>
                                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" required>
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Pendidikan -->
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="bi bi-mortarboard me-2"></i>Riwayat Pendidikan
                            </h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Asal Sekolah <span class="text-danger">*</span></label>
                                    <input type="text" name="asal_sekolah" class="form-control @error('asal_sekolah') is-invalid @enderror" value="{{ old('asal_sekolah') }}" required>
                                    @error('asal_sekolah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tahun Lulus <span class="text-danger">*</span></label>
                                    <input type="number" name="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus') }}" min="2000" max="{{ date('Y') }}" required>
                                    @error('tahun_lulus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NISN</label>
                                    <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}" maxlength="10">
                                    @error('nisn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nilai Rata-rata Ijazah</label>
                                    <input type="number" step="0.01" name="nilai_rata_rata" class="form-control @error('nilai_rata_rata') is-invalid @enderror" value="{{ old('nilai_rata_rata') }}" min="0" max="100">
                                    @error('nilai_rata_rata')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Pilihan Program -->
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="bi bi-bookmark-star me-2"></i>Pilihan Program Studi
                            </h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Jalur Seleksi <span class="text-danger">*</span></label>
                                    <select name="jalur_seleksi_id" class="form-select @error('jalur_seleksi_id') is-invalid @enderror" required>
                                        <option value="">Pilih Jalur Seleksi</option>
                                        @foreach($jalurSeleksi as $jalur)
                                        <option value="{{ $jalur->id }}" {{ old('jalur_seleksi_id') == $jalur->id ? 'selected' : '' }}>{{ $jalur->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('jalur_seleksi_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Program Studi Pilihan 1 <span class="text-danger">*</span></label>
                                    <select name="prodi_pilihan_1" class="form-select @error('prodi_pilihan_1') is-invalid @enderror" required>
                                        <option value="">Pilih Program Studi</option>
                                        @foreach($programStudi as $prodi)
                                        <option value="{{ $prodi->id }}" {{ old('prodi_pilihan_1') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }} ({{ $prodi->jenjang }})</option>
                                        @endforeach
                                    </select>
                                    @error('prodi_pilihan_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Program Studi Pilihan 2</label>
                                    <select name="prodi_pilihan_2" class="form-select @error('prodi_pilihan_2') is-invalid @enderror">
                                        <option value="">Pilih Program Studi</option>
                                        @foreach($programStudi as $prodi)
                                        <option value="{{ $prodi->id }}" {{ old('prodi_pilihan_2') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }} ({{ $prodi->jenjang }})</option>
                                        @endforeach
                                    </select>
                                    @error('prodi_pilihan_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Upload Dokumen -->
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i>Upload Dokumen
                            </h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Pas Foto 3x4 <span class="text-danger">*</span></label>
                                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*" required>
                                    <small class="text-muted">Format: JPG/PNG, maks 2MB</small>
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Scan KTP/KK <span class="text-danger">*</span></label>
                                    <input type="file" name="dokumen_ktp" class="form-control @error('dokumen_ktp') is-invalid @enderror" accept=".pdf,image/*" required>
                                    <small class="text-muted">Format: PDF/JPG/PNG, maks 2MB</small>
                                    @error('dokumen_ktp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Scan Ijazah/SKL</label>
                                    <input type="file" name="dokumen_ijazah" class="form-control @error('dokumen_ijazah') is-invalid @enderror" accept=".pdf,image/*">
                                    <small class="text-muted">Format: PDF/JPG/PNG, maks 2MB</small>
                                    @error('dokumen_ijazah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="agree" id="agree" required>
                                <label class="form-check-label" for="agree">
                                    Saya menyatakan bahwa data yang saya isi adalah benar dan saya bersedia mematuhi seluruh peraturan yang berlaku.
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100" {{ !$gelombangAktif ? 'disabled' : '' }}>
                                <i class="bi bi-send me-2"></i>Kirim Pendaftaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <!-- Alur Pendaftaran -->
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-signpost-2 text-primary me-2"></i>Alur Pendaftaran</h5>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-badge bg-primary">1</div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Isi Formulir</h6>
                                    <small class="text-muted">Lengkapi data pendaftaran</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-primary">2</div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Verifikasi Data</h6>
                                    <small class="text-muted">Data akan diverifikasi oleh panitia</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-primary">3</div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Pembayaran</h6>
                                    <small class="text-muted">Lakukan pembayaran biaya pendaftaran</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-primary">4</div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Cetak Kartu Peserta</h6>
                                    <small class="text-muted">Download kartu peserta ujian</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-primary">5</div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Ikuti Seleksi</h6>
                                    <small class="text-muted">Ikuti tes sesuai jadwal</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-success">6</div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Pengumuman</h6>
                                    <small class="text-muted">Cek hasil seleksi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Info Biaya -->
                @if($biaya && $biaya->count() > 0)
                <div class="card card-pmb mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-cash-coin text-primary me-2"></i>Biaya Pendaftaran</h5>
                        @php $firstBiaya = $biaya->first(); @endphp
                        @if($firstBiaya)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-0">Biaya Formulir</h6>
                                <small class="text-muted">Pendaftaran</small>
                            </div>
                            <span class="fw-bold text-primary">Rp {{ number_format($firstBiaya->biaya_formulir, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-0">Biaya Ujian</h6>
                                <small class="text-muted">Seleksi</small>
                            </div>
                            <span class="fw-bold text-primary">Rp {{ number_format($firstBiaya->biaya_ujian, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold">Total Biaya</h6>
                            </div>
                            <span class="fw-bold text-success fs-5">Rp {{ number_format($firstBiaya->total_biaya, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Kontak -->
                @if($kontak && $kontak->count() > 0)
                @php
                    $whatsappKontak = $kontak->where('type', 'whatsapp')->first();
                    $emailKontak = $kontak->where('type', 'email')->first();
                    $teleponKontak = $kontak->where('type', 'phone')->first();
                @endphp
                <div class="card card-pmb" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-headset text-primary me-2"></i>Butuh Bantuan?</h5>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-whatsapp text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">WhatsApp</small>
                                <p class="mb-0 fw-semibold">{{ $whatsappKontak->value ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-envelope text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Email</small>
                                <p class="mb-0 fw-semibold">{{ $emailKontak->value ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-telephone text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Telepon</small>
                                <p class="mb-0 fw-semibold">{{ $teleponKontak->value ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
