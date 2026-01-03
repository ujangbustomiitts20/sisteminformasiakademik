@extends('layouts.app')

@section('title', 'Detail Daftar Ulang')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Daftar Ulang</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pmb.daftar-ulang.index') }}">Daftar Ulang</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pmb.daftar-ulang.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Info Daftar Ulang -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-info-circle me-2"></i>Informasi Daftar Ulang</span>
                    <span class="badge bg-{{ $daftarUlang->status_badge }} fs-6">{{ $daftarUlang->status_label }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" style="width: 40%">No. Daftar Ulang</td>
                                    <td>{{ $daftarUlang->no_daftar_ulang }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Program Studi</td>
                                    <td><span class="badge bg-primary">{{ $daftarUlang->programStudi->nama ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Biaya Daftar Ulang</td>
                                    <td>Rp {{ number_format($daftarUlang->biaya_daftar_ulang, 0, ',', '.') }}</td>
                                </tr>
                                @if($daftarUlang->biaya_ukt > 0)
                                <tr>
                                    <td class="fw-bold">Biaya UKT</td>
                                    <td>Rp {{ number_format($daftarUlang->biaya_ukt, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Biaya</td>
                                    <td><strong class="text-primary fs-5">Rp {{ number_format($daftarUlang->total_biaya, 0, ',', '.') }}</strong></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" style="width: 40%">Tanggal Dibuat</td>
                                    <td>{{ $daftarUlang->created_at->format('d F Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Batas Waktu</td>
                                    <td>
                                        @if($daftarUlang->tanggal_expired)
                                            {{ $daftarUlang->tanggal_expired->format('d F Y') }}
                                            @if($daftarUlang->tanggal_expired->isPast() && !in_array($daftarUlang->status, ['sudah_bayar', 'menjadi_mahasiswa']))
                                                <span class="badge bg-danger">Expired</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Bayar</td>
                                    <td>{{ $daftarUlang->tanggal_bayar ? $daftarUlang->tanggal_bayar->format('d F Y, H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hasil Seleksi -->
            @if($daftarUlang->calonMahasiswa->hasilSeleksi)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-trophy me-2"></i>Hasil Seleksi
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">Nilai Total</td>
                                    <td><strong>{{ $daftarUlang->calonMahasiswa->hasilSeleksi->nilai_total }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Ranking</td>
                                    <td>{{ $daftarUlang->calonMahasiswa->hasilSeleksi->ranking }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Pembayaran -->
            @if($pembayaran)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-cash me-2"></i>Informasi Pembayaran
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold" style="width: 30%">No. Pembayaran</td>
                            <td>{{ $pembayaran->no_pembayaran }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jumlah</td>
                            <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status</td>
                            <td><span class="badge bg-{{ $pembayaran->status_badge }}">{{ $pembayaran->status_label }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal Bayar</td>
                            <td>{{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d F Y, H:i') : '-' }}</td>
                        </tr>
                    </table>
                    @if($pembayaran->bukti_bayar)
                    <hr>
                    <label class="form-label fw-bold">Bukti Pembayaran</label>
                    <br>
                    <a href="{{ Storage::url($pembayaran->bukti_bayar) }}" target="_blank">
                        @php
                            $ext = pathinfo($pembayaran->bukti_bayar, PATHINFO_EXTENSION);
                        @endphp
                        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ Storage::url($pembayaran->bukti_bayar) }}" class="img-fluid rounded" style="max-height: 200px;">
                        @else
                            <i class="bi bi-file-earmark-pdf me-1"></i> Download Bukti Pembayaran
                        @endif
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Info: Menunggu Pembayaran -->
            @if($daftarUlang->status == 'pending')
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-clock me-2"></i>Menunggu Pembayaran
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Calon mahasiswa belum melakukan pembayaran daftar ulang. 
                        @if($pembayaran)
                            <br><br>
                            <a href="{{ route('pmb.pembayaran.show', $pembayaran->hashid) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye me-1"></i> Lihat & Verifikasi di Menu Pembayaran
                            </a>
                        @else
                            Silakan tunggu calon mahasiswa melakukan pembayaran.
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Aksi: Proses Menjadi Mahasiswa -->
            @if(in_array($daftarUlang->status, ['lunas']))
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-mortarboard me-2"></i>Proses Menjadi Mahasiswa
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Proses ini akan membuat data mahasiswa baru dan mengubah status calon mahasiswa menjadi "Menjadi Mahasiswa".
                    </div>
                    <form action="{{ route('pmb.daftar-ulang.proses-mahasiswa', $daftarUlang->hashid) }}" method="POST" onsubmit="return confirm('Yakin ingin memproses menjadi mahasiswa?')">>
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                                <input type="text" name="nim" class="form-control" placeholder="Auto-generate jika kosong">
                                <small class="text-muted">Biarkan kosong untuk auto-generate</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Angkatan</label>
                                <input type="text" name="angkatan" class="form-control" value="{{ date('Y') }}" readonly>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-mortarboard me-1"></i> Proses Menjadi Mahasiswa
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if($daftarUlang->status == 'menjadi_mahasiswa' && $daftarUlang->mahasiswa)
            <div class="card mb-4 bg-success text-white">
                <div class="card-body">
                    <h5><i class="bi bi-check-circle me-2"></i>Sudah Menjadi Mahasiswa</h5>
                    <p class="mb-0">
                        NIM: <strong>{{ $daftarUlang->mahasiswa->nim ?? '-' }}</strong><br>
                        Tanggal: {{ $daftarUlang->tanggal_proses ? $daftarUlang->tanggal_proses->format('d F Y') : '-' }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Info Pendaftar -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>Data Pendaftar
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($daftarUlang->calonMahasiswa->foto)
                            <img src="{{ Storage::url($daftarUlang->calonMahasiswa->foto) }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                {{ strtoupper(substr($daftarUlang->calonMahasiswa->nama_lengkap, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold">No. Pendaftaran</td>
                            <td>
                                <a href="{{ route('pmb.calon-mahasiswa.show', $daftarUlang->calonMahasiswa->hashid) }}">
                                    {{ $daftarUlang->calonMahasiswa->no_pendaftaran }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nama</td>
                            <td>{{ $daftarUlang->calonMahasiswa->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email</td>
                            <td>{{ $daftarUlang->calonMahasiswa->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">No. HP</td>
                            <td>{{ $daftarUlang->calonMahasiswa->no_hp }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">TTL</td>
                            <td>{{ $daftarUlang->calonMahasiswa->tempat_lahir }}, {{ $daftarUlang->calonMahasiswa->tanggal_lahir->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Asal Sekolah</td>
                            <td>{{ $daftarUlang->calonMahasiswa->asal_sekolah }}</td>
                        </tr>
                    </table>
                    <div class="d-grid">
                        <a href="{{ route('pmb.calon-mahasiswa.show', $daftarUlang->calonMahasiswa->hashid) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i> Detail Lengkap
                        </a>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-clock-history me-2"></i>Timeline
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge rounded-pill bg-success"><i class="bi bi-check-lg"></i></span>
                                </div>
                                <div>
                                    <strong>Lulus Seleksi</strong>
                                    <br><small class="text-muted">{{ $daftarUlang->calonMahasiswa->hasilSeleksi->created_at->format('d/m/Y H:i') ?? '-' }}</small>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge rounded-pill bg-{{ $daftarUlang->status != 'menunggu_bayar' || $daftarUlang->tanggal_bayar ? 'success' : 'warning' }}">
                                        <i class="bi bi-{{ $daftarUlang->tanggal_bayar ? 'check-lg' : 'clock' }}"></i>
                                    </span>
                                </div>
                                <div>
                                    <strong>Daftar Ulang Dibuat</strong>
                                    <br><small class="text-muted">{{ $daftarUlang->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </li>
                        @if($daftarUlang->tanggal_bayar)
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge rounded-pill bg-success"><i class="bi bi-check-lg"></i></span>
                                </div>
                                <div>
                                    <strong>Pembayaran</strong>
                                    <br><small class="text-muted">{{ $daftarUlang->tanggal_bayar->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </li>
                        @endif
                        @if($daftarUlang->status == 'menjadi_mahasiswa')
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge rounded-pill bg-success"><i class="bi bi-check-lg"></i></span>
                                </div>
                                <div>
                                    <strong>Menjadi Mahasiswa</strong>
                                    <br><small class="text-muted">{{ $daftarUlang->tanggal_proses ? $daftarUlang->tanggal_proses->format('d/m/Y H:i') : '-' }}</small>
                                </div>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
