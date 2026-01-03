@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Pembayaran</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pmb.pembayaran.index') }}">Pembayaran</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('pmb.pembayaran.index') }}" class="btn btn-secondary">
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
            <!-- Info Pembayaran -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-info-circle me-2"></i>Informasi Pembayaran</span>
                    <span class="badge bg-{{ $pembayaran->status_badge }} fs-6">{{ $pembayaran->status_label }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" style="width: 40%">No. Pembayaran</td>
                                    <td>{{ $pembayaran->no_pembayaran }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jenis Pembayaran</td>
                                    <td>
                                        @if($pembayaran->jenis_pembayaran == 'pendaftaran')
                                            <span class="badge bg-primary">Biaya Pendaftaran</span>
                                        @else
                                            <span class="badge bg-info">Biaya Daftar Ulang</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jumlah</td>
                                    <td><strong class="text-primary fs-5">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" style="width: 40%">Tanggal Tagihan</td>
                                    <td>{{ $pembayaran->created_at->format('d F Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Batas Pembayaran</td>
                                    <td>
                                        @if($pembayaran->tanggal_expired)
                                            {{ $pembayaran->tanggal_expired->format('d F Y') }}
                                            @if($pembayaran->tanggal_expired->isPast() && $pembayaran->status != 'terverifikasi')
                                                <span class="badge bg-danger">Expired</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Bayar</td>
                                    <td>{{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d F Y, H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bukti Pembayaran -->
            @if($pembayaran->bukti_bayar)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-file-earmark-image me-2"></i>Bukti Pembayaran
                </div>
                <div class="card-body text-center">
                    @php
                        $ext = pathinfo($pembayaran->bukti_bayar, PATHINFO_EXTENSION);
                    @endphp
                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                        <a href="{{ Storage::url($pembayaran->bukti_bayar) }}" target="_blank">
                            <img src="{{ Storage::url($pembayaran->bukti_bayar) }}" class="img-fluid rounded" style="max-height: 400px;">
                        </a>
                    @else
                        <a href="{{ Storage::url($pembayaran->bukti_bayar) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-download me-1"></i> Download Bukti Pembayaran
                        </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Info Verifikasi -->
            @if($pembayaran->status == 'terverifikasi')
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-check-circle me-2"></i>Informasi Verifikasi
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold" style="width: 30%">Tanggal Verifikasi</td>
                            <td>{{ $pembayaran->verified_at ? $pembayaran->verified_at->format('d F Y, H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diverifikasi Oleh</td>
                            <td>{{ $pembayaran->verifiedBy->name ?? 'System' }}</td>
                        </tr>
                        @if($pembayaran->catatan)
                        <tr>
                            <td class="fw-bold">Catatan</td>
                            <td>{{ $pembayaran->catatan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            <!-- Aksi Konfirmasi Pembayaran (untuk status pending) -->
            @if($pembayaran->status == 'pending')
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <i class="bi bi-cash me-2"></i>Konfirmasi Pembayaran
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Klik tombol di bawah untuk mengkonfirmasi bahwa calon mahasiswa sudah melakukan pembayaran.</p>
                    <form action="{{ route('pmb.pembayaran.konfirmasi', $pembayaran->hashid) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                            <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="tunai">Tunai</option>
                                <option value="va">Virtual Account</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bank" class="form-label">Bank (opsional)</label>
                            <input type="text" name="bank" id="bank" class="form-control" placeholder="Contoh: BCA, Mandiri, BRI">
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (opsional)</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="2" placeholder="Catatan tambahan"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-lg me-1"></i> Konfirmasi Sudah Bayar
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Aksi Verifikasi -->
            @if($pembayaran->status == 'menunggu_verifikasi')
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-pencil-square me-2"></i>Verifikasi Pembayaran
                </div>
                <div class="card-body">
                    <form action="{{ route('pmb.pembayaran.verifikasi', $pembayaran->hashid) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (opsional)</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="2" placeholder="Tambahkan catatan jika diperlukan"></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="status" value="terverifikasi" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i> Verifikasi
                            </button>
                            <button type="submit" name="status" value="ditolak" class="btn btn-danger">
                                <i class="bi bi-x-lg me-1"></i> Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Info Pendaftar -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>Informasi Pendaftar
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($pembayaran->calonMahasiswa->foto)
                            <img src="{{ Storage::url($pembayaran->calonMahasiswa->foto) }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ strtoupper(substr($pembayaran->calonMahasiswa->nama_lengkap, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold">No. Pendaftaran</td>
                            <td>
                                <a href="{{ route('pmb.calon-mahasiswa.show', $pembayaran->calonMahasiswa->hashid) }}">
                                    {{ $pembayaran->calonMahasiswa->no_pendaftaran }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nama</td>
                            <td>{{ $pembayaran->calonMahasiswa->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email</td>
                            <td>{{ $pembayaran->calonMahasiswa->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">No. HP</td>
                            <td>{{ $pembayaran->calonMahasiswa->no_hp }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Prodi</td>
                            <td>{{ $pembayaran->calonMahasiswa->programStudi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status</td>
                            <td><span class="badge bg-{{ $pembayaran->calonMahasiswa->status_badge }}">{{ $pembayaran->calonMahasiswa->status_label }}</span></td>
                        </tr>
                    </table>
                    <div class="d-grid">
                        <a href="{{ route('pmb.calon-mahasiswa.show', $pembayaran->calonMahasiswa->hashid) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>

            <!-- Histori Pembayaran -->
            @if($riwayatPembayaran->count() > 1)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-clock-history me-2"></i>Histori Pembayaran
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($riwayatPembayaran as $riwayat)
                        <li class="list-group-item {{ $riwayat->id == $pembayaran->id ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">{{ $riwayat->jenis_pembayaran_label }}</small>
                                    <br>Rp {{ number_format($riwayat->jumlah, 0, ',', '.') }}
                                </div>
                                <span class="badge bg-{{ $riwayat->status_badge }}">{{ $riwayat->status_label }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
