@extends('layouts.app')

@section('title', 'Detail Pengajuan Konversi Kegiatan')

@section('content')
<div class="page-title">
    <h4>Detail Pengajuan Konversi Kegiatan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('konversi-kegiatan.index') }}">Konversi Kegiatan</a></li>
            <li class="breadcrumb-item active">{{ $pengajuanKonversiKegiatan->no_pengajuan }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Info Pengajuan -->
    <div class="col-md-4">
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
                        <td class="text-muted">Mahasiswa</td>
                        <td>{{ $pengajuanKonversiKegiatan->mahasiswa->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIM</td>
                        <td>{{ $pengajuanKonversiKegiatan->mahasiswa->nim }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Studi</td>
                        <td>{{ $pengajuanKonversiKegiatan->mahasiswa->programStudi->nama ?? '-' }}</td>
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
            @if($pengajuanKonversiKegiatan->canSubmit())
            <div class="card-footer">
                <form action="{{ route('konversi-kegiatan.submit', $pengajuanKonversiKegiatan) }}" method="POST" onsubmit="return confirm('Yakin ingin mengajukan? Setelah diajukan, Anda tidak dapat mengubah data kegiatan.')">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-send me-2"></i>Ajukan Pengajuan
                    </button>
                </form>
            </div>
            @endif
        </div>

        <a href="{{ route('konversi-kegiatan.index') }}" class="btn btn-secondary w-100">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Daftar Kegiatan -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Daftar Kegiatan</h6>
                @if($pengajuanKonversiKegiatan->canEdit())
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKegiatan">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Kegiatan
                </button>
                @endif
            </div>
            <div class="card-body">
                @if($pengajuanKonversiKegiatan->details->isEmpty())
                <div class="text-center py-4">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-2">Belum ada kegiatan ditambahkan</p>
                    @if($pengajuanKonversiKegiatan->canEdit())
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKegiatan">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Kegiatan Pertama
                    </button>
                    @endif
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kegiatan</th>
                                <th>Penyelenggara</th>
                                <th>Tanggal</th>
                                <th>MK Tujuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengajuanKonversiKegiatan->details as $detail)
                            <tr>
                                <td>
                                    {!! $detail->jenis_kegiatan_badge !!}
                                    <br>
                                    <strong>{{ $detail->nama_kegiatan }}</strong>
                                    @if($detail->no_sertifikat)
                                    <br><small class="text-muted">No: {{ $detail->no_sertifikat }}</small>
                                    @endif
                                </td>
                                <td>{{ $detail->penyelenggara }}</td>
                                <td>
                                    {{ $detail->tanggal_mulai->format('d/m/Y') }}
                                    @if($detail->tanggal_selesai)
                                    <br><small class="text-muted">s/d {{ $detail->tanggal_selesai->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($detail->mataKuliah)
                                    <strong>{{ $detail->mataKuliah->kode }}</strong>
                                    <br><small>{{ $detail->mataKuliah->nama }}</small>
                                    @if($detail->sks_diakui)
                                    <br><span class="badge bg-info">{{ $detail->sks_diakui }} SKS</span>
                                    @endif
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {!! $detail->status_badge !!}
                                    @if($detail->nilai_huruf)
                                    <br><span class="badge bg-primary">{{ $detail->nilai_huruf }}</span>
                                    @endif
                                    @if($detail->catatan_verifikasi)
                                    <br><small class="text-muted">{{ $detail->catatan_verifikasi }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $detail->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($detail->bukti_dokumen)
                                        <a href="{{ route('konversi-kegiatan.download-bukti', $detail) }}" class="btn btn-sm btn-secondary" target="_blank">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @endif
                                        @if($pengajuanKonversiKegiatan->canEdit())
                                        <form action="{{ route('konversi-kegiatan.destroy-detail', [$pengajuanKonversiKegiatan, $detail]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus kegiatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        @if($pengajuanKonversiKegiatan->status === 'Disetujui' && $pengajuanKonversiKegiatan->total_sks > 0)
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Pengajuan Disetujui!</strong>
            <br>Total SKS yang diakui: <strong>{{ $pengajuanKonversiKegiatan->total_sks }} SKS</strong>
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Kegiatan -->
@if($pengajuanKonversiKegiatan->canEdit())
<div class="modal fade" id="modalTambahKegiatan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('konversi-kegiatan.store-detail', $pengajuanKonversiKegiatan) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Tambah Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
                            <select name="jenis_kegiatan" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisKegiatan as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control" required placeholder="Contoh: Sertifikasi CCNA">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                            <input type="text" name="penyelenggara" class="form-control" required placeholder="Contoh: Cisco Networking Academy">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Sertifikat</label>
                            <input type="text" name="no_sertifikat" class="form-control" placeholder="Nomor sertifikat (jika ada)">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Durasi (Jam)</label>
                            <input type="number" name="durasi_jam" class="form-control" min="1" placeholder="Contoh: 40">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Kegiatan</label>
                        <textarea name="deskripsi_kegiatan" class="form-control" rows="2" placeholder="Jelaskan kegiatan yang dilakukan"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bukti Dokumen</label>
                        <input type="file" name="bukti_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG. Maks: 5MB</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Mata Kuliah Tujuan Konversi <span class="text-danger">*</span></label>
                        <select name="mata_kuliah_id" class="form-select" required>
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->sks }} SKS, Smt {{ $mk->semester }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih mata kuliah yang relevan dengan kegiatan Anda</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Kegiatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Detail Kegiatan -->
@foreach($pengajuanKonversiKegiatan->details as $detail)
<div class="modal fade" id="modalDetail{{ $detail->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="35%">Jenis Kegiatan</td>
                        <td>{!! $detail->jenis_kegiatan_badge !!}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Kegiatan</td>
                        <td><strong>{{ $detail->nama_kegiatan }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Penyelenggara</td>
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
                    @if($detail->deskripsi_kegiatan)
                    <tr>
                        <td class="text-muted">Deskripsi</td>
                        <td>{{ $detail->deskripsi_kegiatan }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">MK Tujuan</td>
                        <td>
                            @if($detail->mataKuliah)
                            {{ $detail->mataKuliah->kode }} - {{ $detail->mataKuliah->nama }}
                            <br><small class="text-muted">({{ $detail->mataKuliah->sks }} SKS)</small>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>{!! $detail->status_badge !!}</td>
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
                    @if($detail->catatan_verifikasi)
                    <tr>
                        <td class="text-muted">Catatan Verifikasi</td>
                        <td>{{ $detail->catatan_verifikasi }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="modal-footer">
                @if($detail->bukti_dokumen)
                <a href="{{ route('konversi-kegiatan.download-bukti', $detail) }}" class="btn btn-secondary" target="_blank">
                    <i class="bi bi-download me-2"></i>Download Bukti
                </a>
                @endif
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
