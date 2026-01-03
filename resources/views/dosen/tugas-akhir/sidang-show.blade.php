@extends('layouts.app')

@section('title', 'Detail Sidang')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Sidang TA</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dosen.tugas-akhir.sidang-penguji') }}">Sidang Penguji</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('dosen.tugas-akhir.sidang-penguji') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Info Sidang -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Sidang</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%">Tanggal</td>
                            <td>: <strong>{{ $sidang->tanggal->format('d M Y') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Waktu</td>
                            <td>: {{ $sidang->waktu_mulai }} - {{ $sidang->waktu_selesai }}</td>
                        </tr>
                        <tr>
                            <td>Ruangan</td>
                            <td>: {{ $sidang->ruangan }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: 
                                <span class="badge bg-{{ $sidang->status == 'selesai' ? 'success' : 'warning' }}">
                                    {{ ucfirst($sidang->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Tim Penguji</h6>
                </div>
                <div class="card-body">
                    @php
                        $peran = null;
                        if ($sidang->ketua_penguji_id == $dosen->id) {
                            $peran = 'Ketua Penguji';
                        } elseif ($sidang->penguji_1_id == $dosen->id) {
                            $peran = 'Penguji 1';
                        } elseif ($sidang->penguji_2_id == $dosen->id) {
                            $peran = 'Penguji 2';
                        }
                    @endphp
                    <div class="alert alert-info py-2">
                        <small>Peran Anda:</small><br>
                        <strong>{{ $peran }}</strong>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Ketua Penguji:</strong><br>
                            {{ $sidang->ketuaPenguji->nama ?? '-' }}
                            @if($sidang->nilai_ketua)
                            <span class="badge bg-success float-end">{{ $sidang->nilai_ketua }}</span>
                            @endif
                        </li>
                        <li class="mb-2">
                            <strong>Penguji 1:</strong><br>
                            {{ $sidang->penguji1->nama ?? '-' }}
                            @if($sidang->nilai_penguji_1)
                            <span class="badge bg-success float-end">{{ $sidang->nilai_penguji_1 }}</span>
                            @endif
                        </li>
                        <li>
                            <strong>Penguji 2:</strong><br>
                            {{ $sidang->penguji2->nama ?? '-' }}
                            @if($sidang->nilai_penguji_2)
                            <span class="badge bg-success float-end">{{ $sidang->nilai_penguji_2 }}</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Input Nilai (jika belum) -->
            @php
                $sudahNilai = false;
                $kolomNilai = null;
                if ($sidang->ketua_penguji_id == $dosen->id) {
                    $sudahNilai = $sidang->nilai_ketua !== null;
                    $kolomNilai = 'nilai_ketua';
                } elseif ($sidang->penguji_1_id == $dosen->id) {
                    $sudahNilai = $sidang->nilai_penguji_1 !== null;
                    $kolomNilai = 'nilai_penguji_1';
                } elseif ($sidang->penguji_2_id == $dosen->id) {
                    $sudahNilai = $sidang->nilai_penguji_2 !== null;
                    $kolomNilai = 'nilai_penguji_2';
                }
            @endphp
            @if(!$sudahNilai && $sidang->status == 'dijadwalkan')
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Input Nilai</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('dosen.tugas-akhir.sidang.nilai', $sidang) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nilai (0-100) <span class="text-danger">*</span></label>
                            <input type="number" name="nilai" class="form-control" min="0" max="100" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Simpan Nilai
                        </button>
                    </form>
                </div>
            </div>
            @elseif($sudahNilai)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Nilai Anda</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success">
                        @if($kolomNilai == 'nilai_ketua')
                        {{ $sidang->nilai_ketua }}
                        @elseif($kolomNilai == 'nilai_penguji_1')
                        {{ $sidang->nilai_penguji_1 }}
                        @else
                        {{ $sidang->nilai_penguji_2 }}
                        @endif
                    </h2>
                    <small class="text-muted">Nilai sudah disimpan</small>
                </div>
            </div>
            @endif
        </div>

        <!-- Detail TA & Mahasiswa -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>NIM</td>
                                    <td>: <strong>{{ $sidang->tugasAkhir->mahasiswa->nim ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Nama</td>
                                    <td>: <strong>{{ $sidang->tugasAkhir->mahasiswa->nama ?? '-' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Pembimbing 1</td>
                                    <td>: {{ $sidang->tugasAkhir->pembimbing1->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Pembimbing 2</td>
                                    <td>: {{ $sidang->tugasAkhir->pembimbing2->nama ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Detail Tugas Akhir</h6>
                </div>
                <div class="card-body">
                    <h5>{{ $sidang->tugasAkhir->judul }}</h5>
                    @if($sidang->tugasAkhir->bidang_kajian)
                    <p><span class="badge bg-info">{{ $sidang->tugasAkhir->bidang_kajian }}</span></p>
                    @endif
                    
                    @if($sidang->tugasAkhir->abstrak)
                    <h6 class="mt-3">Abstrak</h6>
                    <p>{{ $sidang->tugasAkhir->abstrak }}</p>
                    @endif

                    @if($sidang->tugasAkhir->dokumen_draft)
                    <hr>
                    <a href="{{ Storage::url($sidang->tugasAkhir->dokumen_draft) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-file-pdf"></i> Download Draft TA
                    </a>
                    @endif
                </div>
            </div>

            <!-- Hasil Sidang (jika selesai) -->
            @if($sidang->status == 'selesai' && $sidang->nilai_akhir)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Hasil Sidang</h6>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Nilai Akhir</h5>
                            <h2 class="text-success">{{ number_format($sidang->nilai_akhir, 2) }}</h2>
                        </div>
                        <div class="col-md-4">
                            <h5>Grade</h5>
                            <h2 class="text-primary">{{ $sidang->grade ?? '-' }}</h2>
                        </div>
                        <div class="col-md-4">
                            <h5>Hasil</h5>
                            <h2>
                                <span class="badge bg-{{ $sidang->hasil == 'lulus' ? 'success' : ($sidang->hasil == 'lulus_revisi' ? 'warning' : 'danger') }}">
                                    {{ ucfirst(str_replace('_', ' ', $sidang->hasil ?? '-')) }}
                                </span>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Revisi -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Catatan Revisi</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahRevisiModal">
                        <i class="bi bi-plus"></i> Tambah Revisi
                    </button>
                </div>
                <div class="card-body">
                    @forelse($sidang->revisi as $revisi)
                    <div class="card mb-2 {{ $revisi->dosen_id == $dosen->id ? 'border-primary' : '' }}">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $revisi->dosen->nama ?? '-' }}</strong>
                                    <span class="badge bg-{{ $revisi->sudah_diperbaiki ? 'success' : 'warning' }}">
                                        {{ $revisi->sudah_diperbaiki ? 'Selesai' : 'Pending' }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $revisi->created_at->format('d M Y H:i') }}</small>
                                </div>
                                @if($revisi->dosen_id == $dosen->id && !$revisi->sudah_diperbaiki)
                                <form action="{{ route('dosen.tugas-akhir.revisi.verifikasi', $revisi) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Yakin revisi sudah selesai?')">
                                        <i class="bi bi-check"></i> Verifikasi
                                    </button>
                                </form>
                                @endif
                            </div>
                            <p class="mb-0 mt-2">{{ $revisi->catatan_revisi }}</p>
                            @if($revisi->tanggal_perbaikan)
                            <small class="text-success">Diperbaiki: {{ $revisi->tanggal_perbaikan->format('d M Y H:i') }}</small>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">Belum ada catatan revisi</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Revisi -->
<div class="modal fade" id="tambahRevisiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dosen.tugas-akhir.sidang.revisi', $sidang) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Catatan Revisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan Revisi <span class="text-danger">*</span></label>
                        <textarea name="catatan_revisi" class="form-control" rows="4" required placeholder="Tuliskan hal-hal yang perlu diperbaiki..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
