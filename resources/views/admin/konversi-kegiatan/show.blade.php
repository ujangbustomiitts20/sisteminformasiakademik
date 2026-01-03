@extends('layouts.app')

@section('title', 'Detail Pengajuan Konversi Kegiatan')

@section('content')
<div class="page-title">
    <h4>Verifikasi Pengajuan Konversi Kegiatan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.konversi-kegiatan.index') }}">Konversi Kegiatan</a></li>
            <li class="breadcrumb-item active">{{ $pengajuanKonversiKegiatan->no_pengajuan }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Info Pengajuan -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Data Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $pengajuanKonversiKegiatan->mahasiswa->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIM</td>
                        <td>{{ $pengajuanKonversiKegiatan->mahasiswa->nim }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $pengajuanKonversiKegiatan->mahasiswa->programStudi->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $pengajuanKonversiKegiatan->mahasiswa->angkatan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>{{ $pengajuanKonversiKegiatan->mahasiswa->status }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Informasi Pengajuan</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">No. Pengajuan</td>
                        <td><strong>{{ $pengajuanKonversiKegiatan->no_pengajuan }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>{!! $pengajuanKonversiKegiatan->status_badge !!}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td>{{ $pengajuanKonversiKegiatan->tanggal_pengajuan->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tahun Akademik</td>
                        <td>{{ $pengajuanKonversiKegiatan->tahunAkademik->tahun }} {{ $pengajuanKonversiKegiatan->tahunAkademik->semester }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Kegiatan</td>
                        <td>{{ $pengajuanKonversiKegiatan->details->count() }} kegiatan</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total SKS Diakui</td>
                        <td><strong class="text-success">{{ $pengajuanKonversiKegiatan->total_sks }} SKS</strong></td>
                    </tr>
                </table>

                @if($pengajuanKonversiKegiatan->catatan_mahasiswa)
                <hr>
                <p class="text-muted mb-1">Catatan Mahasiswa:</p>
                <p class="mb-0">{{ $pengajuanKonversiKegiatan->catatan_mahasiswa }}</p>
                @endif

                @if($pengajuanKonversiKegiatan->catatan_admin)
                <hr>
                <p class="text-muted mb-1">Catatan Admin:</p>
                <p class="mb-0">{{ $pengajuanKonversiKegiatan->catatan_admin }}</p>
                @endif

                @if($pengajuanKonversiKegiatan->diprosesOleh)
                <hr>
                <p class="text-muted mb-1">Diproses Oleh:</p>
                <p class="mb-0">{{ $pengajuanKonversiKegiatan->diprosesOleh->name }}</p>
                <small class="text-muted">{{ $pengajuanKonversiKegiatan->tanggal_diproses?->format('d/m/Y H:i') }}</small>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.konversi-kegiatan.index') }}" class="btn btn-secondary w-100">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Daftar Kegiatan -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Daftar Kegiatan untuk Diverifikasi</h6>
                @if($pengajuanKonversiKegiatan->canProcess())
                <span class="badge bg-warning text-dark">Perlu Verifikasi</span>
                @endif
            </div>
            <div class="card-body">
                @if($pengajuanKonversiKegiatan->details->isEmpty())
                <div class="text-center py-4">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-2">Tidak ada kegiatan dalam pengajuan ini</p>
                </div>
                @else
                @foreach($pengajuanKonversiKegiatan->details as $detail)
                <div class="card mb-3 border-{{ $detail->status_detail == 'Disetujui' ? 'success' : ($detail->status_detail == 'Ditolak' ? 'danger' : 'secondary') }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            {!! $detail->jenis_kegiatan_badge !!}
                            <strong class="ms-2">{{ $detail->nama_kegiatan }}</strong>
                        </div>
                        <div>
                            {!! $detail->status_badge !!}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" width="40%">Penyelenggara</td>
                                        <td>{{ $detail->penyelenggara }}</td>
                                    </tr>
                                    @if($detail->no_sertifikat)
                                    <tr>
                                        <td class="text-muted">No. Sertifikat</td>
                                        <td>{{ $detail->no_sertifikat }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="text-muted">Tanggal</td>
                                        <td>
                                            {{ $detail->tanggal_mulai->format('d/m/Y') }}
                                            @if($detail->tanggal_selesai)
                                            - {{ $detail->tanggal_selesai->format('d/m/Y') }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if($detail->durasi_jam)
                                    <tr>
                                        <td class="text-muted">Durasi</td>
                                        <td>{{ $detail->durasi_jam }} jam</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" width="40%">MK Tujuan</td>
                                        <td>
                                            @if($detail->mataKuliah)
                                            <strong>{{ $detail->mataKuliah->kode }}</strong> - {{ $detail->mataKuliah->nama }}
                                            <br><small class="text-muted">({{ $detail->mataKuliah->sks }} SKS)</small>
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                    @if($detail->sks_diakui)
                                    <tr>
                                        <td class="text-muted">SKS Diakui</td>
                                        <td><span class="badge bg-success">{{ $detail->sks_diakui }} SKS</span></td>
                                    </tr>
                                    @endif
                                    @if($detail->nilai_huruf)
                                    <tr>
                                        <td class="text-muted">Nilai</td>
                                        <td><span class="badge bg-primary">{{ $detail->nilai_huruf }} ({{ $detail->nilai_angka }})</span></td>
                                    </tr>
                                    @endif
                                    @if($detail->bukti_dokumen)
                                    <tr>
                                        <td class="text-muted">Bukti</td>
                                        <td>
                                            <a href="{{ route('konversi-kegiatan.download-bukti', $detail) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="bi bi-download me-1"></i>Download Bukti
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($detail->deskripsi_kegiatan)
                        <hr class="my-2">
                        <p class="text-muted mb-1">Deskripsi:</p>
                        <p class="mb-0">{{ $detail->deskripsi_kegiatan }}</p>
                        @endif

                        @if($detail->catatan_verifikasi)
                        <hr class="my-2">
                        <p class="text-muted mb-1">Catatan Verifikasi:</p>
                        <p class="mb-0">{{ $detail->catatan_verifikasi }}</p>
                        @endif
                    </div>
                    @if($pengajuanKonversiKegiatan->canProcess())
                    <div class="card-footer bg-light">
                        <form action="{{ route('admin.konversi-kegiatan.verify-detail', $detail) }}" method="POST" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status_detail" class="form-select form-select-sm" required>
                                    <option value="">Pilih...</option>
                                    <option value="Disetujui" {{ $detail->status_detail == 'Disetujui' ? 'selected' : '' }}>Setujui</option>
                                    <option value="Ditolak" {{ $detail->status_detail == 'Ditolak' ? 'selected' : '' }}>Tolak</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">SKS Diakui</label>
                                <input type="number" name="sks_diakui" class="form-control form-control-sm" min="0" max="{{ $detail->mataKuliah?->sks ?? 6 }}" value="{{ $detail->sks_diakui ?? $detail->mataKuliah?->sks }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Nilai Huruf</label>
                                <select name="nilai_huruf" class="form-select form-select-sm">
                                    <option value="">-</option>
                                    <option value="A" {{ $detail->nilai_huruf == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="AB" {{ $detail->nilai_huruf == 'AB' ? 'selected' : '' }}>AB</option>
                                    <option value="B" {{ $detail->nilai_huruf == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="BC" {{ $detail->nilai_huruf == 'BC' ? 'selected' : '' }}>BC</option>
                                    <option value="C" {{ $detail->nilai_huruf == 'C' ? 'selected' : '' }}>C</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Catatan</label>
                                <input type="text" name="catatan_verifikasi" class="form-control form-control-sm" value="{{ $detail->catatan_verifikasi }}" placeholder="Catatan verifikasi">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                    <i class="bi bi-check-lg me-1"></i>Update
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
                @endforeach
                @endif
            </div>
        </div>

        <!-- Finalize -->
        @if($pengajuanKonversiKegiatan->canProcess())
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-check2-square me-2"></i>Finalisasi Pengajuan</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.konversi-kegiatan.finalize', $pengajuanKonversiKegiatan) }}" method="POST" onsubmit="return confirm('Yakin ingin memfinalisasi pengajuan ini?')">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Akhir <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Disetujui">Setujui Pengajuan</option>
                                <option value="Ditolak">Tolak Pengajuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catatan Admin</label>
                            <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Catatan untuk mahasiswa">{{ $pengajuanKonversiKegiatan->catatan_admin }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Finalisasi Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @elseif($pengajuanKonversiKegiatan->status === 'Disetujui')
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Pengajuan Sudah Disetujui</strong>
            <br>Total SKS yang diakui: <strong>{{ $pengajuanKonversiKegiatan->total_sks }} SKS</strong>
        </div>
        @elseif($pengajuanKonversiKegiatan->status === 'Ditolak')
        <div class="alert alert-danger">
            <i class="bi bi-x-circle me-2"></i>
            <strong>Pengajuan Ditolak</strong>
            @if($pengajuanKonversiKegiatan->catatan_admin)
            <br>Alasan: {{ $pengajuanKonversiKegiatan->catatan_admin }}
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
