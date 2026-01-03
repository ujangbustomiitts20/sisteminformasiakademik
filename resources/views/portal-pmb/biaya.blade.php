@extends('portal-pmb.layouts.main')

@section('title', 'Biaya Kuliah')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" data-aos="fade-up">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="{{ route('portal-pmb.index') }}" class="text-white-50">Beranda</a></li>
                        <li class="breadcrumb-item active text-white">Biaya Kuliah</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-3" data-aos="fade-up" data-aos-delay="100">Biaya Kuliah</h1>
                <p class="text-white-50 fs-5" data-aos="fade-up" data-aos-delay="200">Informasi lengkap tentang biaya pendidikan</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Biaya Pendaftaran -->
        @if($biayaPendaftaran->count() > 0)
        <div class="mb-5">
            <h3 class="fw-bold mb-4" data-aos="fade-up"><i class="bi bi-credit-card text-primary me-2"></i>Biaya Pendaftaran</h3>
            <div class="row g-4">
                @foreach($biayaPendaftaran->groupBy('jalur_seleksi_id') as $jalurId => $biayaGroup)
                @php $biaya = $biayaGroup->first(); @endphp
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card card-pmb h-100">
                        <div class="card-body p-4 text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                <i class="bi bi-receipt text-primary fs-3"></i>
                            </div>
                            <h5 class="fw-bold mb-2">{{ $biaya->jalurSeleksi->nama ?? 'Jalur Seleksi' }}</h5>
                            <div class="mb-2">
                                <small class="text-muted d-block">Biaya Formulir</small>
                                <span class="text-primary fw-bold">Rp {{ number_format($biaya->biaya_formulir, 0, ',', '.') }}</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Biaya Ujian</small>
                                <span class="text-primary fw-bold">Rp {{ number_format($biaya->biaya_ujian, 0, ',', '.') }}</span>
                            </div>
                            <hr>
                            <div>
                                <small class="text-muted d-block">Total</small>
                                <h4 class="text-primary fw-bold mb-0">Rp {{ number_format($biaya->total_biaya ?? ($biaya->biaya_formulir + $biaya->biaya_ujian), 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="alert alert-info mb-5" data-aos="fade-up">
            <i class="bi bi-info-circle me-2"></i>Informasi biaya pendaftaran akan segera tersedia.
        </div>
        @endif
        
        <!-- Biaya Kuliah per Program Studi -->
        <div class="mb-5">
            <h3 class="fw-bold mb-4" data-aos="fade-up"><i class="bi bi-mortarboard text-primary me-2"></i>Biaya Kuliah per Program Studi</h3>
            
            @if(isset($programStudi) && $programStudi->count() > 0)
            <div class="table-responsive" data-aos="fade-up" data-aos-delay="100">
                <table class="table table-hover bg-white rounded-4 overflow-hidden shadow-sm">
                    <thead class="table-primary">
                        <tr>
                            <th class="py-3 px-4">Program Studi</th>
                            <th class="py-3 px-4">Jenjang</th>
                            <th class="py-3 px-4 text-end">SPP/Semester</th>
                            <th class="py-3 px-4 text-end">UKT/Semester</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programStudi as $prodi)
                        <tr>
                            <td class="py-3 px-4">
                                <h6 class="fw-bold mb-0">{{ $prodi->nama }}</h6>
                                <small class="text-muted">{{ $prodi->fakultas->nama ?? '' }}</small>
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge bg-primary">{{ $prodi->jenjang }}</span>
                            </td>
                            <td class="py-3 px-4 text-end">
                                @if($prodi->biaya_spp ?? false)
                                <span class="fw-bold">Rp {{ number_format($prodi->biaya_spp, 0, ',', '.') }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-end">
                                @if($prodi->biaya_ukt ?? false)
                                <span class="fw-bold">Rp {{ number_format($prodi->biaya_ukt, 0, ',', '.') }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('portal-pmb.program-studi.detail', $prodi->hashid) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data program studi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
            <div class="alert alert-info" data-aos="fade-up">
                <i class="bi bi-info-circle me-2"></i>Informasi biaya kuliah akan segera tersedia.
            </div>
            @endif
            </div>
        </div>
        
        <!-- Info Pembayaran -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="card card-pmb h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-bank text-primary me-2"></i>Metode Pembayaran</h5>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                                <i class="bi bi-building text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Transfer Bank</h6>
                                <small class="text-muted">BNI, BRI, Mandiri, BSI</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                <i class="bi bi-shop text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Minimarket</h6>
                                <small class="text-muted">Indomaret, Alfamart</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-3 p-2 me-3">
                                <i class="bi bi-wallet2 text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">E-Wallet</h6>
                                <small class="text-muted">GoPay, OVO, Dana, LinkAja</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card card-pmb h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-gift text-primary me-2"></i>Program Keringanan</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-start mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Beasiswa Prestasi Akademik</h6>
                                    <small class="text-muted">Potongan hingga 100% untuk mahasiswa berprestasi</small>
                                </div>
                            </li>
                            <li class="d-flex align-items-start mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Beasiswa Kurang Mampu</h6>
                                    <small class="text-muted">Bantuan biaya untuk keluarga kurang mampu</small>
                                </div>
                            </li>
                            <li class="d-flex align-items-start mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Potongan Pembayaran Awal</h6>
                                    <small class="text-muted">Diskon untuk pembayaran di muka</small>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Cicilan Tanpa Bunga</h6>
                                    <small class="text-muted">Fasilitas cicilan untuk meringankan beban</small>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Catatan -->
        <div class="alert alert-info border-0 rounded-4" data-aos="fade-up">
            <div class="d-flex">
                <i class="bi bi-info-circle fs-4 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">Catatan Penting</h6>
                    <ul class="mb-0 ps-3">
                        <li>Biaya di atas dapat berubah sewaktu-waktu</li>
                        <li>Biaya belum termasuk seragam, buku, dan kegiatan ekstrakurikuler</li>
                        <li>Pembayaran dapat dilakukan secara cicilan (syarat dan ketentuan berlaku)</li>
                        <li>Untuk informasi lebih lanjut, silakan hubungi bagian keuangan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container position-relative text-center text-white">
        <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">Ada Pertanyaan tentang Biaya?</h2>
        <p class="fs-5 opacity-75 mb-4" data-aos="fade-up" data-aos-delay="100">Tim kami siap membantu menjawab pertanyaan Anda</p>
        <a href="{{ route('portal-pmb.kontak') }}" class="btn btn-light btn-lg px-5" data-aos="fade-up" data-aos-delay="200">
            <i class="bi bi-chat-dots me-2"></i>Hubungi Kami
        </a>
    </div>
</section>
@endsection
