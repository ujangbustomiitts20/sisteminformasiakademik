@extends('portal-pmb.layouts.main')

@section('title', 'Pendaftaran Belum Dibuka')

@section('content')
<section class="py-5 min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-lg p-5 text-center" data-aos="fade-up">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle" style="width: 100px; height: 100px;">
                            <i class="bi bi-calendar-x text-warning" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    
                    <h2 class="fw-bold mb-3">Pendaftaran Belum Dibuka</h2>
                    
                    @if(isset($periodePmb))
                        <p class="text-muted mb-4">
                            Periode PMB <strong>{{ $periodePmb->nama }}</strong> sedang tidak dalam masa pendaftaran aktif.
                        </p>
                        
                        @if(isset($gelombangBerikutnya))
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Gelombang Berikutnya:</strong><br>
                                {{ $gelombangBerikutnya->nama }} akan dibuka pada 
                                <strong>{{ $gelombangBerikutnya->tanggal_mulai_daftar->format('d F Y') }}</strong>
                            </div>
                            
                            <div class="row g-3 justify-content-center mb-4">
                                <div class="col-auto">
                                    <div class="bg-light rounded-3 p-3 text-center" style="min-width: 80px;">
                                        <div class="fw-bold text-primary fs-3" id="countdown-days">--</div>
                                        <small class="text-muted">Hari</small>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="bg-light rounded-3 p-3 text-center" style="min-width: 80px;">
                                        <div class="fw-bold text-primary fs-3" id="countdown-hours">--</div>
                                        <small class="text-muted">Jam</small>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="bg-light rounded-3 p-3 text-center" style="min-width: 80px;">
                                        <div class="fw-bold text-primary fs-3" id="countdown-minutes">--</div>
                                        <small class="text-muted">Menit</small>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="bg-light rounded-3 p-3 text-center" style="min-width: 80px;">
                                        <div class="fw-bold text-primary fs-3" id="countdown-seconds">--</div>
                                        <small class="text-muted">Detik</small>
                                    </div>
                                </div>
                            </div>
                            
                            <script>
                                const targetDate = new Date('{{ $gelombangBerikutnya->tanggal_mulai_daftar->format('Y-m-d') }}T00:00:00');
                                
                                function updateCountdown() {
                                    const now = new Date();
                                    const diff = targetDate - now;
                                    
                                    if (diff > 0) {
                                        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                                        
                                        document.getElementById('countdown-days').textContent = days;
                                        document.getElementById('countdown-hours').textContent = hours;
                                        document.getElementById('countdown-minutes').textContent = minutes;
                                        document.getElementById('countdown-seconds').textContent = seconds;
                                    } else {
                                        // Refresh halaman jika sudah waktunya
                                        location.reload();
                                    }
                                }
                                
                                updateCountdown();
                                setInterval(updateCountdown, 1000);
                            </script>
                        @else
                            <p class="text-muted mb-4">
                                Saat ini tidak ada gelombang pendaftaran yang aktif. Silakan cek kembali nanti atau hubungi panitia PMB untuk informasi lebih lanjut.
                            </p>
                        @endif
                    @else
                        <p class="text-muted mb-4">
                            Saat ini tidak ada periode PMB yang aktif. Silakan cek kembali nanti untuk informasi pendaftaran mahasiswa baru.
                        </p>
                    @endif
                    
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="{{ route('portal-pmb.index') }}" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-house me-2"></i>Kembali ke Beranda
                        </a>
                        <a href="{{ route('portal-pmb.jadwal') }}" class="btn btn-outline-primary btn-lg px-4">
                            <i class="bi bi-calendar3 me-2"></i>Lihat Jadwal
                        </a>
                        <a href="{{ route('portal-pmb.kontak') }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="bi bi-headset me-2"></i>Hubungi Kami
                        </a>
                    </div>
                </div>
                
                <!-- Info Tambahan -->
                <div class="row g-4 mt-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="bg-white bg-opacity-10 rounded-3 p-4 text-white text-center h-100">
                            <i class="bi bi-journal-bookmark fs-2 mb-3"></i>
                            <h5 class="fw-semibold">Pelajari Program Studi</h5>
                            <p class="small mb-3 opacity-75">Kenali berbagai program studi yang tersedia di kampus kami.</p>
                            <a href="{{ route('portal-pmb.program-studi') }}" class="btn btn-sm btn-light">Lihat Prodi</a>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="bg-white bg-opacity-10 rounded-3 p-4 text-white text-center h-100">
                            <i class="bi bi-question-circle fs-2 mb-3"></i>
                            <h5 class="fw-semibold">FAQ</h5>
                            <p class="small mb-3 opacity-75">Temukan jawaban dari pertanyaan yang sering ditanyakan.</p>
                            <a href="{{ route('portal-pmb.faq') }}" class="btn btn-sm btn-light">Baca FAQ</a>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="bg-white bg-opacity-10 rounded-3 p-4 text-white text-center h-100">
                            <i class="bi bi-newspaper fs-2 mb-3"></i>
                            <h5 class="fw-semibold">Berita Terbaru</h5>
                            <p class="small mb-3 opacity-75">Ikuti berita dan pengumuman terbaru dari panitia PMB.</p>
                            <a href="{{ route('portal-pmb.berita') }}" class="btn btn-sm btn-light">Baca Berita</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
