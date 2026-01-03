@extends('layouts.app')

@section('title', 'Detail Calon Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $calonMahasiswa->nama_lengkap }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pmb.dashboard') }}">PMB</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pmb.calon-mahasiswa.index') }}">Calon Mahasiswa</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('pmb.calon-mahasiswa.cetak-kartu-peserta', $calonMahasiswa->hashid) }}" class="btn btn-success" target="_blank">
                <i class="bi bi-printer me-1"></i> Cetak Kartu
            </a>
            <a href="{{ route('pmb.calon-mahasiswa.edit', $calonMahasiswa->hashid) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <a href="{{ route('pmb.calon-mahasiswa.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Foto & Status -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($calonMahasiswa->foto)
                        <img src="{{ Storage::url($calonMahasiswa->foto) }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px; font-size: 3rem;">
                            {{ strtoupper(substr($calonMahasiswa->nama_lengkap, 0, 1)) }}
                        </div>
                    @endif
                    <h5 class="mb-1">{{ $calonMahasiswa->nama_lengkap }}</h5>
                    <p class="text-muted mb-2">{{ $calonMahasiswa->no_pendaftaran }}</p>
                    <span class="badge bg-{{ $calonMahasiswa->status_badge }} fs-6">{{ $calonMahasiswa->status_label }}</span>
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Ringkasan
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Dokumen Lengkap</span>
                        @if($calonMahasiswa->is_dokumen_lengkap)
                            <span class="badge bg-success"><i class="bi bi-check-lg"></i> Ya</span>
                        @else
                            <span class="badge bg-warning"><i class="bi bi-clock"></i> Belum</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pembayaran</span>
                        @if($calonMahasiswa->is_bayar_pendaftaran)
                            <span class="badge bg-success"><i class="bi bi-check-lg"></i> Lunas</span>
                        @else
                            <span class="badge bg-warning"><i class="bi bi-clock"></i> Belum</span>
                        @endif
                    </div>
                    <hr>
                    <div class="mb-2">
                        <strong>Gelombang:</strong><br>
                        {{ $calonMahasiswa->gelombangPmb->nama ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>Jalur Seleksi:</strong><br>
                        {{ $calonMahasiswa->jalurSeleksi->nama ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>Prodi Pilihan 1:</strong><br>
                        {{ $calonMahasiswa->programStudi->nama ?? 'N/A' }}
                    </div>
                    @if($calonMahasiswa->programStudi2)
                    <div class="mb-0">
                        <strong>Prodi Pilihan 2:</strong><br>
                        {{ $calonMahasiswa->programStudi2->nama }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Aksi Cepat -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-link-45deg me-2"></i>Aksi
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('pmb.calon-mahasiswa.verifikasi-dokumen', $calonMahasiswa->hashid) }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-check me-1"></i> Verifikasi Dokumen
                        </a>
                        @if(!$calonMahasiswa->is_bayar_pendaftaran)
                        <form action="{{ route('pmb.pembayaran.generate-tagihan') }}" method="POST">
                            @csrf
                            <input type="hidden" name="calon_mahasiswa_id" value="{{ $calonMahasiswa->id }}">
                            <input type="hidden" name="jenis_pembayaran" value="pendaftaran">
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="bi bi-cash me-1"></i> Generate Tagihan
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Data Pribadi -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>Data Pribadi
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" style="width: 40%">NIK</td>
                                    <td>{{ $calonMahasiswa->nik ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">NISN</td>
                                    <td>{{ $calonMahasiswa->nisn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jenis Kelamin</td>
                                    <td>{{ $calonMahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">TTL</td>
                                    <td>{{ $calonMahasiswa->tempat_lahir }}, {{ $calonMahasiswa->tanggal_lahir->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Agama</td>
                                    <td>{{ $calonMahasiswa->agama ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" style="width: 40%">Email</td>
                                    <td>{{ $calonMahasiswa->email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">No. HP</td>
                                    <td>{{ $calonMahasiswa->no_hp }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">WhatsApp</td>
                                    <td>{{ $calonMahasiswa->no_wa ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <p class="mb-0"><strong>Alamat:</strong><br>{{ $calonMahasiswa->alamat_lengkap }}</p>
                </div>
            </div>

            <!-- Asal Sekolah -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-building me-2"></i>Asal Sekolah
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold" style="width: 30%">Nama Sekolah</td>
                            <td>{{ $calonMahasiswa->asal_sekolah }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">NPSN</td>
                            <td>{{ $calonMahasiswa->npsn_sekolah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jurusan</td>
                            <td>{{ $calonMahasiswa->jurusan_sekolah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tahun Lulus</td>
                            <td>{{ $calonMahasiswa->tahun_lulus }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nilai Rata-rata</td>
                            <td>{{ $calonMahasiswa->nilai_rata_rata ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-people me-2"></i>Data Orang Tua
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Ayah</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">Nama</td>
                                    <td>{{ $calonMahasiswa->nama_ayah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Pekerjaan</td>
                                    <td>{{ $calonMahasiswa->pekerjaan_ayah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">No. HP</td>
                                    <td>{{ $calonMahasiswa->no_hp_ayah ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Ibu</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">Nama</td>
                                    <td>{{ $calonMahasiswa->nama_ibu ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Pekerjaan</td>
                                    <td>{{ $calonMahasiswa->pekerjaan_ibu ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">No. HP</td>
                                    <td>{{ $calonMahasiswa->no_hp_ibu ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if($calonMahasiswa->penghasilan_ortu)
                    <p class="mb-0"><strong>Penghasilan Orang Tua:</strong> Rp {{ number_format($calonMahasiswa->penghasilan_ortu, 0, ',', '.') }}/bulan</p>
                    @endif
                </div>
            </div>

            <!-- Dokumen -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-earmark me-2"></i>Dokumen</span>
                    <a href="{{ route('pmb.calon-mahasiswa.verifikasi-dokumen', $calonMahasiswa->hashid) }}" class="btn btn-sm btn-primary">
                        Verifikasi
                    </a>
                </div>
                <div class="card-body">
                    @if($calonMahasiswa->dokumen->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Jenis Dokumen</th>
                                    <th>File</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calonMahasiswa->dokumen as $dok)
                                <tr>
                                    <td>{{ $dok->jenis_dokumen_label }}</td>
                                    <td>
                                        <a href="{{ Storage::url($dok->path_file) }}" target="_blank">
                                            {{ $dok->nama_file }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $dok->status_badge }}">{{ $dok->status_label }}</span>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('pmb.calon-mahasiswa.delete-dokumen', ['calon' => $calonMahasiswa->hashid, 'dokumen' => $dok->hashid]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center mb-3">Belum ada dokumen yang diunggah</p>
                    @endif

                    <!-- Form Upload Dokumen -->
                    <div class="border-top pt-3 mt-3">
                        <h6 class="mb-3"><i class="bi bi-upload me-2"></i>Upload Dokumen Baru</h6>
                        <form action="{{ route('pmb.calon-mahasiswa.upload-dokumen', $calonMahasiswa->hashid) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
                                    <select name="jenis_dokumen" id="jenis_dokumen" class="form-select" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        @php
                                            $dokumenWajib = $settingDokumen->where('is_wajib', true);
                                            $dokumenOpsional = $settingDokumen->where('is_wajib', false);
                                        @endphp
                                        @if($dokumenWajib->count() > 0)
                                        <optgroup label="📌 Dokumen Wajib">
                                            @foreach($dokumenWajib as $setting)
                                                @php
                                                    $sudahAda = $calonMahasiswa->dokumen->where('jenis_dokumen', $setting->kode)->first();
                                                @endphp
                                                <option value="{{ $setting->kode }}" {{ $sudahAda ? 'disabled' : '' }}>
                                                    {{ $setting->nama_dokumen }}
                                                    @if($sudahAda) ✓ Sudah diupload @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        @endif
                                        @if($dokumenOpsional->count() > 0)
                                        <optgroup label="📎 Dokumen Opsional">
                                            @foreach($dokumenOpsional as $setting)
                                                @php
                                                    $sudahAda = $calonMahasiswa->dokumen->where('jenis_dokumen', $setting->kode)->first();
                                                @endphp
                                                <option value="{{ $setting->kode }}" {{ $sudahAda ? 'disabled' : '' }}>
                                                    {{ $setting->nama_dokumen }}
                                                    @if($sudahAda) ✓ Sudah diupload @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label for="file" class="form-label">File</label>
                                    <input type="file" name="file" id="file" class="form-control" required>
                                    <small class="text-muted">Format: PDF, JPG, PNG. Maks: 5MB</small>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-upload me-1"></i> Upload
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hasil Seleksi -->
            @if($calonMahasiswa->hasilSeleksi)
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
                                    <td>{{ $calonMahasiswa->hasilSeleksi->nilai_total }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Ranking</td>
                                    <td>{{ $calonMahasiswa->hasilSeleksi->ranking }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status</td>
                                    <td><span class="badge bg-{{ $calonMahasiswa->hasilSeleksi->status_badge }} fs-6">{{ $calonMahasiswa->hasilSeleksi->status_label }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($calonMahasiswa->hasilSeleksi->program_studi_diterima_id)
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">Prodi Diterima</td>
                                    <td>{{ $calonMahasiswa->hasilSeleksi->programStudiDiterima->nama ?? '-' }}</td>
                                </tr>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Nilai Seleksi -->
            @if($calonMahasiswa->nilaiSeleksi->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart me-2"></i>Nilai Seleksi
                </div>
                <div class="card-body">
                    @foreach($calonMahasiswa->nilaiSeleksi as $nilaiSeleksi)
                    <table class="table table-sm mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>Komponen</th>
                                <th class="text-center">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nilaiSeleksi->komponen_nilai_detail as $detail)
                            <tr>
                                <td>{{ $detail['label'] }}</td>
                                <td class="text-center">{{ $detail['nilai'] }}</td>
                            </tr>
                            @endforeach
                            <tr class="table-secondary">
                                <td class="fw-bold">Nilai Total</td>
                                <td class="text-center"><strong>{{ number_format($nilaiSeleksi->nilai_total, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
