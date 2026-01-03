@extends('portal-pmb.layouts.main')

@section('title', 'Alur Pendaftaran')

@section('content')
<!-- Hero Section -->
<section class="py-5 bg-primary text-white">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="fw-bold mb-3" data-aos="fade-up">Alur Pendaftaran</h1>
                <p class="lead opacity-90" data-aos="fade-up" data-aos-delay="100">
                    Ikuti langkah-langkah berikut untuk mendaftar sebagai mahasiswa baru di kampus kami.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0" data-aos="fade-up" data-aos-delay="200">
                <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-light btn-lg px-4">
                    <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Steps Section -->
<section class="py-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Step 1 -->
                <div class="d-flex mb-5" data-aos="fade-up">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">1</span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h4 class="fw-bold mb-2">Registrasi Akun</h4>
                        <p class="text-muted mb-3">{{ $konten['alur_step_1'] ?? 'Buat akun di website PMB dengan menggunakan email aktif. Anda akan menerima link verifikasi melalui email.' }}</p>
                        <div class="bg-light rounded-3 p-3">
                            <small class="text-muted">
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                <strong>Tips:</strong> Gunakan email yang sering Anda akses untuk memudahkan komunikasi.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="d-flex mb-5" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">2</span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h4 class="fw-bold mb-2">Isi Formulir Pendaftaran</h4>
                        <p class="text-muted mb-3">{{ $konten['alur_step_2'] ?? 'Lengkapi formulir pendaftaran online dengan data diri yang benar dan akurat.' }}</p>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <i class="bi bi-person text-primary me-2"></i>Data Pribadi
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <i class="bi bi-mortarboard text-primary me-2"></i>Data Pendidikan
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <i class="bi bi-people text-primary me-2"></i>Data Orang Tua
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <i class="bi bi-journal-check text-primary me-2"></i>Pilihan Program Studi
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="d-flex mb-5" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">3</span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h4 class="fw-bold mb-2">Upload Dokumen</h4>
                        <p class="text-muted mb-3">{{ $konten['alur_step_3'] ?? 'Upload dokumen persyaratan yang diperlukan sesuai jalur pendaftaran yang dipilih.' }}</p>
                        <div class="alert alert-info mb-0">
                            <h6 class="fw-bold mb-2"><i class="bi bi-folder2-open me-2"></i>Dokumen yang Diperlukan:</h6>
                            <ul class="mb-0 ps-3">
                                <li>Pas foto 3x4 berlatar merah (format JPG/PNG)</li>
                                <li>Scan KTP atau Surat Keterangan Domisili</li>
                                <li>Scan Kartu Keluarga</li>
                                <li>Scan Ijazah atau Surat Keterangan Lulus</li>
                                <li>Scan Rapor semester 1-5</li>
                                <li>Sertifikat prestasi (untuk jalur prestasi)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="d-flex mb-5" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">4</span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h4 class="fw-bold mb-2">Pembayaran Biaya Pendaftaran</h4>
                        <p class="text-muted mb-3">{{ $konten['alur_step_4'] ?? 'Lakukan pembayaran biaya pendaftaran melalui metode yang tersedia.' }}</p>
                        <div class="row g-2">
                            <div class="col-auto">
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i class="bi bi-bank me-1"></i>Transfer Bank
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i class="bi bi-credit-card me-1"></i>Virtual Account
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i class="bi bi-wallet2 me-1"></i>E-Wallet
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="d-flex mb-5" data-aos="fade-up" data-aos-delay="400">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">5</span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h4 class="fw-bold mb-2">Ikuti Ujian Seleksi</h4>
                        <p class="text-muted mb-3">{{ $konten['alur_step_5'] ?? 'Ikuti ujian seleksi sesuai jadwal yang ditentukan (untuk jalur yang memerlukan ujian).' }}</p>
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <small>
                                <i class="bi bi-info-circle text-warning me-2"></i>
                                Ujian dilaksanakan secara <strong>Computer Based Test (CBT)</strong> di kampus atau secara online. Pastikan Anda sudah mencetak kartu peserta ujian.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Step 6 -->
                <div class="d-flex mb-5" data-aos="fade-up" data-aos-delay="500">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">6</span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h4 class="fw-bold mb-2">Cek Pengumuman</h4>
                        <p class="text-muted mb-3">{{ $konten['alur_step_6'] ?? 'Cek hasil seleksi melalui website PMB atau email yang terdaftar.' }}</p>
                        <a href="{{ route('portal-pmb.cek-pengumuman') }}" class="btn btn-outline-primary">
                            <i class="bi bi-search me-2"></i>Cek Pengumuman
                        </a>
                    </div>
                </div>

                <!-- Step 7 -->
                <div class="d-flex" data-aos="fade-up" data-aos-delay="600">
                    <div class="flex-shrink-0">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">7</span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h4 class="fw-bold mb-2">Daftar Ulang</h4>
                        <p class="text-muted mb-3">{{ $konten['alur_step_7'] ?? 'Jika dinyatakan lulus, segera lakukan daftar ulang sesuai jadwal yang ditentukan.' }}</p>
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <h6 class="fw-bold mb-2 text-success"><i class="bi bi-check-circle me-2"></i>Proses Daftar Ulang:</h6>
                            <ol class="mb-0 ps-3 text-muted">
                                <li>Pembayaran UKT (Uang Kuliah Tunggal)</li>
                                <li>Verifikasi dokumen asli</li>
                                <li>Pengisian data mahasiswa baru</li>
                                <li>Foto untuk KTM (Kartu Tanda Mahasiswa)</li>
                                <li>Pembuatan akun SIAKAD</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h3 class="fw-bold mb-3">Siap Untuk Mendaftar?</h3>
                <p class="text-muted mb-4">Jangan lewatkan kesempatan untuk menjadi bagian dari keluarga besar kampus kami!</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="{{ route('portal-pmb.pendaftaran') }}" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                    </a>
                    <a href="{{ route('portal-pmb.faq') }}" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-question-circle me-2"></i>Baca FAQ
                    </a>
                    <a href="{{ route('portal-pmb.kontak') }}" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-headset me-2"></i>Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
